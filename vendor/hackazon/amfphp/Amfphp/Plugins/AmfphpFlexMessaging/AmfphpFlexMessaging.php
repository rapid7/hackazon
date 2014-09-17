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
 *  includes
 *  */
require_once dirname(__FILE__) . '/AcknowledgeMessage.php';
require_once dirname(__FILE__) . '/ErrorMessage.php';

/**
 * Support for flex messaging.
 * 
 * This plugin can be deactivated if the project doesn't need flex messaging, usually a RemoteObject in Flex.
 * 
 * Flex doesn't use the basic packet system. When using a remote objct, first a CommandMessage is sent, expecting an AcknowledgeMessage in return.
 * Then a RemotingMessage is sent, expecting an AcknowledgeMessage in return.
 * This plugin adds support for this message flow.
 * 
 * In case of an error, an ErrorMessage is expected
 * 
 * @link http://livedocs.adobe.com/flex/3/html/help.html?content=data_access_4.html
 * @package Amfphp_Plugins_FlexMessaging
 * @author Ariel Sommeria-Klein
 */
class AmfphpFlexMessaging {

    const FLEX_TYPE_COMMAND_MESSAGE = 'flex.messaging.messages.CommandMessage';
    const FLEX_TYPE_REMOTING_MESSAGE = 'flex.messaging.messages.RemotingMessage';
    const FLEX_TYPE_ACKNOWLEDGE_MESSAGE = 'flex.messaging.messages.AcknowledgeMessage';
    const FLEX_TYPE_ERROR_MESSAGE = 'flex.messaging.messages.ErrorMessage';
    const FIELD_MESSAGE_ID = 'messageId';

    /**
     * if this is set, special error handling applies
     * @var Boolean
     */
    protected $clientUsesFlexMessaging;

    /**
     * the messageId of the last flex message. Used for error generation
     * @var String
     */
    protected $lastFlexMessageId;

    /**
     * the response uri of the last flex message. Used for error generation
     * @var String
     */
    protected $lastFlexMessageResponseUri;

    /**
     * return error details.
     * @see Amfphp_Core_Config::CONFIG_RETURN_ERROR_DETAILS
     * @var boolean
     */
    protected $returnErrorDetails = false;

    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function __construct(array $config = null) {
        Amfphp_Core_FilterManager::getInstance()->addFilter(Amfphp_Core_Amf_Handler::FILTER_AMF_REQUEST_MESSAGE_HANDLER, $this, 'filterAmfRequestMessageHandler');
        Amfphp_Core_FilterManager::getInstance()->addFilter(Amfphp_Core_Amf_Handler::FILTER_AMF_EXCEPTION_HANDLER, $this, 'filterAmfExceptionHandler');
        $this->clientUsesFlexMessaging = false;
        $this->returnErrorDetails = (isset($config[Amfphp_Core_Config::CONFIG_RETURN_ERROR_DETAILS]) && $config[Amfphp_Core_Config::CONFIG_RETURN_ERROR_DETAILS]);
    }

    /**
     * filter amf request message handler
     * @param Object $handler null at call. If the plugin takes over the handling of the request message,
     * it must set this to a proper handler for the message, probably itself.
     * @param Amfphp_Core_Amf_Message $requestMessage the request message
     * @return array
     */
    public function filterAmfRequestMessageHandler($handler, Amfphp_Core_Amf_Message $requestMessage) {
        if ($requestMessage->targetUri == 'null') {
            //target uri is null for Flex messaging, so it's an easy way to detect it.  This plugin will handle it 
            $this->clientUsesFlexMessaging = true;
            return $this;
        }
        

    }

    /**
     * filter amf exception handler
     * @param Object $handler null at call. If the plugin takes over the handling of the request message,
     * it must set this to a proper handler for the message, probably itself.
     * @return array
     */
    public function filterAmfExceptionHandler($handler) {
        if ($this->clientUsesFlexMessaging) {
            return $this;
        }
    }

    /**
     * handle the request message instead of letting the Amf Handler do it.
     * @param Amfphp_Core_Amf_Message $requestMessage
     * @param Amfphp_Core_Common_ServiceRouter $serviceRouter
     * @return Amfphp_Core_Amf_Message
     */
    public function handleRequestMessage(Amfphp_Core_Amf_Message $requestMessage, Amfphp_Core_Common_ServiceRouter $serviceRouter) {
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $messageType = $requestMessage->data[0]->$explicitTypeField;
        $messageIdField = self::FIELD_MESSAGE_ID;
        $this->lastFlexMessageId = $requestMessage->data[0]->$messageIdField;
        $this->lastFlexMessageResponseUri = $requestMessage->responseUri;


        if ($messageType == self::FLEX_TYPE_COMMAND_MESSAGE) {
            //command message. An empty AcknowledgeMessage is expected.
            $acknowledge = new AmfphpFlexMessaging_AcknowledgeMessage($requestMessage->data[0]->$messageIdField);
            return new Amfphp_Core_Amf_Message($requestMessage->responseUri . Amfphp_Core_Amf_Constants::CLIENT_SUCCESS_METHOD, null, $acknowledge);
        }


        if ($messageType == self::FLEX_TYPE_REMOTING_MESSAGE) {
            //remoting message. An AcknowledgeMessage with the result of the service call is expected.
            $remoting = $requestMessage->data[0];
            $serviceCallResult = $serviceRouter->executeServiceCall($remoting->source, $remoting->operation, $remoting->body);
            $acknowledge = new AmfphpFlexMessaging_AcknowledgeMessage($remoting->$messageIdField);
            $acknowledge->body = $serviceCallResult;
            return new Amfphp_Core_Amf_Message($requestMessage->responseUri . Amfphp_Core_Amf_Constants::CLIENT_SUCCESS_METHOD, null, $acknowledge);
        }
        throw new Amfphp_Core_Exception('unrecognized flex message');
    }

    /**
     * flex expects error messages formatted in a special way, using the ErrorMessage object.
     * @return Amfphp_Core_Amf_Packet
     * @param Exception $exception
     */
    public function generateErrorResponse(Exception $exception) {
        $error = new AmfphpFlexMessaging_ErrorMessage($this->lastFlexMessageId);
        $error->faultCode = $exception->getCode();
        $error->faultString = $exception->getMessage();
        if ($this->returnErrorDetails) {
            $error->faultDetail = $exception->getTraceAsString();
            $error->rootCause = $exception;
        }
        $errorMessage = new Amfphp_Core_Amf_Message($this->lastFlexMessageResponseUri . Amfphp_Core_Amf_Constants::CLIENT_FAILURE_METHOD, null, $error);
        $errorPacket = new Amfphp_Core_Amf_Packet();
        $errorPacket->messages[] = $errorMessage;
        return $errorPacket;
    }

}

?>
