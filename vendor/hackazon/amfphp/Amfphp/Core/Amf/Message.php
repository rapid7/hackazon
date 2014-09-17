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
 * Amfphp_Core_Amf_Message is a data type that encapsulates all of the various properties a Message object can have.
 *
 * @package Amfphp_Core_Amf
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Amf_Message {

    /**
     * inthe case of a request:
     * parsed to a service name and a function name. supported separators for the targetUri are '.' and '/'
     * The service name can either be just the name of the class (TestService) or include a path(package/TestService)
     * example of full targetUri package/TestService/mirrorFunction
     *
     * in the case of a response:
     * the request responseUri + OK/KO
     * for example: /1/onResult or /1/onStatus
     *
     * @var String
     */
    public $targetUri = '';

    /**
     * in the case of a request:
     * operation name, for example /1
     *
     * in the case of a response:
     * undefined
     * 
     * @var String
     */
    public $responseUri = '';

    /**
     * data
     * @var mixed
     */
    public $data;

    /**
     * constructor
     * @param String $targetUri
     * @param String $responseUri
     * @param mixed $data
     */
    public function __construct($targetUri = '', $responseUri = '', $data = null) {
        $this->targetUri = $targetUri;
        $this->responseUri = $responseUri;
        $this->data = $data;
    }

}

?>
