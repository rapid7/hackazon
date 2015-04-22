<?php
/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov 
 * Date: 21.04.2015
 * Time: 17:35
  */

use App\Pixie;

class AmfphpJsonEx extends \AmfphpJson
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var \VulnModule\VulnInjection\Service
     */
    protected $vulnService;

    protected $handler;

    /**
     * @var null|\App\Exception\HttpException
     */
    protected $exception = null;

    public function __construct(array $config = null)
    {
        parent::__construct($config);
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST_HANDLER, $this, 'filterHandler', 5);
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_HEADERS, $this, 'filterHeaders');
        $this->pixie = $config['pixie'];
    }

    function getPixie()
    {
        return $this->pixie;
    }

    function setPixie(Pixie $pixie = null)
    {
        $this->pixie = $pixie;
    }

    public function filterHandler($handler, $contentType)
    {
        $this->handler = $handler;
        return parent::filterHandler($handler, $contentType);
    }

    /**
     * @param array|stdClass $deserializedRequest
     * @param Amfphp_Core_Common_ServiceRouter $serviceRouter
     * @return array
     */
    public function handleDeserializedRequest($deserializedRequest, Amfphp_Core_Common_ServiceRouter $serviceRouter)
    {
        try {
            $serviceName = $deserializedRequest->serviceName;
            $methodName = $deserializedRequest->methodName;

            $parameters = array();
            if (isset($deserializedRequest->parameters)) {
                $parameters = $deserializedRequest->parameters;
            }

            $this->pixie->vulnService->goDown($deserializedRequest->serviceName);
            $this->pixie->vulnService->goDown($deserializedRequest->methodName);
            $result = $serviceRouter->executeServiceCall($serviceName, $methodName, $parameters);
            $this->pixie->vulnService->goUp()->goUp();

            $this->exception = null;
            return $result;

        } catch (\App\Exception\HttpException $ex) {
            $result = [
                'error' => true,
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            ];

            if ($this->returnErrorDetails) {
                $result['trace'] = $ex->getTraceAsString();
            }

            $this->exception = $ex;

            return $result;

        } catch (\App\Exception\SQLException $ex) {
            $result = [
                'error' => true,
                'code' => $ex->getCode(),
                'message' => $ex->isBlind() ? '' : $ex->getMessage()
            ];

            if (!$ex->isBlind() && $this->returnErrorDetails) {
                $result['trace'] = $ex->getTraceAsString();
            }

            $this->exception = $ex;

            return $result;

        } catch (\Exception $ex) {
            $result = [
                'error' => true,
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            ];

            if ($this->returnErrorDetails) {
                $result['trace'] = $ex->getTraceAsString();
            }

            $this->exception = $ex;

            return $result;
        }
    }

    public function filterHeaders($headers, $contentType){
        if ($contentType == self::JSON_CONTENT_TYPE) {
            if ($this->exception) {
                if ($this->exception instanceof \App\Exception\HttpException) {
                    $headers['HTTP/1.1 ' . $this->exception->getStatus()] = '';

                } else {
                    $headers['HTTP/1.1 500 Internal Server Error'] = '';
                }
            }
            $headers['Content-Type'] =  'application/json';
        }
        return $headers;
    }
}