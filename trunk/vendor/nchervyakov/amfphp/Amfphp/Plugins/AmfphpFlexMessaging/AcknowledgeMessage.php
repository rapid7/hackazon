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
 * Used to generate a Flex Acknowledge message.
 * part of the AmfphpFlexMessaging plugin
 *
 * @package Amfphp_Plugins_FlexMessaging
 * @author Ariel Sommeria-Klein
 */
class AmfphpFlexMessaging_AcknowledgeMessage {
    /**
     * correlation id. guid
     * @see generateRandomId
     * @var string 
     */
    public $correlationId;
    
    /**
     * message id. guid
     * @see generateRandomId
     * @var string 
     */
    public $messageId;
    
     /**
     * client id. guid
     * @see generateRandomId
     * @var string 
     */   
    public $clientId;
    
    /**
     * destination
     * @var string 
     */
    public $destination;
    
    /**
     * body
     * @var stdClass 
     */
    public $body;
    
    /**
     * time to live
     * @var int 
     */
    public $timeToLive;
    
    /**
     * time stamp
     * @var int 
     */
    public $timestamp;
    
    /**
     * headers. DSId(string), DSMessagingVersion(int)
     * @var stdClass 
     */
    public $headers;

    /**
     * constructor
     * @param string $correlationId
     */
    public function __construct($correlationId) {
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $this->$explicitTypeField = AmfphpFlexMessaging::FLEX_TYPE_ACKNOWLEDGE_MESSAGE;
        $this->correlationId = $correlationId;
        $this->messageId = $this->generateRandomId();
        $this->clientId = $this->generateRandomId();
        $this->destination = null;
        $this->body = null;
        $this->timeToLive = 0;
        $this->timestamp = (int) (time() . '00');
        $this->headers = new stdClass();
    }

    /**
     *  generate random id
     * @return string
     */
    public function generateRandomId() {
        // version 4 UUID
        return sprintf(
                        '%08X-%04X-%04X-%02X%02X-%012X', mt_rand(), mt_rand(0, 65535), bindec(substr_replace(
                                        sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)
                        ), bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)), mt_rand(0, 255), mt_rand()
        );
    }

}

?>
