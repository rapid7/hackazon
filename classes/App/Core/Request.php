<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 07.08.2014
 * Time: 10:49
 */


namespace App\Core;

use App\EventDispatcher\Events;
use App\Events\GetResponseEvent;
use App\Exception\HttpException;
use App\Helpers\HttpHelper;
use App\Pixie;

/**
 * Class Request
 * @package App\Core
 * @property Pixie $pixie
 * @inheritdoc
 */
class Request extends \PHPixie\Request
{
    protected $rawInputData = null;
    protected $adjustedRawInputData = null;

    /**
     * @inheritdoc
     */
    public function get($key = null, $default = null, $filter_xss = false)
    {
        return parent::get($key, $default, $filter_xss);
    }

    /**
     * @inheritdoc
     */
    public function post($key = null, $default = null, $filter_xss = false)
    {
        return parent::post($key, $default, $filter_xss);
    }

    /**
     * @inheritdoc
     */
    public function param($key = null, $default = null, $filter_xss = false)
    {
        return parent::param($key, $default, $filter_xss);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $event = new GetResponseEvent($this, $this->_cookie);
        $this->pixie->dispatcher->dispatch(Events::KERNEL_PRE_EXECUTE, $event);

        if ($event->getResponse()) {
            return $event->getResponse();
        }
        return parent::execute();
    }

    public function rawRequestData()
    {
        if ($this->rawInputData === null) {
            $rawInput = fopen('php://input', 'r');
            $data = '';

            while ($block = fread($rawInput, 1024)) {
                $data .= $block;
            }

            fclose($rawInput);
            $this->rawInputData = $data;
        }

        return $this->rawInputData;
    }

    /**
     * @return null
     */
    public function getAdjustedRawInputData()
    {
        if ($this->adjustedRawInputData === null) {
            parse_str($this->rawRequestData(), $this->adjustedRawInputData);
            if (!is_array($this->adjustedRawInputData)) {
                $this->adjustedRawInputData = [];
            }
        }
        return $this->adjustedRawInputData;
    }

    public function put($key = null, $default = null, $filter_xss = false)
    {
        return $this->get_filtered_value($this->getAdjustedRawInputData(), $key, $default, $filter_xss);
    }

    public function getRequestData($key = null, $default = null, $filter_xss = false)
    {
        switch ($this->method) {
            case 'GET':
                return $this->get($key, $default, $filter_xss);

            case 'POST':
                return $this->post($key, $default, $filter_xss);

            default:
                return $this->put($key, $default, $filter_xss);
        }
    }

    /**
     * Changes request body to array if it is not url-encoded (but in json, xml formats).
     */
    public function adjustRequestContentType()
    {
        if (in_array($this->method, ['GET', 'DELETE', 'HEAD', 'OPTIONS'])) {
            return;
        }

        if ($this->method == 'POST') {
            $fieldName = '_post';
        } else {
            $fieldName = 'adjustedRawInputData';
        }

        $contentType = HttpHelper::cleanContentType($this->server('CONTENT_TYPE'));

        if ($contentType == 'application/json') {
            $this->$fieldName = json_decode($this->rawRequestData(), true);
            if ($this->rawRequestData() && strpos(trim($this->rawRequestData()), '{') === 0 && !$this->$fieldName) {
                throw new HttpException('Request data are malformed. Please check it.', 400, null, 'Bad Request');
            }

        } else if ($contentType == 'application/xml') {
            $xml = simplexml_load_string($this->rawRequestData());
            if ($this->rawRequestData() && $xml === false) {
                throw new HttpException('Request data are malformed. Please check it.', 400, null, 'Bad Request');
            }
            $this->$fieldName = json_decode(json_encode($xml), true);
        }

        $this->$fieldName = is_array($this->$fieldName) ? $this->$fieldName : [];
    }
}