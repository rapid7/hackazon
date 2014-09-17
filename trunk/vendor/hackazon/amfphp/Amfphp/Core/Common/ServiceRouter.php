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
 * The Service Router class is responsible for executing the remote service method and returning it's value.
 * based on the old 'Executive' of php 1.9. It looks for a service either explicitely defined in a
 * ClassFindInfo object, or in a service folder.
 *
 * @package Amfphp_Core_Common
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Common_ServiceRouter {
    /**
     * filter called when the service object is created. Useful for authentication
     * @param Object $serviceObject 
     * @param string $serviceName
     * @param string $methodName
     * @param array $parameters
     */
    const FILTER_SERVICE_OBJECT = 'FILTER_SERVICE_OBJECT';
    /**
     * paths to folders containing services(relative or absolute)
     * @var array of paths
     */
    public $serviceFolders;

    /**
     *
     * @var array of ClassFindInfo
     */
    public $serviceNames2ClassFindInfo;
    
    /**
     * check parameters. This is useful for development, but should be disabled for production
     * @var Boolean
     */
    public $checkArgumentCount;

    /**
     * constructor
     * @param array $serviceFolders folders containing service classes
     * @param array $serviceNames2ClassFindInfo a dictionary of service classes represented in a ClassFindInfo.
     * @param Boolean $checkArgumentCount
     */
    public function __construct($serviceFolders, $serviceNames2ClassFindInfo, $checkArgumentCount = false) {
        $this->serviceFolders = $serviceFolders;
        $this->serviceNames2ClassFindInfo = $serviceNames2ClassFindInfo;
        $this->checkArgumentCount = $checkArgumentCount;
    }

    /**
     * get a service object by its name. Looks for a match in serviceNames2ClassFindInfo, then in the defined service folders.
     * If none found, an exception is thrown
     * this method is static so that it can be used also by the discovery service
     *  '__' are replaced by '/' to help the client generator support packages without messing with folders and the like
     * the service object can either be in the global namespace or in the namespace suggested by the name.
     * For example a call to Sub1/Sub2/NamespaceTestService will load the PHP file in Sub1/Sub2/NamespaceTestService,
     * and return an instance of either NamespaceTestService or Sub1\Sub2\NamespaceTestService
     * 
     * @param type $serviceName
     * @param array $serviceFolders
     * @param array $serviceNames2ClassFindInfo
     * @return Object service object
     */
    public static function getServiceObjectStatically($serviceName, array $serviceFolders, array $serviceNames2ClassFindInfo){
        $serviceObject = null;
        if (isset($serviceNames2ClassFindInfo[$serviceName])) {
            $classFindInfo = $serviceNames2ClassFindInfo[$serviceName];
            require_once $classFindInfo->absolutePath;
            $serviceObject = new $classFindInfo->className();
        } else {
            $temp = str_replace('.', '/', $serviceName);
            $serviceNameWithSlashes = str_replace('__', '/', $temp);
            $serviceIncludePath = $serviceNameWithSlashes . '.php';
            $exploded = explode('/', $serviceNameWithSlashes);
            $className = $exploded[count($exploded) - 1];
            //no class find info. try to look in the folders
            foreach ($serviceFolders as $folder) {
                $folderPath = NULL;
                $rootNamespace = NULL;
                if(is_array($folder)){
                    $rootNamespace = $folder[1];
                    $folderPath = $folder[0];
                }else{
                    $folderPath = $folder;
                }
                $servicePath = $folderPath . $serviceIncludePath;
                
                if (file_exists($servicePath)) {
                    require_once $servicePath;
                    if($rootNamespace == NULL){
                        $serviceObject = new $className();
                    }else{
                        $namespacedClassName = $rootNamespace . '\\' . str_replace('/', '\\', $serviceNameWithSlashes);
                       
                        $serviceObject = new $namespacedClassName;
                        
                    }
                    
                }
            }
        }

        if (!$serviceObject) {
            throw new Amfphp_Core_Exception("$serviceName service not found ");
        }
        return $serviceObject;
        
    }
    
    /**
     * get service object
     * @param String $serviceName
     * @return Object service object
     */
    public function getServiceObject($serviceName) {
        return self::getServiceObjectStatically($serviceName, $this->serviceFolders, $this->serviceNames2ClassFindInfo);
    }

    /**
     * loads and instanciates a service class matching $serviceName, then calls the function defined by $methodName using $parameters as parameters
     * throws an exception if service not found.
     * if the service exists but not the function, an exception is thrown by call_user_func_array. It is pretty explicit, so no further code was added
     *
     * @param string $serviceName
     * @param string $methodName
     * @param array $parameters
     * @return mixed the result of the function call
     *
     */
    public function executeServiceCall($serviceName, $methodName, array $parameters) {
        $unfilteredServiceObject = $this->getServiceObject($serviceName);
        $serviceObject = Amfphp_Core_FilterManager::getInstance()->callFilters(self::FILTER_SERVICE_OBJECT, $unfilteredServiceObject, $serviceName, $methodName, $parameters);

        $isStaticMethod = false;
        
        if(method_exists($serviceObject, $methodName)){
            //method exists, but isn't static
        }else if (method_exists($serviceName, $methodName)) {
            $isStaticMethod = true;
        }else{
            throw new Amfphp_Core_Exception("method $methodName not found on $serviceName object ");
        }
        
        if(substr($methodName, 0, 1) == '_'){
            throw new Exception("The method $methodName starts with a '_', and is therefore not accessible");
        }
        
        if($this->checkArgumentCount){
            $method = new ReflectionMethod($serviceObject, $methodName);
            $numberOfRequiredParameters = $method->getNumberOfRequiredParameters();
            $numberOfParameters = $method->getNumberOfParameters();
            $numberOfProvidedParameters = count($parameters);
            if ($numberOfProvidedParameters < $numberOfRequiredParameters || $numberOfProvidedParameters > $numberOfParameters) {
                throw new Amfphp_Core_Exception("Invalid number of parameters for method $methodName in service $serviceName : $numberOfRequiredParameters  required, $numberOfParameters total, $numberOfProvidedParameters provided");
            }      
        }
        if($isStaticMethod){
            return call_user_func_array(array($serviceName, $methodName), $parameters);
        }else{
            return call_user_func_array(array($serviceObject, $methodName), $parameters);
        }
    }


}

?>