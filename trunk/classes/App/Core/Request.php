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
use VulnModule\Config\FieldDescriptor;
use VulnModule\VulnerableField;

/**
 * Class Request
 * @package App\Core
 * @property Pixie $pixie
 * @inheritdoc
 */
class Request extends \PHPixie\Request
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH = 'PATCH';
    const METHOD_HEAD = 'HEAD';
    const METHOD_OPTIONS = 'OPTIONS';

    protected $rawInputData = null;
    protected $adjustedRawInputData = null;

    /**
     * @var Request headers
     */
    protected $_headers;

    public function __construct($pixie, $route, $method = "GET", $post = [], $get = [], $param = [], $server = [], $cookie = [], $headers = [])
    {
        parent::__construct($pixie, $route, $method, $post, $get, $param, $server, $cookie);
        $this->_headers = $headers;
    }

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
     * @param null $key
     * @param null $default
     * @param bool $filter_xss
     * @return mixed
     */
    public function put($key = null, $default = null, $filter_xss = false)
    {
        return $this->get_filtered_value($this->getAdjustedRawInputData(), $key, $default, $filter_xss);
    }

    public function header($key = null, $default = null, $filter_xss = false) {
        return $this->get_filtered_value($this->_headers, $key, $default, $filter_xss);
    }

    public function getWrap($key = null, $default = null)
    {
        return $this->getWrappedValueOrArray(FieldDescriptor::SOURCE_QUERY, $key, $default);
    }

    public function postWrap($key = null, $default = null)
    {
        return $this->getWrappedValueOrArray(FieldDescriptor::SOURCE_BODY, $key, $default);
    }

    public function putWrap($key = null, $default = null)
    {
        return $this->getWrappedValueOrArray(FieldDescriptor::SOURCE_BODY, $key, $default);
    }

    public function patchWrap($key = null, $default = null)
    {
        return $this->getWrappedValueOrArray(FieldDescriptor::SOURCE_BODY, $key, $default);
    }

    public function cookieWrap($key = null, $default = null)
    {
        return $this->getWrappedValueOrArray(FieldDescriptor::SOURCE_COOKIE, $key, $default);
    }

    public function paramWrap($key = null, $default = null)
    {
        return $this->getWrappedValueOrArray(FieldDescriptor::SOURCE_PARAM, $key, $default);
    }

    public function headerWrap($key = null, $default = null)
    {
        return $this->getWrappedValueOrArray(FieldDescriptor::SOURCE_HEADER, $key, $default);
    }

    /**
     * @param $source
     * @param null $key
     * @param null $default
     * @param null $arr
     * @return array|mixed|null|VulnerableField|VulnerableField[]
     */
    public function getWrappedValueOrArray($source, $key = null, $default = null, $arr = null)
    {
        if ($key !== null) {
            return $this->getWrappedValue($source, $key, $default, $arr);

        } else {
            $result = [];
            $rawData = $this->getRawValue($source, null, []);
            foreach ($rawData as $k => $value) {
                $result[$k] = $this->getWrappedValue($source, $k, $value, $rawData);
            }
            return $result;
        }
    }

    /**
     * @param $source
     * @param null $key
     * @param null $default
     * @param null|array $arr
     * @return mixed|null|VulnerableField
     */
    public function getWrappedValue($source, $key = null, $default = null, $arr = null)
    {
        $raw = $this->getRawValue($source, $key, $default, $arr);

        if (!is_scalar($raw) && !is_null($raw)) {
            return $raw;
        }

        $context = $this->getCurrentContext();
        $descriptor = new FieldDescriptor($key, $source);
        $field = $context->getOrCreateMatchingField($descriptor);
        $field->setRequest($this);
        $vulnElement = $field->getMatchedVulnerabilityElement();

        return new VulnerableField($descriptor, $raw, $vulnElement);
    }

    /**
     * @param $source
     * @param $key
     * @param null $default
     * @param array|null $arr
     * @return mixed|null
     */
    public function getRawValue($source, $key = null, $default = null, $arr = null)
    {
        if (is_array($arr)) {
            return $this->get_filtered_value($arr, $key, $default, false);
        }

        if ($source == FieldDescriptor::SOURCE_QUERY) {
            $raw = $this->get($key, $default);

        } else if ($source == FieldDescriptor::SOURCE_BODY) {
            if (in_array($this->method, [self::METHOD_POST])) {
                $raw = $this->post($key, $default);

            } else if (in_array($this->method, [self::METHOD_PUT, self::METHOD_PATCH])) {
                $raw = $this->put($key, $default);

            } else {
                $raw = $default;
            }

        } else if ($source == FieldDescriptor::SOURCE_COOKIE) {
            $raw = $this->cookie($key, $default);

        } else if ($source == FieldDescriptor::SOURCE_HEADER) {
            $raw = $this->header($key, $default);

        } else if ($source == FieldDescriptor::SOURCE_PARAM) {
            $raw = rawurldecode($this->param($key, $default));

        } else {
            $raw = $default;
        }

        return $raw;
    }

    /**
     * @return \VulnModule\Config\Context
     */
    protected function getCurrentContext() {
        return $this->pixie->vulnService->getConfig()->getCurrentContext();
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
            if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
                $this->rawInputData = $GLOBALS['HTTP_RAW_POST_DATA'];

            } else {
                $this->rawInputData = file_get_contents('php://input');
            }
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

    /**
     * @param null $key
     * @param null $default
     * @param bool $filter_xss
     * @return mixed
     */
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
            if ($this->$fieldName === null) {
                throw new HttpException('Request data are malformed. Please check it.', 400, null, 'Bad Request');
            }

        } else if ($contentType == 'application/xml') {
            $requestBody = $this->rawRequestData();

            // Inject XMLExternalEntity vulnerability
            if ($protected = !$this->pixie->vulnService->isVulnerableTo('XMLExternalEntity')) {
                libxml_disable_entity_loader(true);
            } else {
                libxml_disable_entity_loader(false);
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

    /**
     * @param null $key
     * @param null $default
     * @param bool $filter_xss
     * @return mixed
     */
    public function cookie($key = null, $default = null, $filter_xss = false)
    {
        return $this->get_filtered_value($this->_cookie, $key, $default, $filter_xss);
    }

    /**
     * @return bool
     */
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

    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @return array All possible request methods
     */
    public static function getMethods()
    {
        return [
            self::METHOD_GET,
            self::METHOD_POST,
            self::METHOD_PUT,
            self::METHOD_DELETE,
            self::METHOD_PATCH,
            self::METHOD_HEAD,
            self::METHOD_OPTIONS
        ];
    }

    /**
     * @param array $arr
     * @param $source
     * @return array
     */
    public function wrapArray($arr, $source)
    {
        if (!is_array($arr) || !count($arr)) {
            return [];
        }
        $result = [];

        foreach ($arr as $key => $value) {
            $result[$key] = $this->getWrappedValue($source, $key, $value, $arr);
        }

        return $result;
    }

    /**
     * @param object $obj
     * @param $source
     * @return object
     */
    public function wrapObject($obj, $source)
    {
        if (!is_object($obj) || !count(get_object_vars($obj))) {
            return new \stdClass();
        }
        $result = new \stdClass();

        $arr = (array)$obj;
        foreach ($arr as $key => $value) {
            $result->$key = $this->getWrappedValue($source, $key, $value, $arr);
        }

        return $result;
    }
}