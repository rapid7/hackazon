<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.08.2014
 * Time: 12:22
 */


namespace App\Exception;


class RedirectException extends HttpException
{
    protected $code = 302;
    protected $message = "Found";
    protected $statusMessage = "Found";
    protected $location;

    function __construct($location, $code = 302)
    {
        $this->location = $location;
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
}