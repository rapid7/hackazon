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
 * Contains all collected information about a service. This information will be used by the generator. 
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Plugins_Discovery
 */
class AmfphpDiscovery_ServiceDescriptor {
    /**
     *name
     * @var string
     */
     public $name;
    /**
     *  methods
     * @var array of MethodInfo
     */
    public $methods; 
    
    /**
     * class level comment
     * @var string 
     */
    public $comment;

    /**
     * constructor
     * @param string $name
     * @param array $methods
     * @param string $comment
     */
    public function __construct($name, array $methods, $comment) {
        $this->name = $name;
        $this->methods = $methods;
        $this->comment = $comment;
    }
}

?>
