<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * 
 */

/**
 * Contains all collected information about a service method.
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Plugins_Discovery
 */
class AmfphpDiscovery_MethodDescriptor {

    /**
     * name
     * @var string 
     */
    public $name;

    /**
     * 
     * @var array of ParameterInfo
     */
    public $parameters;

    /**
     *
     * @var string method level comment
     */
    public $comment;

    /**
     * return type
     * @var string 
     */
    public $returnType;

    /**
     * constructor
     * @param string $name
     * @param array $parameters
     * @param string $comment
     * @param string $returnType 
     */
    public function __construct($name, array $parameters, $comment, $returnType) {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->comment = $comment;
        $this->returnType = $returnType;
    }

}

?>
