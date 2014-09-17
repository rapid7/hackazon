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
 * The information necessary for a service router to be able to load a class' file and instanciate it
 *
 * @package Amfphp_Core_Common
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Common_ClassFindInfo {

    /**
     * the absolute path to the file containing the class definition
     * @var String
     */
    public $absolutePath;

    /**
     * the name of the class.can include a namespace, for example : \namespace\class
     * @var String
     */
    public $className;

    /**
     * constructor
     * @param String $absolutePath
     * @param String $className
     */
    public function __construct($absolutePath, $className) {
        $this->absolutePath = $absolutePath;
        $this->className = $className;
    }

}

?>
