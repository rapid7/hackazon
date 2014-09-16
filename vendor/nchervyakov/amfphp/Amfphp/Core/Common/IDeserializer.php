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
 * interface for deserializers. 
 * @package Amfphp_Core_Common
 * @author Ariel Sommeria-klein
 */
interface Amfphp_Core_Common_IDeserializer {
    
    /**
     * deserialize the data.
     * @param array $getData typically the $_GET array. 
     * @param array $postData typically the $_POST array.
     * @param String $rawPostData
     * @return mixed the deserialized data. For example an Amf packet.
     */
    public function deserialize(array $getData, array $postData, $rawPostData);
}
?>
