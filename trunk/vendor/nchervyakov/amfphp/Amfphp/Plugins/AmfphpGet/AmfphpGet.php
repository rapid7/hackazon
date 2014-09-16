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
 * Adds support for HTTP GET requests to services, useful for simple test calls and for cross domain ajax calls.
 * This plugin can be deactivated if the project doesn't use GET requests.
 *  
 * Cross Domain Ajax calls are normally not possible for security reasons, but by using a hack you can get around it.
 * This however must be done with HTTP GET. So this plugin is specifically for requesting json data from amfPHP using HTTP GET. 
 * This comes with some limitations: GET is limited in size, and you can't send complex objects.
 * If you're on the same domain, you're probably better off using the AmfphpJson plugin as these limitations don't apply.
 * 
 * You must add the 'text/amfphpget' content type, or set it in the headers so that it is recognized as a call to be handled by this plugin.
 * for example:
 * http://yourserver.com/?contentType=text/amfphpget&serviceName=YourService&methodName=yourMethod&p01=value1&p2=value2 etc.
 * 
 * If you are using this for crossdomain ajax with JSONP, the expected format of the request is to add the extra 'callback' parameter.
 * If no callback id is found, the answer simply contains the json encoded return data.
 * If the callback is found, the answer is wrapped so that it can be used for JSONP.
 * 
 * Thanks to nViso.ch who needed the cross domain ajax functionality.
 *  
 * Requires at least PHP 5.2.
 *
 * @see http://remysharp.com/2007/10/08/what-is-jsonp/ 
 * @see http://usejquery.com/posts/9/the-jquery-cross-domain-ajax-guide 
 * @package Amfphp_Plugins_Get
 * @author Ariel Sommeria-Klein.  
 */
class AmfphpGet implements Amfphp_Core_Common_IDeserializer, Amfphp_Core_Common_IDeserializedRequestHandler, Amfphp_Core_Common_IExceptionHandler, Amfphp_Core_Common_ISerializer {

    /**
    * the content-type string indicating a cross domain ajax call
    */
    const CONTENT_TYPE = 'text/amfphpget';
    
    /**
     * return error details.
     * @see Amfphp_Core_Config::CONFIG_RETURN_ERROR_DETAILS
     * @var boolean
     */
    protected $returnErrorDetails = false;
	
    /**
     * constructor. Add filters on the HookManager.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function  __construct(array $config = null) {
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZER, $this, 'filterHandler');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST_HANDLER, $this, 'filterHandler');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_EXCEPTION_HANDLER, $this, 'filterHandler');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZER, $this, 'filterHandler');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_HEADERS, $this, 'filterHeaders');
        $this->returnErrorDetails = (isset ($config[Amfphp_Core_Config::CONFIG_RETURN_ERROR_DETAILS]) && $config[Amfphp_Core_Config::CONFIG_RETURN_ERROR_DETAILS]);
        
    }

    /**
     * If the content type contains the 'json' string, returns this plugin
     * @param mixed null at call in gateway.
     * @param String $contentType
     * @return this or null
     */
    public function filterHandler($handler, $contentType){
        if(strpos($contentType, self::CONTENT_TYPE) !== false){
            return $this;
        }
    }

    /**
     * deserialize
     * @see Amfphp_Core_Common_IDeserializer
     * @param array $getData
     * @param array $postData
     * @param string $rawPostData
     * @return string
     */
    public function deserialize(array $getData, array $postData, $rawPostData){
        return $getData;
    }

    /**
     * Retrieve the serviceName, methodName and parameters from the PHP object
     * representing the JSON string
     * call service
     * @see Amfphp_Core_Common_IDeserializedRequestHandler
     * @param array $deserializedRequest
     * @param Amfphp_Core_Common_ServiceRouter $serviceRouter
     * @return the service call response
     */
    public function handleDeserializedRequest($deserializedRequest, Amfphp_Core_Common_ServiceRouter $serviceRouter){
		
        if(isset ($deserializedRequest['serviceName'])){
            $serviceName = $deserializedRequest['serviceName'];
        }else{
            throw new Exception('Service name field missing in call parameters \n' . print_r($deserializedRequest, true));
        }
        if(isset ($deserializedRequest['methodName'])){
            $methodName = $deserializedRequest['methodName'];
        }else{
            throw new Exception('MethodName field missing in call parameters \n' . print_r($deserializedRequest, true));
        }
        $parameters = array();
        $paramCounter = 1;
        while(isset ($deserializedRequest["p$paramCounter"])){
            $parameters[] = $deserializedRequest["p$paramCounter"];
            $paramCounter++;
        }
        return $serviceRouter->executeServiceCall($serviceName, $methodName, $parameters);
        
    }

    /**
     * handle exception
     * @see Amfphp_Core_Common_IExceptionHandler
     * @param Exception $exception
     * @return stdClass
     */
    public function handleException(Exception $exception){
        $error = new stdClass();
        $error->message = $exception->getMessage();
        $error->code = $exception->getCode();
        if($this->returnErrorDetails){
            $error->file = $exception->getFile();
            $error->line = $exception->getLine();
            $error->stack = $exception->getTraceAsString();
        }        
        return (object)array('error' => $error);
        
    }
    
    /**
     * Encode the PHP object returned from the service call into a JSON string
     * @see Amfphp_Core_Common_ISerializer
     * @param mixed $data
     * @return string the encoded JSON string sent to JavaScript
     */
    public function serialize($data){
        $encoded = json_encode($data);
        if(isset ($_GET['callback'])){
            return $_GET['callback'] . '(' . $encoded . ');';
        }else{
            return $encoded;
        }
    }
    
    
    /**
     * sets return content type to json
     * @param array $headers
     * @param string $contentType
     * @return array
     */
    public function filterHeaders($headers, $contentType){
        if ($contentType == self::CONTENT_TYPE) {
            $headers['Content-Type'] =  'application/json';
            return $headers;
        }
    }    


}
?>
