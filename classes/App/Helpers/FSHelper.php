<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 29.08.2014
 * Time: 14:50
 */


namespace App\Helpers;


class FSHelper 
{
    public static function cleanFileName($fileName, $length = null)
    {
        $name = preg_replace('/[^\w\d_]/u', '_', $fileName);
        $name = preg_replace('/_+/', '_', $name);
        if ($length === null) {
            //return substr(iconv("UTF-8", "ISO-8859-9//TRANSLIT", $name), 0);
            return substr($name, 0);
        } else {
            //return substr(iconv("UTF-8", "ISO-8859-9//TRANSLIT", $name), 0, $length);
            return substr($name, 0, $length);
        }
    }
} 