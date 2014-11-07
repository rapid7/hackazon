<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 05.11.2014
 * Time: 17:44
 */


namespace App;


class Pixifier 
{
    /**
     * @var Pixie
     */
    private $pixie;

    private function __construct()
    {
    }

    /**
     * @return Pixifier
     */
    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * @return Pixie
     */
    public function getPixie()
    {
        return $this->pixie;
    }

    /**
     * @param Pixie $pixie
     */
    public function setPixie($pixie)
    {
        $this->pixie = $pixie;
    }
}