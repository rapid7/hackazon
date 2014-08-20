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
    protected $message = "401 Unauthorized. You must be authenticated to access resource.";
    protected $statusMessage = "Unauthorized";
} 