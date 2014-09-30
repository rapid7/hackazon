<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.09.2014
 * Time: 13:32
 */


namespace App\Rest\Controller;


use App\Exception\NotFoundException;
use App\Rest\Controller;

class Auth extends Controller
{
    public function action_get()
    {
        throw new NotFoundException;
    }

    public function action_post($data = null)
    {
        throw new NotFoundException;
    }

    public function action_put($data = null)
    {
        throw new NotFoundException;
    }

    public function action_patch($data = null)
    {
        throw new NotFoundException;
    }

    public function action_delete()
    {
        throw new NotFoundException;
    }

    public function action_options()
    {
        throw new NotFoundException;
    }

    public function action_get_collection()
    {
        throw new NotFoundException;
    }
} 