<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 21.08.2014
 * Time: 11:06
 */


namespace App\Helpers;


class HttpHelper 
{
    public static function cleanContentType($accept)
    {
        return preg_replace('#^([^/]+/)(.*?\+)?(.*?)(;.*)?$#i', '$1$3', $accept);
    }
} 