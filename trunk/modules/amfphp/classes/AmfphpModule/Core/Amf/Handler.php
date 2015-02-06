<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.01.2015
 * Time: 15:42
 */


namespace AmfphpModule\Core\Amf;


use Amfphp_Core_Amf_Message;
use Amfphp_Core_Common_ServiceRouter;
use App\Pixie;

class Handler extends \Amfphp_Core_Amf_Handler
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var \VulnModule\VulnInjection\Service
     */
    protected $vulnService;

    /**
     * @param array $sharedConfig
     * @param Pixie $pixie
     */
    public function __construct($sharedConfig, $pixie)
    {
        parent::__construct($sharedConfig);
        $this->pixie = $pixie;
        $this->vulnService = $this->pixie->vulnService;
    }

    /**
     * @inheritdoc
     * @throws \Amfphp_Core_Exception
     * @throws \Exception
     */
    protected function handleRequestMessage(Amfphp_Core_Amf_Message $requestMessage, Amfphp_Core_Common_ServiceRouter $serviceRouter)
    {
        $filterManager = \Amfphp_Core_FilterManager::getInstance();
        $fromFilters = $filterManager->callFilters(self::FILTER_AMF_REQUEST_MESSAGE_HANDLER, null, $requestMessage);
        if ($fromFilters) {
            $handler = $fromFilters;
            return $handler->handleRequestMessage($requestMessage, $serviceRouter);
        }

        //plugins didn't do any special handling. Assumes this is a simple Amfphp_Core_Amf_ RPC call
        $serviceCallParameters = $this->getServiceCallParameters($requestMessage);
        $this->vulnService->goDown($serviceCallParameters->serviceName);
        $this->vulnService->goDown($serviceCallParameters->methodName);
        $ret = $serviceRouter->executeServiceCall($serviceCallParameters->serviceName, $serviceCallParameters->methodName, $serviceCallParameters->methodParameters);
        $this->vulnService->goUp()->goUp();

        $responseMessage = new Amfphp_Core_Amf_Message();
        $responseMessage->data = $ret;
        $responseMessage->targetUri = $requestMessage->responseUri . \Amfphp_Core_Amf_Constants::CLIENT_SUCCESS_METHOD;
        //not specified
        $responseMessage->responseUri = 'null';
        return $responseMessage;
    }
}