<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 11.08.2014
 * Time: 14:09
 */


namespace VulnModule\Csrf;


use App\Exception\HttpException;

/**
 * Class TokenStorage
 * @package VulnModule\Csrf
 */
class TokenStorage
{
    /**
     * The namespace used to store values in the session.
     * @var string
     */
    const SESSION_NAMESPACE = '_csrf';

    /**
     * @var bool
     */
    private $sessionStarted = false;

    /**
     * @var string
     */
    private $namespace;

    /**
     * Initializes the storage with a session namespace.
     *
     * @param string  $namespace The namespace under which the token is stored
     *                           in the session
     */
    public function __construct($namespace = self::SESSION_NAMESPACE)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param $tokenId
     * @return string
     * @throws \App\Exception\HttpException
     */
    public function getToken($tokenId)
    {
        if (!$this->sessionStarted) {
            $this->startSession();
        }

        if (!isset($_SESSION[$this->namespace][$tokenId])) {
            throw new HttpException('The CSRF token with ID '.$tokenId.' does not exist.', 400, null, 'Bad request');
        }

        return (string) $_SESSION[$this->namespace][$tokenId];
    }

    /**
     * @param $tokenId
     * @param $token
     */
    public function setToken($tokenId, $token)
    {
        if (!$this->sessionStarted) {
            $this->startSession();
        }

        $_SESSION[$this->namespace][$tokenId] = (string) $token;
    }

    /**
     * @param $tokenId
     * @return bool
     */
    public function hasToken($tokenId)
    {
        if (!$this->sessionStarted) {
            $this->startSession();
        }

        return isset($_SESSION[$this->namespace][$tokenId]);
    }

    /**
     * @param $tokenId
     * @return null|string
     */
    public function removeToken($tokenId)
    {
        if (!$this->sessionStarted) {
            $this->startSession();
        }

        $token = isset($_SESSION[$this->namespace][$tokenId])
            ? (string) $_SESSION[$this->namespace][$tokenId]
            : null;

        unset($_SESSION[$this->namespace][$tokenId]);

        return $token;
    }

    /**
     * PHP Version-dependent session start
     */
    private function startSession()
    {
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            if (PHP_SESSION_NONE === session_status()) {
                session_start();
            }
        } elseif (!session_id()) {
            session_start();
        }

        $this->sessionStarted = true;
    }
}