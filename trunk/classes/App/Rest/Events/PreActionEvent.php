<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 20.08.2014
 * Time: 10:43
 */


namespace App\Rest\Events;


use App\EventDispatcher\Event;
use App\Rest\Controller;
use PHPixie\Request;

class PreActionEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Controller
     */
    protected $controller;

    function __construct(Request $request, Controller $controller)
    {
        $this->request = $request;
        $this->controller = $controller;
    }

    /**
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }
} 