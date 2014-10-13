<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice_Generators
 * 
 */

/**
 * generates a Flash project for consumption of amfPHP services
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Backoffice_Generators
 */
class AmfphpHtmlClientGenerator extends Amfphp_BackOffice_ClientGenerator_LocalClientGenerator {

    /**
     * constructor
     */
    public function __construct() {
        parent::__construct(array('html', 'js'), dirname(__FILE__) . '/Template');
    }

    /**
     * get ui call text
     * @return string
     */
    public function getUiCallText() {
        return "HTML";
    }

    /**
     * info url
     * @return string
     */
    public function getInfoUrl() {
        return "http://www.silexlabs.org/amfphp/documentation/client-generators/html/";
    }

    /**
     * (non-PHPdoc)
     * @see ClientGenerator/Amfphp_BackOffice_ClientGenerator_LocalClientGenerator::getTestUrlSuffix()
     */
    public function getTestUrlSuffix() {
        return 'index.html';
    }

}

?>
