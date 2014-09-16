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
 * interface for Vo Converters.  
 * @package Amfphp_Core_Common
 * @author Ariel Sommeria-klein
 */
interface Amfphp_Core_Common_IVoConverter {
    
    /**
     * creates and returns an instance of of $voName.
     * 
     * @param type $voName
     * @return typed object or null
     */
    public function getNewVoInstance($voName);

    
    /**
     * sets the the explicit type marker on the object. 
     *
     * 
     * @param mixed $obj
     * @return mixed
     */
    public function markExplicitType($obj);
    
        
    /**
     * for some protocols it is possible to call convertToType and markExplicitObject directly during deserialization and serialization.
     * This is typically the case of AMF, but not JSON.
     * In that case this function must be called with enabled set to false, so the plugin does not scan the objects to do it itself. 
     * By default scanning is enabled
     * @param boolean $enabled
     */
    public function setScanEnabled($enabled);
    /**
     * get scan enabled.
     * @return boolean 
     */
    public function getScanEnabled();
}
?>
