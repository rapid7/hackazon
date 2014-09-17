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
 * where everything comes together in amfphp.
 * The class used for the entry point of a remoting call
 *
 * @package Amfphp_Core
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Gateway {

    /**
     * filter just after plugin init. Use this to add a service folder for a plugin
     * @param array serviceFolders array of absolute paths
     */
    const FILTER_SERVICE_FOLDER_PATHS = 'FILTER_SERVICE_FOLDER_PATHS';

    /**
     * filter just after plugin init. Use this to add a service for a plugin when a service folder isn't enough
     * @param array serviceNames2ClassFindInfo array of ClassFindInfo. Key is the service nane
     */
    const FILTER_SERVICE_NAMES_2_CLASS_FIND_INFO = 'FILTER_SERVICE_NAMES_2_CLASS_FIND_INFO';

    /**
     * filter called when the serialized request comes in.
     * @todo this filter only allows manipulation of raw post data, and is as such a bit misleading. Maybe rename and do filters for GET and POST
     * @param String $rawData the raw http data
     */
    const FILTER_SERIALIZED_REQUEST = 'FILTER_SERIALIZED_REQUEST';

    /**
     * filter called to allow a plugin to override the default amf deserializer.
     * Plugin should return a Amfphp_Core_Common_IDeserializer if it recognizes the content type
     * @param Amfphp_Core_Common_IDeserializer $deserializer the deserializer. null at call in gateway.
     * @param String $contentType
     */
    const FILTER_DESERIALIZER = 'FILTER_DESERIALIZER';
    
    /**
     * filter called after the request is deserialized. The callee can modify the data and return it.
     * @param mixed $deserializedRequest
     */
    const FILTER_DESERIALIZED_REQUEST = 'FILTER_DESERIALIZED_REQUEST';

    /**
     * filter called to allow a plugin to override the default amf deserialized request handler.
     * Plugin should return a Amfphp_Core_Common_IDeserializedRequestHandler if it recognizes the request
     * @param Amfphp_Core_Common_IDeserializedRequestHandler $deserializedRequestHandler null at call in gateway.
     * @param String $contentType
     */
    const FILTER_DESERIALIZED_REQUEST_HANDLER = 'FILTER_DESERIALIZED_REQUEST_HANDLER';

    /**
     * filter called when the response is ready but not yet serialized.  The callee can modify the data and return it.
     * @param $deserializedResponse
     */
    const FILTER_DESERIALIZED_RESPONSE = 'FILTER_DESERIALIZED_RESPONSE';

    /**
     * filter called to allow a plugin to override the default amf exception handler.
     * If the plugin takes over the handling of the request message,
     * it must set this to a proper Amfphp_Core_Common_IExceptionHandler
     * @param Amfphp_Core_Common_IExceptionHandler $exceptionHandler. null at call in gateway.
     * @param String $contentType
     */
    const FILTER_EXCEPTION_HANDLER = 'FILTER_EXCEPTION_HANDLER';

    /**
     * filter called to allow a plugin to override the default amf serializer.
     * @param Amfphp_Core_Common_ISerializer $serializer the serializer. null at call in gateway.
     * @param String $contentType
     * Plugin sets to a Amfphp_Core_Common_ISerializer if it recognizes the content type
     */
    const FILTER_SERIALIZER = 'FILTER_SERIALIZER';

    /**
     * filter called when the packet response is ready and serialized.
     * @param String $rawData the raw http data
     */
    const FILTER_SERIALIZED_RESPONSE = 'FILTER_SERIALIZED_RESPONSE';

    /**
     * filter called to get the headers
     * @param array $headers an associative array of headers. For example array('Content-Type' => 'application/x-amf')
     * @param String $contentType
     */
    const FILTER_HEADERS = 'FILTER_HEADERS';
    
    /**
     * filter Vo Converter
     * note: this has nothing to do with the gateway. Filter definitions should one day be centralized in an independant place.
     * @param Amfphp_Core_Common_IVoConverter
     */
    const FILTER_VO_CONVERTER = 'FILTER_VO_CONVERTER';


    /**
     * config.
     * @var Amfphp_Core_Config
     */
    protected $config;

    /**
     * typically the $_GET array.
     * @var array
     */
    protected $getData;

    /**
     * typically the $_POST array.
     * @var array
     */
    protected $postData;

    /**
     * the content type. For example for amf, application/x-amf
     * @var String
     */
    protected $contentType;

    /**
     * the serialized request 
     * @var String 
     */
    protected $rawInputData;

    /**
     * the serialized response
     * @var String
     */
    protected $rawOutputData;

    /**
     *
     */
    /**
     * constructor
     * @param array $getData typically the $_GET array.
     * @param array $postData typically the $_POST array.
     * @param String $rawInputData
     * @param String $contentType
     * @param Amfphp_Core_Config $config optional. The default config object will be used if null
     */
    public function  __construct(array $getData, array $postData, $rawInputData, $contentType, Amfphp_Core_Config $config = null) {
        $this->getData = $getData;
        $this->postData = $postData;
        $this->rawInputData = $rawInputData;
        $this->contentType = $contentType;
        if($config){
            $this->config = $config;
        }else{
            $this->config = new Amfphp_Core_Config();
        }
        
        //check for deprecated config settings. replace "true" by "false" if you want to keep this around, or simply delete once your upgrade works
        if(true){
            if(isset($this->config->serviceFolderPaths)){
                throw new Exception('In the Amfphp config, serviceFolderPaths have been renamed to serviceFolders. See http://www.silexlabs.org/amfphp/documentation/upgrading-from-2-0-x-and-2-1-x-to-2-2/');
            }
            if(isset($this->config->pluginsConfig['AmfphpCustomClassConverter'])){
                throw new Exception('The AmfphpCustomClassConverter has been renamed to AmfphpVoConverter. Please update your config accordingly. See http://www.silexlabs.org/amfphp/documentation/upgrading-from-2-0-x-and-2-1-x-to-2-2/');
            }
            if(isset($this->config->pluginsConfig['AmfphpVoConverter'])){
                $voConverterConfig = $this->config->pluginsConfig['AmfphpVoConverter'];
                if(isset($voConverterConfig['customClassFolderPaths']) || isset($voConverterConfig['voFolderPaths']))
                throw new Exception('The AmfphpVoConverter folder info is to be set in "voFolderPaths" . Please update your config accordingly. See http://www.silexlabs.org/amfphp/documentation/upgrading-from-2-0-x-and-2-1-x-to-2-2/');
            }
        }

    }
    
    /**
     * The service method runs the gateway application.  It deserializes the raw data passed into the constructor as an Amfphp_Core_Amf_Packet, handles the headers,
     * handles the messages as requests to services, and returns the responses from the services
     * It does not however handle output headers, gzip compression, etc. that is the job of the calling script
     *
     * @return <String> the serialized data
     */
    public function service(){
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $deserializedResponse = null;
        try{
            Amfphp_Core_PluginManager::getInstance()->loadPlugins($this->config->pluginsFolders, $this->config->pluginsConfig, $this->config->sharedConfig, $this->config->disabledPlugins);
            $defaultHandler = new Amfphp_Core_Amf_Handler($this->config->sharedConfig);
            
            //filter service folder paths
            $this->config->serviceFolders = $filterManager->callFilters(self::FILTER_SERVICE_FOLDER_PATHS, $this->config->serviceFolders);

            //filter service names 2 class find info
            $this->config->serviceNames2ClassFindInfo = $filterManager->callFilters(self::FILTER_SERVICE_NAMES_2_CLASS_FIND_INFO, $this->config->serviceNames2ClassFindInfo);

            //filter serialized request 
            $this->rawInputData = $filterManager->callFilters(self::FILTER_SERIALIZED_REQUEST, $this->rawInputData);

            //filter deserializer
            $deserializer = $filterManager->callFilters(self::FILTER_DESERIALIZER, $defaultHandler, $this->contentType);
            
            //deserialize
            $deserializedRequest = $deserializer->deserialize($this->getData, $this->postData, $this->rawInputData);

            //filter deserialized request
            $deserializedRequest = $filterManager->callFilters(self::FILTER_DESERIALIZED_REQUEST, $deserializedRequest);

            //create service router
            $serviceRouter = new Amfphp_Core_Common_ServiceRouter($this->config->serviceFolders, $this->config->serviceNames2ClassFindInfo, $this->config->checkArgumentCount);

            //filter deserialized request handler
            $deserializedRequestHandler = $filterManager->callFilters(self::FILTER_DESERIALIZED_REQUEST_HANDLER, $defaultHandler, $this->contentType);

            //handle request
            $deserializedResponse = $deserializedRequestHandler->handleDeserializedRequest($deserializedRequest, $serviceRouter);

        }catch(Exception $exception){
            //filter exception handler
            $exceptionHandler = $filterManager->callFilters(self::FILTER_EXCEPTION_HANDLER, $defaultHandler, $this->contentType);

            //handle exception
            $deserializedResponse = $exceptionHandler->handleException($exception);

        }
        //filter deserialized response
        $deserializedResponse = $filterManager->callFilters(self::FILTER_DESERIALIZED_RESPONSE, $deserializedResponse);


        //filter serializer
        $serializer = $filterManager->callFilters(self::FILTER_SERIALIZER, $defaultHandler, $this->contentType);

        //serialize
        $this->rawOutputData = $serializer->serialize($deserializedResponse);

        //filter serialized response 
        $this->rawOutputData = $filterManager->callFilters(self::FILTER_SERIALIZED_RESPONSE, $this->rawOutputData);

        return $this->rawOutputData;

    }

    /**
     * get the response headers. Creates an associative array of headers, then filters them, then returns an array of strings
     * @return array
     */
    public function getResponseHeaders(){
        
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $headers = array('Content-Type' => $this->contentType);
        $headers = $filterManager->callFilters(self::FILTER_HEADERS, $headers, $this->contentType);
        $ret = array();
        foreach($headers as $key => $value){
            $ret[] = $key . ': ' . $value;
        }
        return $ret;
    }

    /**
     * helper function for sending gateway data to output stream
     */
    public function output(){

        $responseHeaders = $this->getResponseHeaders();
        foreach($responseHeaders as $header){
            header($header);
        }
        echo $this->rawOutputData;
        return $this->rawOutputData;
    }

}
?>
