<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.01.2015
 * Time: 16:10
 */


namespace AmfphpModule\Core;


use Amfphp_Core_Config;
use AmfphpModule\Core\Amf\Handler;
use App\Pixie;

class Gateway extends \Amfphp_Core_Gateway
{
    /**
     * @var Pixie
     * @inheritdoc
     */
    protected $pixie;

    public function  __construct(Pixie $pixie, array $getData, array $postData, $rawInputData, $contentType, Amfphp_Core_Config $config = null)
    {
        parent::__construct($getData, $postData, $rawInputData, $contentType, $config);
        $this->pixie = $pixie;
    }

    /**
     * @inheritdoc
     */
    public function service()
    {
        $filterManager = \Amfphp_Core_FilterManager::getInstance();
        $deserializedResponse = null;
        $defaultHandler = null;

        try{
            \Amfphp_Core_PluginManager::getInstance()->loadPlugins($this->config->pluginsFolders, $this->config->pluginsConfig, $this->config->sharedConfig, $this->config->disabledPlugins);
            $defaultHandler = new Handler($this->config->sharedConfig, $this->pixie);

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
            $serviceRouter = new \Amfphp_Core_Common_ServiceRouter($this->config->serviceFolders, $this->config->serviceNames2ClassFindInfo, $this->config->checkArgumentCount);

            //filter deserialized request handler
            /** @var Handler $deserializedRequestHandler */
            $deserializedRequestHandler = $filterManager->callFilters(self::FILTER_DESERIALIZED_REQUEST_HANDLER, $defaultHandler, $this->contentType);

            //handle request
            $deserializedResponse = $deserializedRequestHandler->handleDeserializedRequest($deserializedRequest, $serviceRouter);

        }catch(\Exception $exception){
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

    public function getResponseHeaders()
    {
        $filterManager = \Amfphp_Core_FilterManager::getInstance();
        $headers = array('Content-Type' => $this->contentType);
        $headers = $filterManager->callFilters(self::FILTER_HEADERS, $headers, $this->contentType);
        $ret = array();
        foreach($headers as $key => $value){
            $ret[] = implode(': ', array_filter([trim($key), trim($value)]));
        }
        return $ret;
    }
}