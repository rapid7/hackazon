<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 27.01.2015
 * Time: 18:01
 */


namespace VulnModule\Config\URL;


use VulnModule\Config\Context;

class URL
{
    protected $segments = [];

    protected $service;

    protected $method;

    protected $technology = Context::TECH_GENERIC;

    public function addSegment($segment)
    {
        if ($segment) {
            $this->segments[] = $segment;
        }
    }

    public function __toString()
    {
        $url = preg_replace('#^\/+#', '/', preg_replace('#\/+$#', '', implode('/', $this->segments)));
        if (!in_array($this->technology, [Context::TECH_GENERIC, Context::TECH_WEB])) {
            $parts = array_filter([$this->service, $this->method]);
            $url = count($parts) ? $url . ' [ ' . implode('::', $parts) . ' ]' : $url;
        }
        return $url;
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        if (in_array($this->technology, [Context::TECH_GENERIC, Context::TECH_WEB])) {
            $this->segments[] = $service;
        }
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        if (in_array($this->technology, [Context::TECH_GENERIC, Context::TECH_WEB])) {
            $this->segments[] = $method;
        }
        $this->method = $method;
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
}