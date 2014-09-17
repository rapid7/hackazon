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
 * Amf xml (not the AS3/AMF3 XMLDocument) will be converted to and from this class.
 * PHP has many libs to manipulate XML, and it is not up to amfPHP to choose which one to use. Furthermore, AS3 has 2 XML types, XML and and XMLDocument.
 * So amfPHP just wraps the string data in these types, and iut is up to the user to parse the contained string data
 *
 * @package Amfphp_Core_Amf_Types
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Amf_Types_Xml {

    /**
     * data
     * @var string 
     */
    public $data;

    /**
     * constructor
     * @param string $data
     */
    public function __construct($data) {
        $this->data = $data;
    }

}

?>
