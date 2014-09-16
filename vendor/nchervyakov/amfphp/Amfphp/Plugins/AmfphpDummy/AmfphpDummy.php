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
 * This catches browser requests to the gateway, to show something more helpful than an error message.
 *
 * @package Amfphp_Plugins_Dummy
 * @author Ariel Sommeria-Klein, Daniel Hoffmann (intermedi8.de) 
 */
class AmfphpDummy implements Amfphp_Core_Common_IDeserializer, Amfphp_Core_Common_IDeserializedRequestHandler, Amfphp_Core_Common_IExceptionHandler, Amfphp_Core_Common_ISerializer {
    /**
     * if content type is not set or content is set to "application/x-www-form-urlencoded", this plugin will handle the request
     */

    const CONTENT_TYPE = "application/x-www-form-urlencoded";

    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function __construct(array $config = null) {
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST_HANDLER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_EXCEPTION_HANDLER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_HEADERS, $this, "filterHeaders");
    }

    /**
     * if no content type, then returns this.
     * @param mixed null at call in gateway.
     * @param String $contentType
     * @return this or null
     */
    public function filterHandler($handler, $contentType) {
        if (!$contentType || $contentType == self::CONTENT_TYPE) {
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
    public function deserialize(array $getData, array $postData, $rawPostData) {
        $ret = new stdClass();
        return $ret;
    }

    /**
     * handle deserialized request
     * @see Amfphp_Core_Common_IDeserializedRequestHandler
     * @param mixed $deserializedRequest
     * @param Amfphp_Core_Common_ServiceRouter $serviceRouter
     * @return mixed
     */
    public function handleDeserializedRequest($deserializedRequest, Amfphp_Core_Common_ServiceRouter $serviceRouter) {
        
    }

    /**
     * handle exception
     * @see Amfphp_Core_Common_IExceptionHandler
     * @param Exception $exception
     */
    public function handleException(Exception $exception) {
        
    }

    /**
     * serialize. just includes index.html
     * @see Amfphp_Core_Common_ISerializer
     * @param mixed $data
     * @return mixed
     */
    public function serialize($data) {

        include(dirname(__FILE__) . "/index.html");
    }

    /**
     * filter the headers to make sure the content type is set to text/html if the request was handled by the service browser
     * @param array $headers
     * @param string $contentType
     * @return array
     */
    public function filterHeaders($headers, $contentType) {
        if (!$contentType || $contentType == self::CONTENT_TYPE) {
            return array();
        }
    }

}

?>
