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
 * Authentication for Amfphp. 
 * This plugin can be deactivated if the project doesn't need to protect access to its services.
 * 
 * On a service object, the plugin looks for a method called _getMethodRoles. If the method exists, the plugin will look for a role in the session that matches the role.
 * If the roles don't match, an Exception is thrown.
 * The _getMethodRoles takes a parameter $methodName, and must return an array of strings containing acceptable roles for the method. If the return value is null,
 * it is considered that that particular method is not protected.
 * 
 * For example:
 * <code>
 * public function _getMethodRoles($methodName){
 *    if($methodName == 'adminMethod'){
 *        return array('admin');
 *    }else{
 *        return null;
 *    }
 * }
 *
 * </code>
 * 
 * To authenticate a user, the plugin looks for a 'login' method. This method can either be called
 * explicitly, or by setting a header with the name 'Credentials', containing {userid: userid, password: password}, as defined by the AS2
 * NetConnection.setCredentials method. It is considered good practise to have a 'logout' method, though this is optional
 * The login method returns a role in a 'string'. It takes 2 parameters, the user id and the password.
 * The logout method should call AmfphpAuthentication::clearSessionInfo();
 * 
 * See the AuthenticationService class in the test data for an example of an implementation.
 * 
 * Roles are stored in an associative array in $_SESSION[self::SESSION_FIELD_ROLES], using the role as key for easy access
 * 
 * @link https://github.com/silexlabs/amfphp-2.0/blob/master/Tests/TestData/Services/AuthenticationService.php
 * @package Amfphp_Plugins_Authentication
 * @author Ariel Sommeria-klein
 */
class AmfphpAuthentication {
    /**
     * the field in the session where the roles array is stored
     */

    const SESSION_FIELD_ROLES = 'amfphp_roles';

    /**
     * the name of the method on the service where the method roles are given
     */
    const METHOD_GET_METHOD_ROLES = '_getMethodRoles';

    /**
     * the name of the login method
     */
    const METHOD_LOGIN = 'login';

    /**
     * the user id passed in the credentials header
     * @var String
     */
    public $headerUserId;

    /**
     * the password passed in the credentials header
     * @var String
     */
    protected $headerPassword;

    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function __construct(array $config = null) {
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Common_ServiceRouter::FILTER_SERVICE_OBJECT, $this, 'filterServiceObject');
        $filterManager->addFilter(Amfphp_Core_Amf_Handler::FILTER_AMF_REQUEST_HEADER_HANDLER, $this, 'filterAmfRequestHeaderHandler');
        $this->headerUserId = null;
        $this->headerPassword = null;
    }

    /**
     * filter amf request header handler
     * @param Object $handler
     * @param Amfphp_Core_Amf_Header $header the request header
     * @return AmfphpAuthentication 
     */
    public function filterAmfRequestHeaderHandler($handler, Amfphp_Core_Amf_Header $header) {
        if ($header->name == Amfphp_Core_Amf_Constants::CREDENTIALS_HEADER_NAME) {
            return $this;
        }
    }

    /**
     * called when the service object is created, just before the method call.
     * Tries to authenticate if a credentials header was sent in the packet.
     * Throws an exception if the roles don't match
     *
     * @param <Object> $serviceObject
     * @param <String> $serviceName
     * @param <String> $methodName
     * @return <array>
     */
    public function filterServiceObject($serviceObject, $serviceName, $methodName) {
        if (!method_exists($serviceObject, self::METHOD_GET_METHOD_ROLES)) {
            return;
        }

        if ($methodName == self::METHOD_GET_METHOD_ROLES) {
            throw new Exception('_getMethodRoles method access forbidden');
        }

        //the service object has a '_getMethodRoles' method. role checking is necessary if the returned value is not null
        $methodRoles = call_user_func(array($serviceObject, self::METHOD_GET_METHOD_ROLES), $methodName);
        if (!$methodRoles) {
            return;
        }

        //try to authenticate using header info if available
        if ($this->headerUserId && $this->headerPassword) {
            call_user_func(array($serviceObject, self::METHOD_LOGIN), $this->headerUserId, $this->headerPassword);
        }

        if (session_id() == '') {
            session_start();
        }

        if (!isset($_SESSION[self::SESSION_FIELD_ROLES])) {
            throw new Amfphp_Core_Exception('User not authenticated');
        }

        $userRoles = $_SESSION[self::SESSION_FIELD_ROLES];

        foreach ($methodRoles as $methodRole) {
            if (isset($userRoles[$methodRole])) {
                //a match is found
                return;
            }
        }
        throw new Amfphp_Core_Exception('Access denied.');
    }

    /**
     * clears the session info set by the plugin. Use to logout
     */
    public static function clearSessionInfo() {
        if (session_id() == '') {
            session_start();
        }
        if (isset($_SESSION[self::SESSION_FIELD_ROLES])) {
            unset($_SESSION[self::SESSION_FIELD_ROLES]);
        }
    }

    /**
     * add role
     * @param String $roleToAdd
     */
    public static function addRole($roleToAdd) {
        if (session_id() == '') {
            session_start();
        }
        if (!isset($_SESSION[self::SESSION_FIELD_ROLES])) {
            $_SESSION[self::SESSION_FIELD_ROLES] = array();
        }

        //role isn't already available. Add it.
        $_SESSION[self::SESSION_FIELD_ROLES][$roleToAdd] = true;
    }

    /**
     * looks for a 'Credentials' request header. If there is one, uses it to try to authentify the user.
     * @param Amfphp_Core_Amf_Header $header the request header
     * @return void
     */
    public function handleRequestHeader(Amfphp_Core_Amf_Header $header) {
        if ($header->name != Amfphp_Core_Amf_Constants::CREDENTIALS_HEADER_NAME) {
            throw new Amfphp_Core_Exception('not an authentication amf header. type: ' . $header->name);
        }
        $userIdField = Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_USERID;
        $passwordField = Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_PASSWORD;

        $userId = $header->data->$userIdField;
        $password = $header->data->$passwordField;

        if (session_id() == '') {
            session_start();
        }
        $this->headerUserId = $userId;
        $this->headerPassword = $password;
    }

}

?>
