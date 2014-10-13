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
 * Sign in dialog If not checks POST data for login credentials.
 * throws Exception containing user feedback
 * @todo fix session roles to use a dictionary approach, here and in AmfphpAuthentication plugin.
 * Wait for bigger version to break compatibility
 * @author Ariel Sommeria-klein
 *
 */
/**
 * includes
 */
require_once(dirname(__FILE__) . '/ClassLoader.php');

if (session_id() == '') {
    session_start();
}


if (isset($_SESSION[Amfphp_BackOffice_AccessManager::SESSION_FIELD_ROLES])) {
    unset($_SESSION[Amfphp_BackOffice_AccessManager::SESSION_FIELD_ROLES][Amfphp_BackOffice_AccessManager::AMFPHP_ADMIN_ROLE]);
}
?>
<script>
    window.location = './SignIn.php';
</script>