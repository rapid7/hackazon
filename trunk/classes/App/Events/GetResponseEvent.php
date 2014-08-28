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
use App\Exception\HttpException;

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

    /**
     * @var \Exception
     */
    protected $exception;

    protected $cookie;

    public function __construct(Request $request = null, $cookie = [])
    {
        $this->request = $request;
        $this->cookie = $cookie;
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

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param \Exception $exception
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }
} 