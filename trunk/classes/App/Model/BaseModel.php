<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 31.07.2014
 * Time: 13:31
 */


namespace App\Model;


use App\Pixie;
use PHPixie\DB\PDOV\Connection;
use PHPixie\DB\PDOV\Query;
use PHPixie\ORM\Model;
use App\Pixifier;
use VulnModule\VulnerableField;

/**
 * Class BaseModel.
 * Extends PHPixie model with helper methods.
 *
 * @property Pixie $pixie
 * @property Connection $conn
 * @package App\Model
 */
class BaseModel extends Model
{
    /**
     * @var array Cached field names of the model.
     */
    protected static $fieldNames = [];

    /**
     * @var array Wrapped vulnerable values
     */
    protected $wrappers = [];

    protected $returnWrappers = false;

	/**
	 * Checks if the collection contains strictly given instance of model.
	 *
	 * @param Model $model
	 * @return bool
	 */
	public function contains(Model $model = null)
	{
		if ($model === null || !$model->loaded() || !$this->loaded()) {
			return false;
		}

		foreach ($this as $item) {
			if ($model === $item) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if the collection contains given instance of model by ID.
	 * It is made, because PHPixie doesn't contain Unit of Work feature.
	 *
	 * @param Model $model
	 * @return bool
	 */
	public function containsById(Model $model)
	{
		if ($model === null || !$model->loaded() || !$this->loaded()) {
			return false;
		}

		if (__CLASS__ !== get_class($model)) {
			return false;
		}

		$idField = $this->id_field;

		foreach ($this as $item) {
			if ($model->$idField == $item->$idField) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Filters already loaded collection of object, not DB query 'Where' clause.
	 *
	 * @param array $params Model props to filter
	 * @param array $options
	 * @return array
	 */
	public function filterBy(array $params = array(), array $options = array())
	{
		$result = array();

		if (!$this->loaded()) {
			return $result;
		}

		$options = array_merge_recursive(array(
			'limit' => null 	// Maximum number of result elements
		), $options);

		$props = array_keys($params);

		$total = 0;
		$limit = is_numeric($options['limit']) && $options['limit'] > 0
				? (int) $options['limit'] : PHP_INT_MAX;

		foreach ($this as $item) {
			$matches = true;
			foreach ($props as $prop) {
				if ($item->$prop != $params[$prop]) {
					$matches = false;
					break;
				}
			}

			if ($matches) {
				$result[] = $item;
				$total++;
				if ($total >= $limit) {
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * Filters like filterBy(), but returns single result.
	 *
	 * @see filterBy
	 * @param array $params
	 * @param array $options
	 * @return null|Model
	 */
	public function filterOneBy(array $params = array(), array $options = array())
	{
		$options['limit'] = 1;
		$result = $this->filterBy($params, $options);
		return $result[0];
	}

    public function getFields(array $fields)
    {
        if (!$this->loaded()) {
            //return [];
        }

        $result = [];
        foreach ($fields as $field) {
            if (isset($this->$field)) {
                $result[$field] = $this->$field;
            }
        }

        return $result;
    }

    public function setIsLoaded($isLoaded)
    {
        $this->_loaded = (bool) $isLoaded;
    }

    public function refresh()
    {
        if (!$this->loaded()) {
            return $this;
        }

        $row = (array) $this->conn->query('select')
            ->table($this->table)
            ->where($this->id_field, $this->id())->execute()->current();
        $this->values($row, true);
        return $this;
    }

    public function filterValues($values, $fields = [])
    {
        $result = [];
        $columns = $this->getFieldNames();
        $fieldCount = count($fields);
        $meta = $this->getModelMeta();

        foreach ($values as $key => $value) {
            if ((!$fieldCount || $fieldCount && in_array($key, $fields))
                && in_array($key, $columns)
            ) {
                $result[$key] = $value;

                if ($meta[$key]['is_key'] && !($value instanceof VulnerableField ? $value->raw() : $value)) {
                    $result[$key] = null;
                }
            }
        }
        return $result;
    }

    public function getFieldNames()
    {
        $className = get_class($this);

        if (!self::$fieldNames[$className] && self::$fieldNames[$className] !== false) {
            self::$fieldNames[$className] = $this->columns();
            if (!self::$fieldNames[$className]) {
                self::$fieldNames[$className] = false;
            }
        }

        return self::$fieldNames[$className] === false ? [] : self::$fieldNames[$className];
    }

    public function values($row, $set_loaded = false)
    {
        parent::values($row, $set_loaded);
        foreach ($row as $field => $value) {
            if ($value === null) {
                $this->_row[$field] = $value;
            }
        }
        return $this;
    }

    public function getModelMeta()
    {
        return [];
    }

    public function __sleep()
    {
        $vars = get_object_vars($this);
        unset($vars['query']);
        unset($vars['conn']);
        unset($vars['pixie']);
        unset($vars['cached']);

        return array_keys($vars);
    }

    public function __wakeup()
    {
        $pixie = Pixifier::getInstance()->getPixie();
        $this->pixie = $pixie;
        $this->conn = $pixie->db->get($this->connection);
        $this->query = $this->conn->query('select');
        $this->query->table($this->table);
        $this->cached = [];
        if ($this->_row[$this->id_field]) {
            $this->_loaded = true;
        } else {
            $this->_loaded = false;
        }
       // $this->prepare_relations();
    }

    /**
     * @inheritdoc
     */
    public function __get($column)
    {
        if ($this->returnWrappers && in_array($column, $this->wrappers)) {
            return $this->wrappers[$column];
        }

        return parent::__get($column);
    }

    /**
     * @inheritdoc
     */
    public function __set($column, $val)
    {
        $relations = array_merge($this->has_one, $this->has_many, $this->belongs_to);

        if (array_key_exists($column, $relations)) {
            $this->add($column, $val);

        } else {
            if ($val instanceof VulnerableField) {
                $this->wrappers[$column] = $val;
                $this->_row[$column] = $val->raw();

            } else {
                $this->_row[$column] = $val;
                unset($this->wrappers[$column]);
            }
        }

        $this->cached = [];
    }

    /**
     * @param $column
     * @return mixed
     */
    public function getWrapperOrValue($column)
    {
        return array_key_exists($column, $this->wrappers) ? $this->wrappers[$column] : $this->$column;
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        if ($this->loaded()) {
            $query = $this->conn->query('update')
                ->table($this->table)
                ->where($this->id_field, $this->_row[$this->id_field]);

        } else {
            $query = $this->conn->query('insert')
                ->table($this->table);
        }

        $data = [];
        foreach ($this->_row as $key => $value) {
            if (isset($this->wrappers[$key])) {
                $data[$key] = $this->wrappers[$key];
            } else {
                $data[$key] = $value;
            }
        }

        /** @var Query $query */
        $query->data($data);
        $query->execute();

        if ($this->loaded()) {
            $id = $this->_row[$this->id_field];

        } else {
            $id = $this->conn->insert_id();
        }

        $row = (array)$this->conn->query('select')
            ->table($this->table)
            ->where($this->id_field, $id)->execute()->current();
        $this->values($row, true);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isReturnWrappers()
    {
        return $this->returnWrappers;
    }

    /**
     * @param boolean $returnWrappers
     */
    public function setReturnWrappers($returnWrappers)
    {
        $this->returnWrappers = !!$returnWrappers;
    }

    public function as_array()
    {
        return array_merge($this->_row, $this->wrappers);
    }
}