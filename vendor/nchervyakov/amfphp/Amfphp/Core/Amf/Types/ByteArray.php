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
 * Amf byte arrays will be converted to and from this class
 *
 * @package Amfphp_Core_Amf_Types
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Amf_Types_ByteArray {

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
