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
            $requestBody = $this->rawRequestData();

            // Inject XMLExternalEntity vulnerability
            $vuln = $this->pixie->vulnService->getVulnerability('XMLExternalEntity');
            $protected = $vuln !== true && !$vuln['enabled'];

            if ($protected) {
                libxml_disable_entity_loader(true);
            }

            try {
                $xml = simplexml_load_string($requestBody);
            } catch (\Exception $e) {
                if ($protected) {
                    throw new HttpException('Invalid XML Body.', 400, $e, 'Bad Request');
                } else {
                    throw $e;
                }
            }

            if ($requestBody && $xml === false) {
                throw new HttpException('Request data are malformed. Please check it.', 400, null, 'Bad Request');
            }
            $this->$fieldName = json_decode(json_encode($xml), true);

        } else if ($fieldName == 'adjustedRawInputData') {
            $this->rawRequestData();
            $this->$fieldName = $this->parseRawHttpRequest();
        }

        $this->$fieldName = is_array($this->$fieldName) ? $this->$fieldName : [];
    }

    public function getCookie()
    {
        return $this->_cookie;
    }

    public function isAdminPath()
    {
        return $this->param('namespace') == 'App\\Admin\\';
    }

    /**
     * @param $name
     * @param array $params
     * @return UploadedFile
     */
    public function uploadedFile($name, array $params = [])
    {
        return new UploadedFile($this->pixie, $name, $params);
    }

    /**
     * RAW Request data parser
     * @see http://www.chlab.ch/blog/archives/php/manually-parse-raw-http-data-php
     * @return array
     */
    function parseRawHttpRequest()
    {
        $aData = [];

        // read incoming data
        $input = $this->rawInputData;

        // grab multipart boundary from content type header
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);

        // content type is probably regular form-encoded
        if (!count($matches))
        {
            // we expect regular puts to contain a query string containing data
            parse_str(urldecode($input), $aData);
            return $aData;
        }

        $boundary = $matches[1];

        // split content by boundary and get rid of last -- element
        $aBlocks = preg_split("/-+$boundary/", $input);
        array_pop($aBlocks);

        // loop data blocks
        foreach ($aBlocks as $id => $block)
        {
            if (empty($block))
                continue;

            // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visible char

            // parse uploaded files
            if (strpos($block, 'application/octet-stream') !== FALSE)
            {
                // match "name", then everything after "stream" (optional) except for prepending newlines
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
                $aData['files'][$matches[1]] = $matches[2];
            }
            // parse all other fields
            else
            {
                // match "name" and optional value in between newline sequences
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
                $aData[$matches[1]] = $matches[2];
            }
        }

        return $aData;
    }
}