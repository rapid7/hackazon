<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice
 * 
 */

/**
 *controls access to back office, along with SignIn, SignOut scripts
 * 
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Backoffice
 */
class Amfphp_BackOffice_AccessManager {
    /**
     * the field in the session where the roles array is stored
     */

    const SESSION_FIELD_ROLES = 'amfphp_roles';
    const AMFPHP_ADMIN_ROLE = 'amfphp_admin';

    /**
     * checks if access should be granted, either because no sign in is required, or because the user is actually signed in.
     * note: must be called before output starts, as starting a session can change headers on some configs.
     */
    public function isAccessGranted() {
        
        $config = new Amfphp_BackOffice_Config();
        if(!$config->requireSignIn){
            return true;
        }
        if (session_id() == '') {
            session_start();
        }

        
        if (!isset($_SESSION[self::SESSION_FIELD_ROLES])) {
            return false;
        }
        return isset($_SESSION[self::SESSION_FIELD_ROLES][self::AMFPHP_ADMIN_ROLE]);

    }

}

?>
