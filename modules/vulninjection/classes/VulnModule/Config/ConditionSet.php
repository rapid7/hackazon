<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 26.11.2014
 * Time: 12:24
 */


namespace VulnModule\Config;


use VulnModule\DataType\ArrayObject;
use VulnModule\Config\Condition;

/**
 * Is an ordered container of conditions to match the given field name
 * @package VulnModule\Rule
 */
class ConditionSet implements \ArrayAccess
{
    /**
     * @var array|ArrayObject<Condition>|Condition[]
     */
    protected $conditions;

    function __construct($conditions = [])
    {
        $this->conditions = new ArrayObject();
        $this->setConditions($conditions);
    }

    /**
     * @return array|ArrayObject<ICondition>|ICondition[]
     */
    public function getConditions()
    {
        return $this->conditions->getArrayCopy();
    }

    /**
     * @param array|ArrayObject $conditions
     */
    public function setConditions($conditions)
    {
        if (!is_array($conditions) && !($conditions instanceof ArrayObject)) {
            throw new \InvalidArgumentException();
        }

        $this->conditions = new ArrayObject();

        foreach ($conditions as $condition) {
            $this->addCondition($condition);
        }
    }

    /**
     * @param ICondition $condition
     */
    public function addCondition(ICondition $condition)
    {
        $this->conditions[$condition->getName()] = $condition;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        $key = $offset instanceof Condition ? $offset->getName() : $offset;
        return $this->conditions->containsKey($key);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed|Condition Can return all value types.
     */
    public function offsetGet($offset)
    {
        $key = $offset instanceof Condition ? $offset->getName() : $offset;
        return $this->conditions[$key];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!($value instanceof Condition)) {
            throw new \InvalidArgumentException("Value must be an instance of class Condition");
        }

        $this->addCondition($value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $key = $offset instanceof Condition ? $offset->getName() : $offset;
        unset($this->conditions[$key]);
    }

    public function count()
    {
        return count($this->conditions);
    }
}