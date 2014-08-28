<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 14:59
 */


namespace App\EventDispatcher;


class Events 
{
    const KERNEL_PRE_EXECUTE = 'kernel.pre_execute';
    const KERNEL_PRE_HANDLE_EXCEPTION = 'kernel.pre_handle_exception';
}