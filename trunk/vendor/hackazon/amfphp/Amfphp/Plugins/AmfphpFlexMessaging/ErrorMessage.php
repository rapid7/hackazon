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
 * Used to generate a Flex Error message.
 * part of the AmfphpFlexMessaging plugin
 *
 * @package Amfphp_Plugins_FlexMessaging
 * @author Ariel Sommeria-Klein
 */
class AmfphpFlexMessaging_ErrorMessage {

    /**
     * correlation id. guid
     * @var string 
     */
    public $correlationId;

    /**
     * fault code
     * @var float 
     */
    public $faultCode;

    /**
     * fault detail
     * @var string 
     */
    public $faultDetail;

    /**
     * fault string
     * @var string 
     */
    public $faultString;

    /**
     * an object describing the cause. Whatever you need.
     * @var mixed 
     */
    public $rootCause;

    /**
     * constructor
     * @param type $correlationId
     */
    public function __construct($correlationId) {
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $this->$explicitTypeField = AmfphpFlexMessaging::FLEX_TYPE_ERROR_MESSAGE;
        $this->correlationId = $correlationId;
    }

}

?>
