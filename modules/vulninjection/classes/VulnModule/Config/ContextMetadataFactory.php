<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.01.2015
 * Time: 12:33
 */


namespace VulnModule\Config;


use VulnModule\AnnotationReader;
use VulnModule\Config\Annotations\Context as ContextAnnotation;
use VulnModule\Config\Annotations\Description;
use VulnModule\Config\Annotations\Route;

class ContextMetadataFactory
{
    /**
     * @var AnnotationReader
     */
    protected $reader;

    /**
     * @var array
     */
    protected $namespaces;

    protected $cache = [];

    /**
     * @param AnnotationReader $reader
     */
    function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
        $this->namespaces = [];
    }

    public function addNamespace($ns, $technology = Context::TECH_GENERIC)
    {
        if (!is_array($this->namespaces[$technology])) {
            $this->namespaces[$technology] = [];
        }

        $this->namespaces[$technology][] = preg_replace('#\\\\+$#', '\\', $ns . '\\');
    }

    public function getMetadata($controllerName, $actionName = null, $technology = Context::TECH_GENERIC)
    {
        if (!$this->reader) {
            return null;
        }

        $key = $controllerName . '::' . $actionName;
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $metadata = null;

        if (!is_array($this->namespaces[$technology])) {
            $this->namespaces[$technology] = [];
        }

        $className = $this->getControllerClassName($controllerName, $technology);
        if ($technology == Context::TECH_WEB && !$className) {
            $className = $this->getControllerClassName($controllerName, Context::TECH_GENERIC);
        }

        $classExists = class_exists($className);
        if (!$classExists && $technology == Context::TECH_REST) {
            $className = 'App\\Rest\\Controller';
            $classExists = true;
        }

        if ($classExists) {
            $metadata = new ContextMetadata();

            if (!$actionName) {
                $metadata->setMappedTo($controllerName);
            }

            /** @var Description $classDescr */
            $classDescr = $actionName ? null : $this->reader->getClassAnnotation($className, 'VulnModule\\Config\\Annotations\\Description');
            /** @var Route $classRouter */
            $classRouter = $this->reader->getClassAnnotation($className, 'VulnModule\\Config\\Annotations\\Route');
            /** @var ContextAnnotation $classContext */
            $classContext = $actionName ? null : $this->reader->getClassAnnotation($className, 'VulnModule\\Config\\Annotations\\Context');

            if ($classDescr) {
                if ($classDescr->description) {
                    $metadata->setDescription($classDescr->description);
                }
            }

            if ($classRouter) {
                if ($classRouter->name) {
                    $metadata->setRoute($classRouter->name);
                }
                $metadata->setRouteParams($classRouter->params);
            }

            if ($classContext) {
                $metadata->setName($classContext->name);
            }

            if (!$actionName) {
                $metadata->setType(Context::TYPE_CONTROLLER);
            }

        } else {
            $this->cache[$key] = null;
            return null;
        }

        if (!$actionName) {
            $this->cache[$key] = $metadata;
            return $metadata;
        }

        $metadata->setType(Context::TYPE_ACTION);
        $metadata->setMappedTo($actionName);
        $methodName = $this->getMethodNameByActionName($actionName, $technology);

        /** @var Description $methodDescr */
        $methodDescr = $this->reader->getMethodAnnotation($className, $methodName, 'VulnModule\\Config\\Annotations\\Description', true);
        /** @var Route $methodRoute */
        $methodRoute = $this->reader->getMethodAnnotation($className, $methodName, 'VulnModule\\Config\\Annotations\\Route', true);
        /** @var ContextAnnotation $methodContext */
        $methodContext = $this->reader->getMethodAnnotation($className, $methodName, 'VulnModule\\Config\\Annotations\\Context', true);

        if ($methodDescr) {
            if ($methodDescr->description) {
                $metadata->setDescription($methodDescr->description);
            }
        }

        if ($methodRoute) {
            if ($methodRoute->name) {
                $metadata->setRoute($methodRoute->name);
            }
            $metadata->setRouteParams($methodRoute->params);
        }

        if ($methodContext) {
            if ($methodContext->name) {
                $metadata->setName($methodContext->name);
            }
        }

        $this->cache[$key] = $metadata;
        return $metadata;
    }

    protected function getControllerClassName($controllerName, $technology) {
        $className = '';
        foreach ($this->namespaces[$technology] as $ns) {
            $tmpClassName = $ns . ucfirst($controllerName);
            if (class_exists($tmpClassName)) {
                $className = $tmpClassName;
                break;
            }
        }

        if ($technology == Context::TECH_GWT) {
            $className .= 'Impl';
        }
        return $className;
    }

    protected function getMethodNameByActionName($actionName, $technology)
    {
        if (in_array($technology, [Context::TECH_GENERIC, Context::TECH_WEB, Context::TECH_REST])) {
            return 'action_' . $actionName;
        } else {
            return $actionName;
        }
    }
}