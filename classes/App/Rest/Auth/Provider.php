<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 22.08.2014
 * Time: 14:57
 */


namespace App\Rest\Auth;


use App\Model\User;
use App\Pixie;
use App\Rest\Controller;
use PHPixie\Auth\Login\Password;

/**
 * Class Provider
 * @package App\Rest\Auth
 */
abstract class Provider
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var Controller
     */
    protected $controller;

    function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    public function authenticate()
    {
    }

    protected function requireBasicCredentials()
    {
        // Ensure headers are lower-case
        $headers = array_change_key_case(getallheaders());

        if (!$headers['authorization'] || strpos($headers['authorization'], 'Basic ') !== 0) {
            $this->askForBasicCredentials();
        }

        $parts = preg_split('/\s+/', $headers['authorization'], 2, PREG_SPLIT_NO_EMPTY);
        $credentials = explode(':', base64_decode($parts[1]));
        $username = $credentials[0];
        $password = $credentials[1];

        if (!$username) {
            $this->askForBasicCredentials();
        }

        /** @var User $user */
        $user = $this->pixie->orm->get('User')->where('username', $username)->find();

        if(!$user->loaded()) {
            $this->askForBasicCredentials();
        }

        /** @var Password $provider */
        $provider = $this->pixie->auth->provider('password');
        $logged = $provider->login($username, $password);

        return compact('user', 'logged');
    }

    protected function askForBasicCredentials($realm = "Provide your credentials.", $authMethod = "Basic")
    {
        header('WWW-Authenticate: '.$authMethod.' realm="'.$realm.'"');
        header('HTTP/1.1 401 Unauthorized', true, 401);
        exit;
    }

    /**
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     * @return $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }
} 
