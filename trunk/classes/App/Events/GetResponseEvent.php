<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 15:10
 */


namespace App\Events;

use App\Core\Request;
use App\Core\Response;
use App\EventDispatcher\Event;

/**
 * Class GetResponseEvent
 * @package App\Events
 */
class GetResponseEvent extends Event
{
    /**
     * @var null|Response
     */
    protected $response = null;

    /**
     * @var Request
     */
    protected $request;

    protected $cookie;

    public function __construct(Request $request, $cookie = [])
    {
        $this->request = $request;
    }

    /**
     * @return null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param \App\Core\Response|null|\PHPixie\Response $response
     */
    public function setResponse(\PHPixie\Response $response)
    {
        $this->response = $response;

        $this->stopPropagation();
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getCookie()
    {
        return $this->cookie;
    }
} 