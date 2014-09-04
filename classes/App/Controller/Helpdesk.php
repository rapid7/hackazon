<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 03.09.2014
 * Time: 18:42
 */


namespace App\Controller;


use App\Page;

class Helpdesk extends Page
{
    public function action_index()
    {
        $this->view->headScripts = "<script type=\"text/javascript\" src=\"helpdesk.nocache.js\"></script>";
        $this->view->subview = "helpdesk/index";
    }

    public function action_HelpdeskService() {
        $servlet = $this->pixie->gwt->getServlet();
        $servlet->start();
        exit;
    }
} 