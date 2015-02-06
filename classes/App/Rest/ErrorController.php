<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 20.08.2014
 * Time: 12:26
 */


namespace App\Rest;


use App\Exception\HttpException;
use App\Exception\SQLException;

class ErrorController extends Controller
{
    protected $checkSessionId = false;

    /**
     * @var \Exception|HttpException
     */
    protected $error;

    public function action_show()
    {
        $status = $this->error instanceof HttpException ? $this->error->getStatus() : '500 Internal Server Error';
        $data = $this->error instanceof HttpException ? $this->error->getData() : [];
        $this->response->add_header('HTTP/1.1 ' . $status);

        $displayErrors = $this->pixie->getParameter('parameters.display_errors', false);
        $showErrors = false;

        if ($this->error instanceof HttpException) {
            $message = $this->error->getMessage();
            if  ($this->error->getCode() >= 400 || $this->error->getCode() < 100) {
                $showErrors = $displayErrors;
            }

        } else if ($this->error instanceof SQLException) {
            if ($this->error->isVulnerable() && !$this->error->isBlind()) {
                $showErrors = true;
                $message = $this->error->getMessage();
            } else {
                $message = "Error";
            }

        } else {
            $message = $this->error->getMessage();
            $showErrors = $displayErrors;
        }

        $this->response->body = array_merge([
            'message' => $message,
            'code' => $this->error->getCode(),
            'trace' => ($showErrors ? $this->error->getTraceAsString() : "")
        ], $data);
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