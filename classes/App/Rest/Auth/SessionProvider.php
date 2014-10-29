<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 22.08.2014
 * Time: 14:59
 */


namespace App\Rest\Auth;


use App\Exception\UnauthorizedException;
use App\Model\User;

/**
 * Class SessionProvider
 * @package App\Rest\Auth
 */
class SessionProvider extends Provider
{
    public function authenticate()
    {
        /** @var User $user */
        $user = $this->pixie->auth->user();
        $logged = false;

        if ($user) {
            $this->controller->setUser($user);
            return;
        }

        if ($this->controller->request->param('controller') == 'auth') {
            list($user, $logged) = $this->requireBasicCredentials();
        }

        if ($logged) {
            $this->controller->setUser($user);
            return;
        }

        //throw new UnauthorizedException();
    }
} 