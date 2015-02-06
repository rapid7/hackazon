<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 22.01.2015
 * Time: 13:04
 */


namespace App\Core;


use PHPixie\Session as BaseSession;

class Session extends BaseSession
{
    /**
     * Gets ot sets flash messages.
     * If the value parameter is passed the message is set, otherwise it is retrieved.
     * After the message is retrieved for the first time it is removed.
     *
     * @param string $key The name of the flash message
     * @param string $val Flash message content
     * @return mixed
     */
    public function flash($key, $val = null)
    {
        return parent::flash($key, $val);
    }
}