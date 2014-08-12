<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.08.2014
 * Time: 14:28
 */


namespace App\Exception;


class InternalErrorException extends HttpException
{
    protected $code = 500;
    protected $message = 'Internal Server Error';
    protected $statusMessage = 'Internal Server Error';
}