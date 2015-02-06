<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 03.09.2014
 * Time: 18:42
 */


namespace App\Controller;


use App\Page;
use VulnModule\Config\Annotations as Vuln;

/**
 * Class Helpdesk
 * @package App\Controller
 * @Vuln\Description("GWT entry point.")
 */
class Helpdesk extends Page
{
    public function before()
    {
        parent::before();
        if (!$this->execute) {
            return;
        }
    }

    public function action_index()
    {
        $this->view->headScripts = '<script type="text/javascript" src="helpdesk.nocache.js"></script>';
        $this->view->subview = "helpdesk/index";
    }

    public function action_HelpdeskService() {
        $this->vulninjection->goUp()->goUp();
        $this->vulninjection->loadAndAddChildContext('gwt');
        $this->vulninjection->goDown('gwt');

        $servlet = $this->pixie->gwt->getServlet();
        $servlet->setRequest($this->request);
        $servlet->start();
        exit;
    }
} 