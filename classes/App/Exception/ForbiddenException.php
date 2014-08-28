<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 31.07.2014
 * Time: 16:05
 */


namespace App\Exception;


class ForbiddenException extends HttpException
{
    protected $code = 403;
    protected $message = "You must be authorized to access this resource.";
    protected $statusMessage = "Forbidden";
}