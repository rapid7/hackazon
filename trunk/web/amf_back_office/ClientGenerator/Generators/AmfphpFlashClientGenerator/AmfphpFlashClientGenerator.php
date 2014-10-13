<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice_Generators
 */

/**
 * generates a Flash project for consumption of amfPHP services
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Backoffice_Generators
 */
class AmfphpFlashClientGenerator extends Amfphp_BackOffice_ClientGenerator_LocalClientGenerator {

    /**
     * constructor
     */
    public function __construct() {
        parent::__construct(array('as'), dirname(__FILE__) . '/Template');
    }

    /**
     * get ui call text
     * @return string
     */
    public function getUiCallText() {
        return "Flash Creative Suite";
    }

    /**
     * info url
     * @return string
     */
    public function getInfoUrl() {
        return "http://www.silexlabs.org/amfphp/documentation/client-generators/flash-creative-suite/";
    }
    
    /**
     * multiplies and adapts the code for each parameter, but adds a comma between each.
     * duplicated and enhanced to add typing. Right now vo typing is not supported, only primitive types, like int, boolean, string etc.
     * @param type $code
     */
    protected function processParameterCommaListBlock($code) {
        $ret = '';
        foreach ($this->methodBeingProcessed->parameters as $parameter) {
            $blockForParameter = str_replace(self::_PARAMETER_, $parameter->name, $code);
            $as3Type = null;
            switch(strtolower($parameter->type)){
                case 'string':
                    $as3Type = 'String';
                break;
                case 'bool':
                case 'boolean':
                    $as3Type = 'Boolean';
                break;
                case 'int':
                    $as3Type = 'int';
                break;
                case 'uint':
                    $as3Type = 'uint';
                break;
                case 'float':
                case 'number':
                    $as3Type = 'Number';
                break;
            }
            if($as3Type){
                $blockForParameter = str_replace('Object', $as3Type, $blockForParameter);
            }
            $ret .= $blockForParameter . ', ';
        }
        //remove last comma
        $ret = substr($ret, 0, -2);
        return $ret;
    }



}

?>
