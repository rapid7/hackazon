<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 23.12.2014
 * Time: 12:45
 */


namespace VulnModule\Config;


use App\Core\Request;

class Condition implements ICondition
{
    const IS_ACTIVE = true;

    public function toArray()
    {
        return [];
    }

    public function getName()
    {
        return preg_replace('/.*\\\\/', '', get_class($this));
    }

    public function setName($name)
    {
    }

    public function match(Request $request)
    {
        return true;
    }

    public function fillFromArray($data)
    {
        if (!is_array($data)) {
            if (method_exists($this, 'setValue')) {
                $this->setValue($data);
            }
            return;
        }

        if (array_key_exists('value', $data)) {
            if (method_exists($this, 'setValue')) {
                $this->setValue($data['value']);
            }
        }
    }

    function __toString()
    {
        return 'Condition "' . $this->getName() . '"';
    }

    /**
     * @param Condition $condition
     * @return bool
     */
    public function equalsTo($condition)
    {
        if ($this === $condition) {
            return true;
        }

        $className = __CLASS__;
        if (!($condition instanceof $className)) {
            return false;
        }

        return $this->getName() == $condition->getName();
    }
}