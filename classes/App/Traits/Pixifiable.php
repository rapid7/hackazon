<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.09.2014
 * Time: 10:34
 */


namespace App\Traits;
use App\Pixie;

/**
 * Makes a class to be friendly with Pixie
 * @package App\Traits
 */
trait Pixifiable
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @return Pixie
     * @amfphpHide
     */
    public function getPixie()
    {
        return $this->pixie;
    }

    /**
     * @param Pixie $pixie
     * @amfphpHide
     */
    public function setPixie(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }
} 