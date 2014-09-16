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
 * content holder for an Amf Packet.
 *
 * @package Amfphp_Core_Amf
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Amf_Packet {
    /**
     * The place to keep the headers data
     *
     * @var <array>
     */
    public $headers;

    /**
     * The place to keep the Message elements
     *
     * @var <array>
     */
    public $messages;

    /**
     * either 0 or 3. This is stored here when deserializing, because the serializer needs the info
     * @var <int>
     */
    public $amfVersion;


    /**
     * The constructor function for a new Amf object.
     *
     * All the constructor does is initialize the headers and Messages containers
     */
    public function __construct() {
        $this->headers = array();
        $this->messages = array();
        $this->amfVersion = Amfphp_Core_Amf_Constants::AMF0_ENCODING;
    }

    

}
?>
