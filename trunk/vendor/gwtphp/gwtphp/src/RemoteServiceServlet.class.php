<?PHP
/*
 * GWTPHP is a port to PHP of the GWT RPC package.
 *
 * <p>This framework is based on GWT (see {@link http://code.google.com/webtoolkit/ gwt-webtoolkit} for details).</p>
 * <p>Design, strategies and part of the methods documentation are developed by Google Team  </p>
 *
 * <p>PHP port, extensions and modifications by Rafal M.Malinowski. All rights reserved.<br>
 * For more information, please see {@link http://gwtphp.sourceforge.com/}.</p>
 *
 *
 * <p>Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at</p>
 *
 * {@link http://www.apache.org/licenses/LICENSE-2.0 http://www.apache.org/licenses/LICENSE-2.0}
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

/**
 * @package gwtphp
 */

define('GWTPHP_DIR',dirname(__FILE__));

require_once(GWTPHP_DIR.'/maps/com/google/gwt/user/client/rpc/IsSerializable.class.php');
require_once(GWTPHP_DIR.'/maps/com/google/gwt/user/client/rpc/RemoteService.class.php');

require_once(GWTPHP_DIR.'/rpc/RPC.class.php');
require_once(GWTPHP_DIR.'/maps/com/google/gwt/user/client/rpc/SerializableException.class.php');

require_once(GWTPHP_DIR.'/rpc/SerializationPolicyProvider.class.php');

require_once(GWTPHP_DIR.'/helpers/RPCTargetResolverStrategy.class.php');
require_once(GWTPHP_DIR.'/helpers/SimpleRPCTargetResolverStrategy.class.php');

/**
 * The servlet base class for your RPC service implementations that
 * automatically deserializes incoming requests from the client and serializes
 * outgoing responses for client/server RPCs.
 */
class RemoteServiceServlet implements SerializationPolicyProvider  {

    private static /*final*/ $GENERIC_FAILURE_MSG = "The call failed on the server; see server log for details";

    /**
     *
     * @var Logger
     */
    private $logger;

    /**
     *
     * @var RPCResolver
     * @deprecated
     */
    //private $rpcResolver;

    /**
     *
  fdsasdfasd mk lh /lbg   * @var MappedClassLoader
     */
    private $mappedClassLoader;

    /**
     *
     * @param MappedClassLoader $mappedClassLoader
     * @return void
     */
    public function setMappedClassLoader(MappedClassLoader $mappedClassLoader) {
        $this->mappedClassLoader = $mappedClassLoader;
    }
    /**
     *
     * @return MappedClassLoader
     */
    public function getMappedClassLoader() {
        return (null === $this->mappedClassLoader)
        ? $this->mappedClassLoader = GWTPHPContext::getInstance()->getMappedClassLoader()
        : $this->mappedClassLoader;
    }


    /**
     * Constructor
     *
     */
    function __construct()
    {
        $this->logger = Logger::getLogger('gwtphp.RemoteServiceServlet');
    }
    /**
     *
     *
     * This start method swallows ALL exceptions, logs them
     * (default in the file: logger.output.html), and returns a GENERIC_FAILURE_MSG response with status code
     * 500.
     */
    public function start($test_post_data = NULL) {
        try {
			if ($test_post_data === NULL)
				$requestPayload = RemoteServiceServlet::readPayloadAsUtf8();
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
        } catch (Exception $ex) {
            $this->doUnexpectedFailure($ex);
        }
    }

    private function readPayloadAsUtf8() {
        if(isset($GLOBALS["HTTP_RAW_POST_DATA"]) && $GLOBALS["HTTP_RAW_POST_DATA"] != "") {
            return $GLOBALS["HTTP_RAW_POST_DATA"];
        } else {
			$postData = file_get_contents("php://input");
			if (strlen($postData)!==0)
				return $postData;
			else{
				$this->logger->debug("GWTPHP and this gateway are installed correctly, but you have to connect " .
								"to this gateway from GWT.",__CLASS__,__METHOD__,__FILE__,__LINE__);
				throw new Exception("Empty content exception");
			}
        }
    }


    /**
     * Process a call originating from the given request. Uses the
     * {@link RPC#invokeAndEncodeResponse(Object, java.lang.reflect.Method, Object[])}
     * method to do the actual work.
     * <p>
     * Subclasses may optionally override this method to handle the payload in any
     * way they desire (by routing the request to a framework component, for
     * instance). The {@link HttpServletRequest} and {@link HttpServletResponse}
     * can be accessed via the {@link #getThreadLocalRequest()} and
     * {@link #getThreadLocalResponse()} methods.
     * </p>
     * This is public so that it can be unit tested easily without HTTP.
     *
     * @param payload the UTF-8 request payload
     * @return a string which encodes either the method's return, a checked
     *         exception thrown by the method, or an
     *         {@link IncompatibleRemoteServiceException}
     * @throws SerializationException if we cannot serialize the response
     * @throws UnexpectedException if the invocation throws a checked exception
     *           that is not declared in the service method's signature
     * @throws RuntimeException if the service method throws an unchecked
     *           exception (the exception will be the one thrown by the service)
     */
    public function processCall($payload) {
        try {
            $this->logger->debug('Processing Call start',$this);
            $rpcRequest = RPC::decodeRequest($payload,$this->getMappedClassLoader(),$this);
            //FOCUS: this method is used only in PHP implementation of GWT RemoteServiceServlet
            $this->onAfterRequestDecoded($rpcRequest);
 			$target = $this->getRPCTargetResolverStrategy()->resolveRPCTarget($rpcRequest->getMethod()->getDeclaringMappedClass());
            
            return RPC::invokeAndEncodeResponse($target,$rpcRequest->getMethod(), $rpcRequest->getParameters(), $rpcRequest->getSerializationPolicy(),$rpcRequest->getMappedClassLoader());
        } catch (IncompatibleRemoteServiceException $ex) {
            $this->logger->log(LoggerLevel::getLevelError(),
	    		'An IncompatibleRemoteServiceException was thrown while processing this call.',
            $ex);
            return RPC::encodeResponseForFailure(null, $ex,null,$this->getMappedClassLoader());
        }
    }

    /**
     * @return RPCTargetResolverStrategy
     *
     */
    protected function getRPCTargetResolverStrategy() {
    	//return new NullRPCTargetResolverStrategy();
    	return new SimpleRPCTargetResolverStrategy();
    }
    
    /**
     * Override this method to examine the serialized response that will be
     * returned to the client. The default implementation does nothing and need
     * not be called by subclasses.
     */
    protected function onAfterResponseSerialized($serializedResponse) {
    }

    /**
     * Override this method to examine the serialized version of the request
     * payload before it is deserialized into objects. The default implementation
     * does nothing and need not be called by subclasses.
     */
    protected function onBeforeRequestDeserialized($serializedRequest) {
    }
    
    /**
     * Override this method to examine the decoded request that will be
     * processing by RemoteServiceServlet. The default implementation does nothing and need
     * not be called by subclasses.
     * FOCUS: this method is used only in PHP implementation of GWT RemoteServiceServlet
     */
    protected function onAfterRequestDecoded($rpcRequest) {
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
     * Returns a {@link SerializationPolicy} for a given module base URL and
     * serialization policy strong name.
     *
     * @param String $moduleBaseURL the URL for the module
     * @param String $serializationPolicyStrongName strong name of the serialization
     *          policy for the specified module URL
     * @return SerializationPolicy a {@link SerializationPolicy} for a given module base URL and RPC
     *         strong name; must not return <code>null</code>
     */
    function getSerializationPolicy($moduleBaseURL, $serializationPolicyStrongName) {
        //TODO: implement this method
    }

    /**
     * Override this method to control what should happen when an exception
     * escapes the {@link #processCall(String)} method. The default implementation
     * will log the failure and send a generic failure response to the client.<p/>
     *
     * An "expected failure" is an exception thrown by a service method that is
     * declared in the signature of the service method. These exceptions are
     * serialized back to the client, and are not passed to this method. This
     * method is called only for exceptions or errors that are not part of the
     * service method's signature, or that result from SecurityExceptions,
     * SerializationExceptions, or other failures within the RPC framework.<p/>
     *
     * Note that if the desired behavior is to both send the GENERIC_FAILURE_MSG
     * response AND to rethrow the exception, then this method should first send
     * the GENERIC_FAILURE_MSG response itself (using getThreadLocalResponse), and
     * then rethrow the exception. Rethrowing the exception will cause it to
     * escape into the servlet container.
     *
     * @param e the exception which was thrown
     */
    protected function doUnexpectedFailure(Exception $e) {
		$this->logger->log(LoggerLevel::getLevelFatal(),$e->getTraceAsString());
        //gwtphp.done
        $this->logger->log(LoggerLevel::getLevelFatal(),"Exception while dispatching incoming RPC call",$this);
        $this->logger->log(LoggerLevel::getLevelFatal(),$e->getMessage(),$this);
        // Send GENERIC_FAILURE_MSG with 500 status.
        //
        $this->respondWithFailure();
    }

    /**
     * Called when the machinery of this class itself has a problem, rather than
     * the invoked third-party method. It writes a simple 500 message back to the
     * client.
     */
    private function respondWithFailure() {
        //gwtphp.done
        header('Content-Type: text/html; charset=utf-8');
        header("HTTP/1.1 500 Internal Server Error");
        echo self::$GENERIC_FAILURE_MSG;
    }

}

?>
