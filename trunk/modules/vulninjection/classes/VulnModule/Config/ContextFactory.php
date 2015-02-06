<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 26.11.2014
 * Time: 13:00
 */


namespace VulnModule\Config;

/**
 * Class ContextFactory
 * @package VulnModule\Config
 */
class ContextFactory
{
    /**
     * @var Context[]
     */
     protected $cache;

    protected function __construct()
    {
        $this->cache = [];
    }

    /**
     * @return ContextFactory
     */
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
    public static function getAllContextNames()
    {
        static $names;
        if (!$names) {
            $names = [];
            foreach (new \DirectoryIterator(__DIR__ . '/Context') as $dir) {
                if ($dir->isDot() || $dir->getExtension() != 'php') {
                    continue;
                }
                $names[] = basename($dir->getFilename(), '.php');
            }
            sort($names);
        }

        return $names;
    }

    /**
     * @param $name
     * @return bool
     */
    public static function exists($name)
    {
        return in_array($name, self::getAllContextNames());
    }

    /**
     * @param $name
     * @return Context
     */
    public function create($name)
    {
        return clone $this->getContext($name);
    }

    /**
     * @param $name
     * @return Context
     */
    protected function getContext($name) {
        if (!array_key_exists($name, $this->cache)) {
            $className = __NAMESPACE__ . '\\Context\\' . $name;
            if (!class_exists($className)) {
                throw new \InvalidArgumentException("Class '$name' doesn't exist.");
            }

            $context = new $className;
            if (!($context instanceof Context)) {
                throw new \RuntimeException("Class '$name' is not a Condition class.'");
            }

            $this->cache[$name] = $context;
        }

        return $this->cache[$name];
    }
}