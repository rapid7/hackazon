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
 * interface for serializers. 
 * @package Amfphp_Core_Common
 * @author Ariel Sommeria-klein
 */
interface Amfphp_Core_Common_ISerializer {
    
    /**
     * Calling this executes the serialization. The return type is noted as a String, but is a binary stream. echo it to the output buffer
     * @param mixed $data the data to serialize.
     * @return String
     */
    public function serialize($data);
}
?>
