<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.01.2015
 * Time: 15:45
 */


namespace VulnModule\Config\Annotations;


/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 * @package VulnModule\Config\Annotation
 */
class Context 
{
    public $name;

    public $technology;

    public $storage_role;

    public $type;
}