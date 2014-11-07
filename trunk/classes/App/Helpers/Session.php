<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 05.11.2014
 * Time: 16:17
 */


namespace App\Helpers;


class Session 
{
    protected static $sessionStarted = false;

    public static function checkSessionStarted() {
        if (self::$sessionStarted) {
            return true;
        }
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            if (PHP_SESSION_NONE === session_status()) {
                session_start();
            }
        } elseif (!session_id()) {
            session_start();
        }

        self::$sessionStarted = true;
        return self::$sessionStarted;
    }
} 