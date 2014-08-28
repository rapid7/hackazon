<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 13:51
 */


namespace App\Admin\Controller;


use App\Admin\Controller;

class Home extends Controller
{
    public function action_index()
    {
        $this->view->subview = 'home/dashboard';
        $this->view->message = "Index page";
    }
} 