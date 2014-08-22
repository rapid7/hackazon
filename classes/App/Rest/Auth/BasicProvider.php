<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 22.08.2014
 * Time: 14:59
 */


namespace App\Rest\Auth;


class BasicProvider extends Provider
{
    public function authenticate()
    {
        list($user, $logged) = $this->requireBasicCredentials();

        if ($logged) {
            $this->controller->setUser($user);
            return;
        }

        $this->askForBasicCredentials();
    }
} 