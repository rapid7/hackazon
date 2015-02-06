<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 26.11.2014
 * Time: 13:00
 */


namespace VulnModule\Config;


class ConditionFactory
{
    /**
     * @var Condition[]
     */
     protected $cache;

    protected function __construct()
    {
        $this->cache = [];
    }

    public static function instance()
    {
        static $instance;
        if (!$instance) {
            $instance = new self;
        }

        return $instance;
    }

    /**
     * @return array
     */
    public static function getAllConditionNames()
    {
        static $names;
        if (!$names) {
            $names = [];
            foreach (new \DirectoryIterator(__DIR__ . '/Condition') as $dir) {
                if ($dir->isDot() || $dir->getExtension() != 'php') {
                    continue;
                }
                $names[] = basename($dir->getFilename(), '.php');
            }
            sort($names);
        }

        return $names;
    }

    public static function exists($name)
    {
        return in_array($name, self::getAllConditionNames());
    }

    /**
     * @param $name
     * @return Condition
     */
    public function create($name)
    {
        return clone $this->getCondition($name);
    }

    /**
     * @param $name
     * @return Condition
     */
    protected function getCondition($name) {
        if (!array_key_exists($name, $this->cache)) {
            $className = __NAMESPACE__ . '\\Condition\\' . $name;
            if (!class_exists($className)) {
                throw new \InvalidArgumentException("Class '$name' doesn't exist.");
            }

            $condition = new $className;
            if (!($condition instanceof Condition)) {
                throw new \RuntimeException("Class '$name' is not a Condition class.'");
            }

            $this->cache[$name] = $condition;
        }

        return $this->cache[$name];
    }
}