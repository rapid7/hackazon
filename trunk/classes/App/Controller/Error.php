<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.08.2014
 * Time: 11:53
 */


namespace App\Controller;


use App\Exception\HttpException;
use App\Page;

class Error extends Page
{
    public function action_view()
    {
        $exception = $this->request->param('exception', null, false);
        $status = method_exists($exception, 'getStatus')
            ? $exception->getStatus() : $exception->getCode() . ' ' .$exception->getMessage();

        header($this->request->server("SERVER_PROTOCOL").' '.$status);
        header("Status: {$status}");

        $isAdmin = $exception instanceof HttpException
            && $exception->getParameter('request')
            && $exception->getParameter('request')->isAdminPath()
            && $this->pixie->auth->has_role('admin');

        if ($isAdmin) {
            $this->view = $this->pixie->view('admin/error/view');
            $this->view->adminRoot = '/admin';
        } else {
            $this->view->subview = 'error/view';
        }
        $this->view->exception = $exception;
        $this->view->pageTitle = 'Error: ' . $exception->getCode() . ' ' . $exception->getMessage();
    }
} 