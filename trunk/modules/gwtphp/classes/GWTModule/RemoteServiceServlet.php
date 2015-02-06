<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.09.2014
 * Time: 10:31
 */


namespace GWTModule;


use App\Core\Request;
use App\Exception\SQLException;
use App\Pixie;
use GWTModule\RPC\RPC;
use VulnModule\Vulnerability\SQL;

/**
 * Pixified version of
 * @package App\GWTPHP
 * @inheritdoc
 */
class RemoteServiceServlet extends \RemoteServiceServlet
{
    /**
     * @var \App\Pixie
     */
    protected $pixie;

    /**
     * @var PHPixieORMRepository
     */
    protected $repository;

    /**
     * @var \Logger
     */
    protected $logger;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @inheritdoc
     * @param Pixie $pixie
     */
    function __construct(Pixie $pixie)
    {
        parent::__construct();
        $this->pixie = $pixie;
        $this->repository = new PHPixieORMRepository($pixie);
        $this->logger = \Logger::getLogger('gwtphp.RemoteServiceServlet');
    }

    /**
     * @return PHPixieORMRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @inheritdoc
     * @return Helper\SimpleRPCTargetResolverStrategy|\RPCTargetResolverStrategy
     */
    protected function getRPCTargetResolverStrategy()
    {
        $strategy = new Helper\SimpleRPCTargetResolverStrategy($this->request);
        $strategy->setPixie($this->pixie);
        return $strategy;
    }

    /**
     * @param string $payload
     * @return string|null
     * @throws \IllegalArgumentException
     */
    public function processCall($payload)
    {
        try {
            $this->logger->debug('Processing Call start');

            /** @var \RPCRequest $rpcRequest */
            $rpcRequest = \RPC::decodeRequest($payload, $this->getMappedClassLoader(), $this);
            $this->onAfterRequestDecoded($rpcRequest);
            /** @var \RPCTargetResolverStrategy|Object $target */
            $target = $this->getRPCTargetResolverStrategy()->resolveRPCTarget($rpcRequest->getMethod()->getDeclaringMappedClass());

            $this->pixie->vulnService->goDown(preg_replace('/Impl$/', '', get_class($target)));
            $this->pixie->vulnService->goDown($rpcRequest->getMethod()->getName());
            if ($target instanceof IGWTService) {
                $target->setContext($this->pixie->vulnService->getConfig()->getCurrentContext());
                $target->setRequest($this->request);
            }

            return RPC::invokeAndEncodeResponse($target, $rpcRequest->getMethod(), $rpcRequest->getParameters(),
                $rpcRequest->getSerializationPolicy(), $rpcRequest->getMappedClassLoader());

        } catch (\IncompatibleRemoteServiceException $ex) {
            $this->logger->log(\LoggerLevel::getLevelError(),
                'An IncompatibleRemoteServiceException was thrown while processing this call.',
                $ex);

            return \RPC::encodeResponseForFailure(null, $ex, null, $this->getMappedClassLoader());
        }
    }

    public function start($test_post_data = NULL)
    {
        try {
            if ($test_post_data === NULL)
                $requestPayload = $this->readPayloadAsUtf8();
            else
                $requestPayload = $test_post_data;

            // Let subclasses see the serialized request.
            //
            $this->onBeforeRequestDeserialized($requestPayload);

            // Invoke the core dispatching logic, which returns the serialized
            // result.
            //
            $this->logger->info($requestPayload);

            $responsePayload = $this->processCall($requestPayload);

            $this->logger->info($responsePayload);

            // Let subclasses see the serialized response.
            //
            $this->onAfterResponseSerialized($responsePayload);
            // Write the response.
            //
            if ($test_post_data === NULL)
                $this->writeResponse($responsePayload);
            else
                return $responsePayload;

        } catch (\Exception $ex) {
            $this->doUnexpectedFailure($ex);
        }
        return null;
    }

    protected function readPayloadAsUtf8() {
        if(isset($GLOBALS["HTTP_RAW_POST_DATA"]) && $GLOBALS["HTTP_RAW_POST_DATA"] != "") {
            return $GLOBALS["HTTP_RAW_POST_DATA"];
        } else {
            $postData = file_get_contents("php://input");
            if (strlen($postData)!==0)
                return $postData;
            else{
                $this->logger->debug("GWTPHP and this gateway are installed correctly, but you have to connect " .
                    "to this gateway from GWT.",__CLASS__,__METHOD__,__FILE__,__LINE__);
                throw new \Exception("Empty content exception");
            }
        }
    }

    /**
     * Write the response payload to the response stream.
     * @param String $responsePayload
     */
    private function writeResponse($responsePayload) {
        header('Content-Type: text/html; charset=utf-8',true);
        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate',true);
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT',true); // Date in the past
        header('Pragma: no-cache',true);
        print $responsePayload;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }
} 