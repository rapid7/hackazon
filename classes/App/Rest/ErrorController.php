<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 20.08.2014
 * Time: 12:26
 */


namespace App\Rest;


use App\Exception\HttpException;

class ErrorController extends Controller
{
    /**
     * @var \Exception|HttpException
     */
    protected $error;

    public function action_show()
    {
        $status = $this->error instanceof HttpException ? $this->error->getStatus() : '500 Internal Server Error';
        $this->response->add_header('HTTP/1.1 '.$status);
        $this->response->body = ['message' => $this->error->getMessage(), 'code' => $this->error->getCode()];
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    public function before()
    {
        $this->prepareContentType();
    }
}