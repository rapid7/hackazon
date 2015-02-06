<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.01.2015
 * Time: 12:32
 */


namespace VulnModule\Config;


class ContextMetadata 
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type = Context::TYPE_STANDARD;

    /**
     * @var string
     */
    protected $storageRole = Context::STORAGE_ROLE_CHILD;

    /**
     * @var string string
     */
    protected $technology = Context::TECH_GENERIC;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var mixed|array
     */
    protected $routeParams = [];

    /**
     * @var string
     */
    protected $mappedTo;

    /**
     * @var string
     */
    protected $description;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getStorageRole()
    {
        return $this->storageRole;
    }

    /**
     * @param string $storageRole
     */
    public function setStorageRole($storageRole)
    {
        $this->storageRole = $storageRole;
    }

    /**
     * @return string
     */
    public function getTechnology()
    {
        return $this->technology;
    }

    /**
     * @param string $technology
     */
    public function setTechnology($technology)
    {
        $this->technology = $technology;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return string
     */
    public function getMappedTo()
    {
        return $this->mappedTo;
    }

    /**
     * @param string $mappedTo
     */
    public function setMappedTo($mappedTo)
    {
        $this->mappedTo = $mappedTo;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return array|mixed
     */
    public function getRouteParams()
    {
        return $this->routeParams ;
    }

    /**
     * @param array|mixed $routeParams
     */
    public function setRouteParams($routeParams)
    {
        $this->routeParams = is_array($routeParams) ? $routeParams : [];
    }
}