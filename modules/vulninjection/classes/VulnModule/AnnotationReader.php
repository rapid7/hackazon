<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 26.01.2015
 * Time: 17:47
 */


namespace VulnModule;


use App\Pixie;

class AnnotationReader
{
    /**
     * @var \Doctrine\Common\Annotations\AnnotationReader|null
     */
    protected $reader;

    /**
     * @var Pixie
     */
    protected $pixie;

    public function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    /**
     * @return null|\Doctrine\Common\Annotations\AnnotationReader
     */
    protected function _getReader()
    {
        if ($this->reader === null) {
            $this->reader = $this->pixie->container['annotation.reader'] ?: false;
        }

        return $this->reader ?: null;
    }

    public function getClassAnnotation($className, $annotationName, $checkParents = false)
    {
        $reader = $this->_getReader();
        $result = null;
        $reflectClass = class_exists($className) ? new \ReflectionClass($className) : null;

        try {
            $result = $reader->getClassAnnotation($reflectClass, $annotationName);
        } catch (\Exception $e) {
            $result = null;
        }

        $parentReflectClass = null;
        if (!$result && $reflectClass && $checkParents && ($parentReflectClass = $reflectClass->getParentClass())) {
            $result = $this->getClassAnnotation($parentReflectClass->getName(), $annotationName, $checkParents);
        }
        return $result;
    }

    public function getClassAnnotations($className)
    {
        $reader = $this->_getReader();
        try {
            return $reader->getClassAnnotations(new \ReflectionClass($className));
        } catch (\Exception $e) {
        }
        return [];
    }

    public function getMethodAnnotation($className, $methodName, $annotationName, $checkParents = false)
    {
        $reader = $this->_getReader();
        $result = null;
        $reflectMethod = class_exists($className) && method_exists($className, $methodName)
                ? new \ReflectionMethod($className, $methodName) : null;

        try {
            $result = $reader->getMethodAnnotation($reflectMethod, $annotationName);
        } catch (\Exception $e) {
            $result = null;
        }

        $parentReflectClass = null;
        if (!$result && class_exists($className)) {
            $reflectClass = new \ReflectionClass($className);
            if ($checkParents && ($parentReflectClass = $reflectClass->getParentClass())) {
                $result = $this->getMethodAnnotation($parentReflectClass->getName(), $methodName, $annotationName, $checkParents);
            }
        }

        return $result;
    }


    public function getMethodAnnotations($className, $methodName)
    {
        $reader = $this->_getReader();
        try {
            return $reader->getMethodAnnotations(new \ReflectionMethod($className, $methodName));
        } catch (\Exception $e) {
        }

        return [];
    }

    public function getClassContextAnnotations($className)
    {
        if (!$this->pixie->container['annotation.reader']) {
            return [];
        }

        if (class_exists($className)) {
            $annotation = $this->getClassAnnotation($className, 'VulnModule\\Config\\Annotations\\Route');
            $descrAnnotation = $this->getClassAnnotation($className, 'VulnModule\\Config\\Annotations\\Description');
            return [
                'route' => $annotation,
                'description' => $descrAnnotation
            ];
        }

        return [];
    }

    public function getMethodContextAnnotations($className, $methodName)
    {
        if (!$this->pixie->container['annotation.reader']) {
            return [];
        }

        if (class_exists($className) && method_exists($className, $methodName)) {
            $annotation = $this->getMethodAnnotation($className, $methodName, 'VulnModule\\Config\\Annotations\\Route');
            $descrAnnotation = $this->getMethodAnnotation($className, $methodName, 'VulnModule\\Config\\Annotations\\Description');
            if (!$annotation || !$descrAnnotation) {
                $classAnnotations = $this->getClassContextAnnotations($className);
                if (!$annotation) {
                    $annotation = $classAnnotations['route'];
                }
                if (!$descrAnnotation) {
                    $descrAnnotation = $classAnnotations['description'];
                }
            }

            return [
                'route' => $annotation,
                'description' => $descrAnnotation
            ];
        }

        return [];
    }
}