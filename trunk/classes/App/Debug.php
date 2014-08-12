<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.08.2014
 * Time: 14:10
 */


namespace App;

/**
 * Class Debug. Extended version of PHPixie Debug.
 * @package App
 */
class Debug extends \PHPixie\Debug
{
    /**
     * Dumps beautifully formatted vars.
     */
    public static function dump()
    {
        echo '<pre>';
        call_user_func_array('var_dump', func_get_args());
        echo '</pre>';
    }

    /**
     * Dumps and finishes the script.
     */
    public static function dumpx()
    {
        call_user_func_array('self::dump', func_get_args());
        exit;
    }

    /**
     * Removed exceptions on notices.
     */
    public function init()
    {
        set_error_handler(array($this, 'error_handler'), E_ALL ^ E_NOTICE);
    }
}