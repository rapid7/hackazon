<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 17.09.2014
 * Time: 13:10
 */


namespace App\Admin\Controller;


use App\Admin\Controller;

class Error extends Controller
{
    protected $checkSessionId = false;

    public function action_view()
    {
        $exception = $this->request->param('exception', null, false);
        $status = method_exists($exception, 'getStatus')
            ? $exception->getStatus() : $exception->getCode() . ' ' .$exception->getMessage();

        header($this->request->server("SERVER_PROTOCOL").' '.$status);
        header("Status: {$status}");

        $this->view->set_template('admin/error/view');
        $this->view->pageHeader = 'Error';
        $this->view->exception = $exception;
        $this->view->pageTitle = 'Error: ' . $exception->getCode() . ' ' . $exception->getMessage();
    }
} 