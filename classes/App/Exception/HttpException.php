<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.08.2014
 * Time: 11:20
 */


namespace App\Exception;


class HttpException extends \Exception
{
    protected $statusMessage = '';

    protected $data = [];

    public function __construct($message = '', $code = 0, \Exception $previous = null, $statusMessage = '')
    {
        $tmp = $this->message;
        parent::__construct($message, $code, $previous);

        $this->message = $message ?: $tmp;

        $this->statusMessage = $statusMessage;
    }

    public function getStatus()
    {
        return $this->getCode() . ' ' . ($this->statusMessage ?: $this->getMessage());
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $name
     * @param $value
     * @internal param array $data
     */
    public function setParameter($name, $value)
    {
        $this->data[$name] = $value;
    }
}