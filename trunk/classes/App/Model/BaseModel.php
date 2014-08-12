<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 31.07.2014
 * Time: 13:31
 */


namespace App\Model;


use App\Pixie;
use PHPixie\ORM\Model;

/**
 * Class BaseModel.
 * Extends PHPixie model with helper methods.
 *
 * @property Pixie $pixie
 * @package App\Model
 */
class BaseModel extends Model
{
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
} 