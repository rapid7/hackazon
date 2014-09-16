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
 *
 * place holder class for the variables necessary to make a service call
 *
 * @package Amfphp_Core_Common
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Common_ServiceCallParameters {


    /**
     * the name of the service. 
     * The service name can either be just the name of the class (TestService) or include a path(package/TestService)
     * separator for path can only be '/'
     *
     * @var String
     */
    public $serviceName;

    /**
     * the name of the method to execute on the service object
     * @var String
     */
    public $methodName;

    /**
     * the parameters to pass to the method being called on the service
     * @var <array>
     */
    public $methodParameters;

}
?>
