<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 17.12.2014
 * Time: 17:13
 */


namespace App\Helpers;


use App\DependencyInjection\Container;
use App\Pixie;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ScopeInterface;

class ContainerHelper implements ContainerInterface
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var Container
     */
    protected $container;

    function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
        $this->container = $pixie->container;
    }

    /**
     * Sets a service.
     *
     * @param string $id The service identifier
     * @param object $service The service instance
     * @param string $scope The scope of the service
     *
     * @api
     */
    public function set($id, $service, $scope = self::SCOPE_CONTAINER)
    {
        $this->container[$id] = $service;
    }

    /**
     * Gets a service.
     *
     * @param string $id The service identifier
     * @param int $invalidBehavior The behavior when the service does not exist
     *
     * @return object The associated service
     *
     * @throws InvalidArgumentException          if the service is not defined
     * @throws ServiceCircularReferenceException When a circular reference is detected
     * @throws ServiceNotFoundException          When the service is not defined
     *
     * @see Reference
     *
     * @api
     */
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if (isset($this->container[$id])) {
            return $this->container[$id];
        }

        if ($invalidBehavior === self::EXCEPTION_ON_INVALID_REFERENCE) {
            throw new \Psr\Log\InvalidArgumentException("Incorrect service id: $id");
        } else {
            return null;
        }
    }

    /**
     * Returns true if the given service is defined.
     *
     * @param string $id The service identifier
     *
     * @return bool true if the service is defined, false otherwise
     *
     * @api
     */
    public function has($id)
    {
        return isset($this->container[$id]);
    }

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     *
     * @api
     */
    public function getParameter($name)
    {
        return $this->container[$name];
    }

    /**
     * Checks if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return bool The presence of parameter in container
     *
     * @api
     */
    public function hasParameter($name)
    {
        return isset($this->container[$name]);
    }

    /**
     * Sets a parameter.
     *
     * @param string $name The parameter name
     * @param mixed $value The parameter value
     *
     * @api
     */
    public function setParameter($name, $value)
    {
        $this->container[$name] = $value;
    }

    /**
     * Enters the given scope
     *
     * @param string $name
     *
     * @api
     */
    public function enterScope($name)
    {
        // TODO: Implement enterScope() method.
    }

    /**
     * Leaves the current scope, and re-enters the parent scope
     *
     * @param string $name
     *
     * @api
     */
    public function leaveScope($name)
    {
        // TODO: Implement leaveScope() method.
    }

    /**
     * Adds a scope to the container
     *
     * @param ScopeInterface $scope
     *
     * @api
     */
    public function addScope(ScopeInterface $scope)
    {
        // TODO: Implement addScope() method.
    }

    /**
     * Whether this container has the given scope
     *
     * @param string $name
     *
     * @return bool
     *
     * @api
     */
    public function hasScope($name)
    {
        // TODO: Implement hasScope() method.
    }

    /**
     * Determines whether the given scope is currently active.
     *
     * It does however not check if the scope actually exists.
     *
     * @param string $name
     *
     * @return bool
     *
     * @api
     */
    public function isScopeActive($name)
    {
        // TODO: Implement isScopeActive() method.
    }
}