<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_BackOffice
 */

/**
 * include this to include amfphp client generator
 * note: this list could be generated. In the meantime maintain it manually. 
 * It would be nice to do this alphabetically, It seems however that an interface must be loaded before a class, so do as possible
 *
 * @author Ariel Sommeria-klein
 *
 */

/**
 * includes
 */
define( 'AMFPHP_BACKOFFICE_ROOTPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

//note this is duplicated from the front ClassLoader.php file
define( 'AMFPHP_VERSION', '2.2.1');

require_once AMFPHP_BACKOFFICE_ROOTPATH . 'Config.php'; 
require_once AMFPHP_BACKOFFICE_ROOTPATH .  'AccessManager.php';
require_once AMFPHP_BACKOFFICE_ROOTPATH . 'ClientGenerator/LocalClientGenerator.php';
require_once AMFPHP_BACKOFFICE_ROOTPATH . 'ClientGenerator/GeneratorManager.php';
require_once AMFPHP_BACKOFFICE_ROOTPATH . 'ClientGenerator/Util.php';
?>
