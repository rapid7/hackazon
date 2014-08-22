<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 22.08.2014
 * Time: 15:23
 */


namespace App\Rest\Auth;
use App\Pixie;


/**
 * Fetches needed REST auth provider.
 * @package App\Rest\Auth
 */
class AuthFactory 
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var Provider[]
     */
    protected $instances = [];

    function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    /**
     * @param string $name Provider name (basic, token, etc.)
     * @return Provider
     * @throws \Exception
     */
    public function get($name)
    {
        if (!$this->instances[$name]) {
            $className = preg_replace('#(\\\\)[^\\\\]+$#', '$1', __CLASS__).ucfirst($name).'Provider';
            if (!class_exists($className)) {
                throw new \Exception('Invalid authentication method for REST service.');
            }

            $this->instances[$name] = new $className($this->pixie);
        }

        return $this->instances[$name];
    }
} 