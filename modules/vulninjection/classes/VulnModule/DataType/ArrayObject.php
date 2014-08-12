<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 07.08.2014
 * Time: 19:57
 */


namespace VulnModule\DataType;


/**
 * Class ArrayObject.
 * Helps to implement recursive value checking.
 *
 * @package VulnModule\DataType
 */
class ArrayObject implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * One-level-depth array of values and other ArrayObjects
     * @var array
     */
    protected $data;

    /**
     * @var string Name of the offset
     */
    protected $offsetName;

    /**
     * @var mixed
     */
    protected $topLevelValue;

    /**
     * @var ArrayObject
     */
    protected $parent;

    /**
     * Counter of the iterator.
     * @var int
     */
    protected $counter = 0;

    /**
     * Creates ArrayObject.
     * @param $offsetName
     * @param array $data
     * @param null $topLevelValue
     * @param null $parent
     * @throws \InvalidArgumentException
     */
    public function __construct($offsetName, $data = [], $topLevelValue = null, $parent = null)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('');
        }

        $this->offsetName = $offsetName;
        $this->topLevelValue = $topLevelValue;
        $this->data = $this->buildArrayObject($data, $topLevelValue);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->data);
        $this->counter++;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->counter < count($this->data);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->data);
        $this->counter = 0;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        if (array_key_exists($offset, $this->data)) {
            return $this->data[$offset];

        }

        if ($this->topLevelValue instanceof ArrayObject) {
            $this->topLevelValue[$offset];
        }

        return $this->topLevelValue;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = is_array($value) ? new ArrayObject($value) : $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @param array $data
     * @internal param null $parent
     * @return array
     */
    private function buildArrayObject(array $data = [])
    {
        $result = [];

        foreach ($data as $name => $item) {
            if (is_array($item)) {
                $result[$name] = new ArrayObject($item);

            } else {
                $result[$name] = $item;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getOffsetName()
    {
        return $this->offsetName;
    }

    /**
     * @param string $offsetName
     * @return $this
     */
    public function setOffsetName($offsetName)
    {
        $this->offsetName = $offsetName;

        return $this;
    }
}