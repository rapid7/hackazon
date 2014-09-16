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
 * Converts data from incoming packets with explicit types to Value Objects(Vos), and vice versa for the outgoing packets.
 * 
 * This plugin can be deactivated if the project doesn't use Value Objects.
 * 
 * The AMF deserializer reads a typed AMF object as a stdObj class, and sets the AMF type to a reserved "explicit type" field.
 * This plugin will look at deserialized data and try to convert any such objects to a real Value Object.
 * 
 * It works in the opposite way on the way out: The AMF serializer needs a stdObj class with the explicit type marker set 
 * to write a typed AMF object. This plugin will convert any typed PHP objects to a stdObj with the explicit type marker set.
 * 
 * The explicit type marker is defined in Amfphp_Core_Amf_Constants
 * 
 * If after deserialization the Value Object is not found, the object is unmodified and the explicit type marker is left set.
 * If the explicit type marker is already set in an outgoing object, the value is left as is.
 * 
 * 
 * This works for nested objects.
 * 
 * 
 * If you don't need strong typing in PHP but would like the objects in your client to be strongly typed, you can:
 * For example a stdObj like this will be returned in AMF as MyVO
 * <code>
 * $returnObj = new stdObj();
 * $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
 * $returnObj->$explicitTypeField = "MyVO"; 
 * </code>
 * 
 * If you are using Flash, remember that you need to register the class alias so that Flash converts the MyVO AMF object to a Flash MyVO object.
 * If you are using Flex you can do this with the RemoteClass metadata tag.
 *  
 * @see Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE
 * @link http://help.adobe.com/en_US/FlashPlatform/reference/actionscript/3/flash/net/package.html#registerClassAlias%28%29 
 * @link http://livedocs.adobe.com/flex/3/html/metadata_3.html#198729
 * @package Amfphp_Plugins_VoConverter
 * @author Ariel Sommeria-Klein
 */
class AmfphpVoConverter implements Amfphp_Core_Common_IVoConverter {

    /**
     * paths to folders containing Value Objects(relative or absolute)
     * default is /Services/Vo/
     * if you need a namespace, use an array instead. First value is the path, second is the root namespace.
     * for example: array(AMFPHP_ROOTPATH . '/Services/Vo/', 'namespace');
     * @var array of folders
     */
    public $voFolders;
    
    /**
     * Set this to true if you want an exception to be thrown when a Value Object is not found. 
     * Avoid setting this to true on a public server as the exception contains details about your server configuration.
     * 
     * @var boolean 
     */
    public $enforceConversion;
    
    /**
     * should objects be scanned or converted. 
     * @see setScanEnabled
     * default true
     * @var boolean
     */
    protected $scanEnabled = true;
    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function __construct(array $config = null) {
        //default
        $this->voFolders = array(AMFPHP_ROOTPATH . '/Services/Vo/');
        if ($config) {
            if (isset($config['voFolders'])) {
                $this->voFolders = $config['voFolders'];
            }
            if (isset($config['enforceConversion'])) {
                $this->enforceConversion = $config['enforceConversion'];
            }            
        }
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_VO_CONVERTER, $this, 'filterVoConverter');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST, $this, 'filterDeserializedRequest');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_RESPONSE, $this, 'filterDeserializedResponse');
    }
    
    /**
     *  provides this as a default vo converter
     * @return \AmfphpVoConverter
     */
    public function filterVoConverter(){
        return $this;
    }
    /**
     * converts untyped objects to their typed counterparts. Loads the class if necessary
     * @param mixed $deserializedRequest
     * @return mixed
     */
    public function filterDeserializedRequest($deserializedRequest) {
        $ret = null;
        if($this->scanEnabled){
            $ret = Amfphp_Core_Amf_Util::applyFunctionToContainedObjects($deserializedRequest, array($this, 'convertToTyped'));
        }
        if(class_exists('AmfphpMonitor', false)){
            AmfphpMonitor::addTime('Request VO Conversion');
        }

        return $ret;
    }

    /**
     * looks at the outgoing packet and sets the explicit type field so that the serializer sends it properly
     * @param mixed $deserializedResponse
     * @return mixed
     */
    public function filterDeserializedResponse($deserializedResponse) {
        $ret = null;
        if($this->scanEnabled){
            $ret = Amfphp_Core_Amf_Util::applyFunctionToContainedObjects($deserializedResponse, array($this, 'markExplicitType'));
        }
        if(class_exists('AmfphpMonitor', false)){
            AmfphpMonitor::addTime('Response VO Conversion');
        }
        return $ret;
    }
    
    /**
     * for some protocols it is possible to call convertToType and markExplicitObject directly during deserialization and serialization.
     * This is typically the case of AMF, but not JSON.
     * In that case this function must be called with enabled set to false, so the plugin does not scan the objects to do it itself. 
     * By default scanning is enabled
     * @param boolean $enabled
     */
    public function setScanEnabled($enabled){
        $this->scanEnabled = $enabled;
    }
    /**
     * get scan enabled.
     * @return boolean  
     */
    public function getScanEnabled(){
        return $this->scanEnabled;
    }
    
    /**
     * if the object contains an explicit type marker, this method attempts to convert it to its typed counterpart
     * If then the class is still not available, the object is not converted
     * note: This is not a recursive function. Rather the recusrion is handled by Amfphp_Core_Amf_Util::applyFunctionToContainedObjects.
     * must be public so that Amfphp_Core_Amf_Util::applyFunctionToContainedObjects can call it
     * @param mixed $obj
     * @return mixed
     */
    public function convertToTyped($obj) {
        if (!is_object($obj)) {
            return $obj;
        }
        
        
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        if (isset($obj->$explicitTypeField)) {
            $voName = $obj->$explicitTypeField;
            $typedObj = $this->getNewVoInstance($voName);
            if($typedObj){
                foreach ($obj as $key => $data) { // loop over each element to copy it into typed object
                    if ($key != $explicitTypeField) {
                        $typedObj->$key = $data;
                    }
                }
                return $typedObj;
            }
        }

        return $obj;
    }
    
    /**
     * creates and returns an instance of of $voName.
     * if the Vo class is already available, then simply creates a new instance of it. If not,
     * attempts to load the file from the available service folders.
     * If all fails, there is the option to throw an error.
     * 
     * @param type $voName
     * @return typed object or null
     */
    public function getNewVoInstance($voName){
        $fullyQualifiedClassName = $voName;
        if (!class_exists($voName, false)) {
            foreach ($this->voFolders as $folder) {
                $folderPath = NULL;
                $rootNamespace = NULL;
                if(is_array($folder)){
                    $rootNamespace = $folder[1];
                    $folderPath = $folder[0];
                }else{
                    $folderPath = $folder;
                }
                $voNameWithSlashes = str_replace('.', '/', $voName);
                $voPath = $folderPath . $voNameWithSlashes . '.php';
                 
                if (file_exists($voPath)) {
                    require_once $voPath;
                    if($rootNamespace != NULL){
                        $fullyQualifiedClassName = $rootNamespace . '\\' . str_replace('/', '\\', $voNameWithSlashes); 
                    }
                    break;
                    
                }
            }
        }
        if (class_exists($fullyQualifiedClassName, false)) {
            //class is available. Use it!
            $vo = new $fullyQualifiedClassName();
            return $vo;
        }else{
            if($this->enforceConversion){
                throw new Amfphp_Core_Exception("\"$voName\" Vo not found. \nCustom Class folder paths : " . print_r($this->voFolders, true));
            }else{
                $ret = new stdClass();
                $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
                $ret->$explicitTypeField = $voName;
                return $ret;
            }
        }
        
    }
    

    /**
     * sets the the explicit type marker on the object. This is only done if it not already set, as in some cases
     * the service class might want to do this manually.
     * note: This is not a recursive function. Rather the recusrion is handled by Amfphp_Core_Amf_Util::applyFunctionToContainedObjects.
     * must be public so that Amfphp_Core_Amf_Util::applyFunctionToContainedObjects can call it
     * 
     * @param mixed $obj
     * @return mixed
     */
    public function markExplicitType($obj) {
        if (!is_object($obj)) {
            return $obj;
        }
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $className = get_class($obj);
        if ($className != 'stdClass' && !isset($obj->$explicitTypeField)) {
            $obj->$explicitTypeField = $className;
        }
        return $obj;
    }

}

?>
