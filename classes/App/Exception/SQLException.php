<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 27.01.2015
 * Time: 13:56
 */


namespace App\Exception;


use Exception;

class SQLException extends \Exception
{
    protected $vulnerable = false;

    protected $blind = true;

    public function __construct($message = "", $code = 0, Exception $previous = null, $isVulnerable = false, $isBlind = true)
    {
        parent::__construct($message, $code, $previous);

        $this->vulnerable = $isVulnerable;
        $this->blind = $isBlind;
    }

    /**
     * @return boolean
     */
    public function isVulnerable()
    {
        return $this->vulnerable;
    }

    /**
     * @param boolean $vulnerable
     */
    public function setVulnerable($vulnerable)
    {
        $this->vulnerable = $vulnerable;
    }

    /**
     * @return boolean
     */
    public function isBlind()
    {
        return $this->blind;
    }

    /**
     * @param boolean $blind
     */
    public function setBlind($blind)
    {
        $this->blind = $blind;
    }
}