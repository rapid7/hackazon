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
 * logs requests and responses in their serialized and deserialized forms. 
 * deactivated by default.
 * 
 * Note that this a crude logging system, with no levels, targets etc. like Log4j for example.
 * It is as such to be used for development purposes, but not for production
 *
 * @package Amfphp_Plugins_Logger
 * @author Ariel Sommeria-klein
 */
class AmfphpLogger {

    const LOG_FILE_PATH = 'amfphplog.log';

    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function __construct(array $config = null) {
        $filterManager = Amfphp_Core_FilterManager::getInstance();

        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZED_REQUEST, $this, 'filterSerializedRequest');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST, $this, 'filterDeserializedRequest');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_RESPONSE, $this, 'filterDeserializedResponse');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZED_RESPONSE, $this, 'filterSerializedResponse');
    }

    /**
     * write message to log file at LOG_FILE_PATH
     * @see LOG_FILE_PATH
     * @param String $message
     * @throws Amfphp_Core_Exception
     */
    public static function logMessage($message) {
        $fh = fopen(self::LOG_FILE_PATH, 'a');
        if (!$fh) {
            throw new Amfphp_Core_Exception("couldn't open log file for writing");
        }
        fwrite($fh, $message . "\n");
        fclose($fh);
    }

    /**
     * logs the serialized incoming packet
     * @param String $rawData
     */
    public function filterSerializedRequest($rawData) {
        self::logMessage("serialized request : \n$rawData");
    }

    /**
     * logs the deserialized request
     * @param mixed $deserializedRequest
     */
    public function filterDeserializedRequest($deserializedRequest) {
        self::logMessage("deserialized request : \n " . print_r($deserializedRequest, true));
    }

    /**
     * logs the deserialized response
     * @param packet $deserializedResponse
     */
    public function filterDeserializedResponse($deserializedResponse) {
        self::logMessage("deserialized response : \n " . print_r($deserializedResponse, true));
    }

    /**
     * logs the deserialized incoming packet
     * @param mixed $rawData
     */
    public function filterSerializedResponse($rawData) {
        self::logMessage("serialized response : \n$rawData");
    }

}

?>
