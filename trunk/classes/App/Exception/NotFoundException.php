<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.08.2014
 * Time: 12:22
 */


namespace App\Exception;


class NotFoundException extends HttpException
{
    protected $code = 404;
    protected $message = "Not Found";
    protected $statusMessage = "Not Found";
}