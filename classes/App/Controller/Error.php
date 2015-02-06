<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.08.2014
 * Time: 11:53
 */


namespace App\Controller;


use VulnModule\Config\Annotations as Vuln;
use App\Page;

class Error extends Page
{
    protected $checkSessionId = false;

    /**
     * @Vuln\Route(name = "error", params={"id": "<id>"})
     * @Vuln\Description("View: error/view.")
     */
    public function action_view()
    {
        $exception = $this->request->param('exception', null, false);
        $status = method_exists($exception, 'getStatus')
            ? $exception->getStatus() : $exception->getCode() . ' ' .$exception->getMessage();

        header($this->request->server("SERVER_PROTOCOL").' '.$status);
        header("Status: {$status}");

        $this->view->subview = 'error/view';
        $this->view->exception = $exception;
        $this->view->pageTitle = 'Error: ' . $exception->getCode() . ' ' . $exception->getMessage();
    }
} 