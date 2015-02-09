<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 01.12.2014
 * Time: 10:33
 */


namespace VulnModule\Config;

/**
 * Describes request field and its source
 * @package VulnModule\Config
 */
class FieldDescriptor 
{
    const SOURCE_ANY = 'any';
    const SOURCE_QUERY = 'query';
    const SOURCE_PARAM = 'param';
    const SOURCE_BODY = 'body';
    const SOURCE_HEADER = 'header';
    const SOURCE_COOKIE = 'cookie';

    protected static $sources = [
        self::SOURCE_ANY,
        self::SOURCE_QUERY,
        self::SOURCE_PARAM,
        self::SOURCE_BODY,
        self::SOURCE_HEADER,
        self::SOURCE_COOKIE,
    ];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $source;

    function __construct($name, $source)
    {
        $this->_setName($name);
        $this->_setSource($source);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    protected function _setSource($source)
    {
        if (!in_array($source, self::$sources, true)) {
            throw new \InvalidArgumentException("Field source '$source' is not valid. Valid ones are: "
                . implode(', ', self::$sources));
        }

        $this->source = $source;
    }

    /**
     * @param string $name
     */
    protected function _setName($name)
    {
        if (!$name || !is_string($name)) {
            throw new \InvalidArgumentException("Field name must be a string. Field provided: $name");
        }
        $this->name = $name;
    }

    public function conformsTo($descriptor)
    {
        if (!($descriptor instanceof FieldDescriptor) || $descriptor === null) {
            return false;
        }
        return $this->name == $descriptor->getName()
            && ($this->source == self::SOURCE_ANY
                || $descriptor->source == self::SOURCE_ANY
                || $this->source == $descriptor->getSource());
    }

    public static function getSources()
    {
        return self::$sources;
    }
}