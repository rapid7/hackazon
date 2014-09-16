<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */

/**
 * Contains all collected information about a service method parameter
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Plugins_Discovery
 */
class AmfphpDiscovery_ParameterDescriptor {

    /**
     * name
     * @var string
     */
    public $name;

    /**
     * This can be gathered in 2 manners: commentary tag analysis and type hinting analysis. 
     * @var String
     */
    public $type;
    
    /**
     * This can be gathered by commentary tag analysis. It should be in json format so that it can be used by the service browser.
     * This is an example tag @param object obj This is a really important object. example: {"key":"value"}
     * @var string 
     */
    public $example;

    /**
     * @todo 
     * @var Boolean
     */
    //public $isOptional;

    /**
     * constructor
     * @param String $name
     * @param String $type
     */
    public function __construct($name, $type, $example) {
        $this->name = $name;
        $this->type = $type;
        $this->example = $example;
    }

}

?>
