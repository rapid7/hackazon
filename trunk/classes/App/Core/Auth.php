<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 06.01.2015
 * Time: 15:17
 */


namespace App\Core;


use App\Model\User;
use PHPixie\Auth\Login\Facebook;
use PHPixie\Auth\Login\Password;
use PHPixie\Auth\Login\Provider;
use PHPixie\Auth\Login\Twitter;

class Auth extends \PHPixie\Auth
{
    /**
     * @param string $provider
     * @param string $config
     * @return Provider|Password|Facebook|Twitter A "\PHPixie\Auth\Login\Provider" subclass object
     */
    public function provider($provider, $config = 'default')
    {
        return parent::provider($provider, $config);
    }

    /**
     * @param string $config
     * @return \PHPixie\ORM\Model|User
     */
    public function user($config = 'default')
    {
        try {
            return parent::user($config);
        } catch (\Exception $e) {
            return null;
        }
    }
}