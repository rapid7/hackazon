<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 31.07.2014
 * Time: 16:05
 */


namespace App\Exception;


class Forbidden extends \Exception
{
    protected $message = "403 Forbidden. You must be authorized to access resource.";
} 