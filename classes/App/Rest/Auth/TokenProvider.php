<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 22.08.2014
 * Time: 15:35
 */


namespace App\Rest\Auth;


use App\Exception\HttpException;
use App\Exception\UnauthorizedException;
use App\Model\User;

class TokenProvider extends Provider
{
    const TOKEN_NAME = '_rest_token';
    const SALT = 'asdfnm79as8dna0t6f73w6';

    protected $sessionStarted = false;

    public function authenticate()
    {
        // Ensure headers are lower-case
        $headers = array_change_key_case(getallheaders());
        $token = null;

        // Fetch token from headers or query string.
        if ($headers['authorization'] && strpos($headers['authorization'], 'Token ') === 0) {
            $parts = preg_split('/\s+/', $headers['authorization'], 2, PREG_SPLIT_NO_EMPTY);
            $token = $parts[1];

        } else if ($this->controller->request->get('_token')) {
            $token = $this->controller->request->get('_token');
        }

        //error_log("Rest Token: " . $token);
        // If token is correct, just proceed request.
        if ($token) {
            /** @var User $user */
            $user = $this->pixie->orm->get('User')->where('rest_token', $token)->find();
            if(!$user->loaded()) {
                throw new UnauthorizedException();
            }

            $this->controller->setUser($user);
            return;

        }

        // Else require basic authorization request from client to get token.
        if ($this->controller->request->param('controller') == 'auth') {
            /**
             * @var User $user
             * @var boolean $logged
             */
            list($user, $logged) = array_values($this->requireBasicCredentials());

            if ($logged) {
                $this->controller->setUser($user);
                if (!$user->rest_token || $this->controller->request->get('refresh')) {
                    $token = sha1($user->username . time() . self::SALT);
                    $user->rest_token = $token;
                    $user->save();
                } else {
                    $token = $user->rest_token;
                }
                $responseException = new HttpException('Your token is established.', 200, null, 'OK');
                $responseException->setParameter('token', $token);
                throw $responseException;
            }
        }

        $this->askForBasicCredentials("Please provide your credentials using url /api/auth");
    }
} 
