<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 11.08.2014
 * Time: 14:19
 */


namespace VulnModule\Csrf;

/**
 * Class CsrfTokenManager
 * @package VulnModule\Csrf
 */
class CsrfTokenManager
{
    /**
     * @var array
     */
    protected $symbolArray;

    /**
     * @var TokenStorage
     */
    private $storage;

    /**
     * Creates a new CSRF provider using PHP's native session storage.
     *
     * @param null|\VulnModule\Csrf\TokenStorage $storage The storage for storing generated CSRF tokens
     */
    public function __construct(TokenStorage $storage = null)
    {
        $this->storage = $storage ?: new TokenStorage();
    }

    /**
     * @param $tokenId
     * @return CsrfToken
     */
    public function getToken($tokenId)
    {
        if ($this->storage->hasToken($tokenId)) {
            $value = $this->storage->getToken($tokenId);
        } else {
            $value = $this->generateToken();

            $this->storage->setToken($tokenId, $value);
        }

        return new CsrfToken($tokenId, $value);
    }

    /**
     * @param $tokenId
     * @return CsrfToken
     */
    public function refreshToken($tokenId)
    {
        $value = $this->generateToken();

        $this->storage->setToken($tokenId, $value);

        return new CsrfToken($tokenId, $value);
    }

    /**
     * @param $tokenId
     * @return null|string
     */
    public function removeToken($tokenId)
    {
        return $this->storage->removeToken($tokenId);
    }

    /**
     * @param $token
     * @return bool
     */
    public function isTokenValid(CsrfToken $token)
    {
        if (!$this->storage->hasToken($token->getId())) {
            return false;
        }

        return $this->storage->getToken($token->getId()) == $token->getValue();
    }

    /**
     *
     * @return string
     */
    public function generateToken()
    {
        srand();
        $arr = $this->getSymbolArray();
        $keys = array_rand($arr, 32);
        $res = [];
        foreach ($keys as $key) {
            $res[] = $arr[$key];
        }

        shuffle($res);
        return implode('', $res);
    }

    /**
     * Creates symbol array used to generate token names.
     * @return array
     */
    protected function getSymbolArray()
    {
        if (!$this->symbolArray) {
            $str = 'abcdefghijklmnopqrstuvwzyz';
            $str .= strtoupper($str);
            $this->symbolArray = preg_split('//', str_repeat($str . '0123456789', 5), -1, PREG_SPLIT_NO_EMPTY);
        }
        return $this->symbolArray;
    }
}
