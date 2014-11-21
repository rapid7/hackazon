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
    const ORIGIN_PUBLIC = 'public';
    const ORIGIN_ADMIN = 'admin';

    protected $statusMessage = 'Bad Request';

    protected $code = 400;

    protected $data = [];

    /**
     * @var string Origin of the exception.
     */
    protected $origin = self::ORIGIN_PUBLIC;

    public function __construct($message = '', $code = 0, \Exception $previous = null, $statusMessage = null)
    {
        $tmp = $this->message;
        parent::__construct($message, $code, $previous);

        $this->message = $message ?: $tmp;

        if ($statusMessage !== null) {
            $this->statusMessage = $statusMessage;
        }
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
     */
    public function setParameter($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $origin
     * @throws \InvalidArgumentException
     */
    public function setOrigin($origin)
    {
        $validOrigins = [self::ORIGIN_PUBLIC, self::ORIGIN_ADMIN];
        if (!in_array($origin, $validOrigins)) {
            throw new \InvalidArgumentException("Origin \"$origin\" is not valid. You must use one of this:"
                . implode(', ', $validOrigins));
        }
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }
}