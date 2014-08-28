<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 20.08.2014
 * Time: 12:32
 */


namespace App\Exception;


class UnauthorizedException extends HttpException
{
    protected $code = 401;
    protected $message = "You must be authenticated to access this resource.";
    protected $statusMessage = "Unauthorized";
} 