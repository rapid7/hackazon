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
 * @package gwtphp.rpc
 */

require_once(GWTPHP_DIR.'/maps/com/google/gwt/user/client/rpc/SerializableException.class.php');
require_once(GWTPHP_DIR.'/maps/java/lang/GWTRuntimeException.class.php');
require_once(GWTPHP_DIR.'/maps/java/lang/NullPointerException.class.php');
require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
require_once(GWTPHP_DIR.'/maps/java/lang/IllegalArgumentException.class.php');
require_once(GWTPHP_DIR.'/rpc/impl/ServerSerializationStreamReader.class.php');
require_once(GWTPHP_DIR.'/rpc/impl/ServerSerializationStreamWriter.class.php');
require_once(GWTPHP_DIR.'/lang/MappedClass.class.php');
require_once(GWTPHP_DIR.'/rpc/impl/LegacySerializationPolicy.class.php');
require_once(GWTPHP_DIR.'/rpc/RPCRequest.class.php');

require_once(GWTPHP_DIR.'/maps/java/lang/Object.class.php');

class RPC {

    /**
     * @var Logger
     */
    private static $logger;

    /**
     * Returns an {@link RPCRequest} that is built by decoding the contents of an
     * encoded RPC request and optionally validating that type can handle the
     * request. If the type parameter is not <code>null</code>, the
     * implementation checks that the type is assignable to the
     * {@link RemoteService} interface requested in the encoded request string.
     *
     * <p>
     * If the serializationPolicyProvider parameter is not <code>null</code>,
     * it is asked for a {@link SerializationPolicy} to use to restrict the set of
     * types that can be decoded from the request. If this parameter is
     * <code>null</code>, then only subtypes of
     * {@link com.google.gwt.user.client.rpc.IsSerializable IsSerializable} or
     * types which have custom field serializers can be decoded.
     * </p>
     *
     * <p>
     * Invoking this method with <code>null</code> for the type parameter,
     * <code>decodeRequest(encodedRequest, null)</code>, is equivalent to
     * calling <code>decodeRequest(encodedRequest)</code>.
     * </p>
     * @param String $encodedRequest a string that encodes the {@link RemoteService}
     *          interface, the service method, and the arguments to pass to the
     *          service method
     * @param MappedClassLoader $mappedClassLoader
     * @param SerializationPolicyProvider $serializationPolicyProvider if not <code>null</code>, the
     *          implementation asks this provider for a
     *          {@link SerializationPolicy} which will be used to restrict the set
     *          of types that can be decoded from this request
     * @return RPCRequest an {@link RPCRequest} instance
     * @throws NullPointerException if the encodedRequest is <code>null</code>
     * @throws IllegalArgumentException if the encodedRequest is an empty string
     * @throws IncompatibleRemoteServiceException if any of the following
     *           conditions apply:
     *           <ul>
     *           <li>if the types in the encoded request cannot be deserialized</li>
     *           <li>if the {@link ClassLoader} acquired from
     *           <code>Thread.currentThread().getClassLoader()</code>
     *           cannot load the service interface or any of the types specified
     *           in the encodedRequest</li>
     *           <li>the requested interface is not assignable to
     *           {@link RemoteService}</li>
     *           <li>the service method requested in the encodedRequest is not a
     *           member of the requested service interface</li>
     *           <li>the type parameter is not <code>null</code> and is not
     *           assignable to the requested {@link RemoteService} interface
     *           </ul>
     */
    public static function decodeRequest($encodedRequest
    , MappedClassLoader $mappedClassLoader
    , SerializationPolicyProvider $serializationPolicyProvider)
    //public static function decodeRequest($encodedRequest, RPCResolver $rpcResolver ,SerializationPolicyProvider $serializationPolicyProvider)
    {

        $logger = Logger::getLogger('gwtphp.rpc.RPC');

        if ($encodedRequest === null) {
        	require_once(GWTPHP_DIR.'/maps/java/lang/NullPointerException.class.php');
            throw new NullPointerException("encodedRequest cannot be null");
        }
        if (strlen($encodedRequest) == 0) {        	
        	require_once(GWTPHP_DIR.'/maps/java/lang/IllegalArgumentException.class.php');
            throw new IllegalArgumentException("encodedRequest cannot be empty");
        }

        try {
            /*ServerSerializationStreamReader*/
            $streamReader = new ServerSerializationStreamReader($mappedClassLoader,$serializationPolicyProvider);
            //classLoader, serializationPolicyProvider);

            $streamReader->prepareToRead($encodedRequest);

            // Read the name of the RemoteService interface
            /*String*/
            $serviceIntfName = $streamReader->readString();
            $logger->debug("serviceIntfName: " .$serviceIntfName);
            // TODO: wybrac metode sprawdzenia czy posiadamy obiekt ktory moze implementowac wybrany
            // do uruchomienia interface - sprawdzic pozniej czy obiekt implementuje ten interface

            //if ($type != null) {
            //        if (!implementsInterface(type, serviceIntfName)) {
            //          // The service does not implement the requested interface
                //          throw new IncompatibleRemoteServiceException(
                //              "Blocked attempt to access interface '" + serviceIntfName
                //                  + "', which is not implemented by '" + printTypeName(type)
                //                  + "'; this is either misconfiguration or a hack attempt");
                //        }
                //}
                $serializationPolicy = $streamReader->getSerializationPolicy();
                //$gwtService = $classLoader->getMapManager()->getGWTServiceMap($serviceIntfName);

                /*MappedClass*/
                $serviceIntf = null;
                try {
                    $serviceIntf = RPC::getClassFromSerializedName($serviceIntfName, $mappedClassLoader);
                    $serviceIntfClass = $serviceIntf->getReflectionClass();
                    if (!$serviceIntfClass->implementsInterface('RemoteService') ) {
                    	 // The requested interface is not a RemoteService interface
                    	 require_once(GWTPHP_DIR.'/maps/com/google/gwt/user/client/rpc/IncompatibleRemoteServiceException.class.php');
	                    throw new IncompatibleRemoteServiceException(
			            "Blocked attempt to access interface '"
                    	                  . $serviceIntfName
                    	                  . "', which doesn't extend RemoteService; this is either misconfiguration or a hack attempt");
                    }
                    //	        if (!RemoteService.class.isAssignableFrom(serviceIntf)) {
                    //	          // The requested interface is not a RemoteService interface
                    //	          throw new IncompatibleRemoteServiceException(
                    //	              "Blocked attempt to access interface '"
                    //	                  + printTypeName(serviceIntf)
                    //	                  + "', which doesn't extend RemoteService; this is either misconfiguration or a hack attempt");
                    //	        }
                } catch (ClassNotFoundException $e) {
                    require_once(GWTPHP_DIR.'/maps/com/google/gwt/user/client/rpc/IncompatibleRemoteServiceException.class.php');
                    throw new IncompatibleRemoteServiceException(
		            "Could not locate requested interface '" . $serviceIntfName
                    . "' in default classloader: " . $e->getMessage());
                }

                $serviceMethodName =  $streamReader->readString();

                $logger->debug("serviceMethodName: " .$serviceMethodName);

                $paramCount =  $streamReader->readInt();
                $logger->debug("paramCount: " .$paramCount);

                /*MappedClass[]*/
                $parameterTypes = array();

                for ($i = 0; $i < $paramCount; ++$i) {
                    $paramClassName = $streamReader->readString();
                    //$parameterTypes[$i] = $paramClassName;

                    try {
                        $parameterTypes[$i] =  RPC::getClassFromSerializedName($paramClassName,
                        $mappedClassLoader);
                    } catch (ClassNotFoundException $e) {
                        require_once(GWTPHP_DIR.'/maps/com/google/gwt/user/client/rpc/IncompatibleRemoteServiceException.class.php');
                        throw new IncompatibleRemoteServiceException("Parameter " + $i
                        + " of is of an unknown type '" + $paramClassName + "': " . $e->getMessage());
                    }
                }

                $logger->debug(print_r($parameterTypes,true));


                $mappedMethod =  RPC::findInterfaceMethod($serviceIntf, $serviceMethodName,
                $parameterTypes, true);
                	
                if ($mappedMethod == null) {
                    require_once(GWTPHP_DIR.'/maps/com/google/gwt/user/client/rpc/IncompatibleRemoteServiceException.class.php');
                    throw new IncompatibleRemoteServiceException(
                    RPC::formatMethodNotFoundErrorMessage($serviceIntf, $serviceMethodName,
                    $parameterTypes));
                }
                /*Object[]*/
                $parameterValues = array();
                for ($i = 0; $i < $paramCount; ++$i) {
                    $parameterValues[$i] = $streamReader->deserializeValue($parameterTypes[$i]);
                }
                $logger->debug(print_r($parameterValues,true));

                //$gwtService->getServiceMethodMap(0)->getMethod();
                //
                //$rpcResolver->

                //$method = $rpcResolver->getRPCServiceMethod($serviceIntfName,$serviceMethodName);
                //$rpcRequest = new RPCRequest($method, $parameterValues, $serializationPolicy);
                //$rpcRequest->setClass($service);
                	
                //			$rpcRequest = $rpcResolver->getRPCRequest($serviceIntfName,$serviceMethodName,$parameterTypes);
                //			$rpcRequest->setParameters($parameterValues);
                //			$rpcRequest->setSerializationPolicy($serializationPolicy);


                return new RPCRequest($mappedMethod,$parameterValues,$serializationPolicy,$mappedClassLoader);
        } catch (SerializationException $ex) {
            require_once(GWTPHP_DIR.'/maps/com/google/gwt/user/client/rpc/IncompatibleRemoteServiceException.class.php');
            throw new IncompatibleRemoteServiceException($ex->getMessage(), $ex);
        }


    }
    /**
     *
     *
     * @param MappedClass $serviceIntf
     * @param string $serviceMethodName
     * @param MappedClass[] $parameterTypes
     * @param boolen $includeInherited
     * @return MappedMethod
     */
    private static function findInterfaceMethod(MappedClass $serviceIntf, $methodName,
    $parameterTypes, $includeInherited) { // TODO in gwt-1.5 deleted
        return $serviceIntf->getDeclaredMethod($methodName, $parameterTypes);
        //TODO: search in inherited methods
        //return null;
    }
    /**
     * Returns the {@link Class} instance for the named class or primitive type.
     *
     * @param string $serializedName the serialized name of a class or primitive type
     * @param MappedClassLoader $mappedClassLoader the classLoader used to load {@link Class}es
     * @return MappedClass instance for the given type name
     * @throws ClassNotFoundException if the named type was not found
     */
    private static function getClassFromSerializedName($serializedName, MappedClassLoader $mappedClassLoader)  {
        // TODO: create TYPE_NAMES for primitive classes
        // $value = TYPE_NAMES.get(serializedName);
        //if (value != null) {
        //  return value;
        //}
        return $mappedClassLoader->loadMappedClass($serializedName);
    }

    /**
     * Returns a string that encodes the result of calling a service method, which
     * could be the value returned by the method or an exception thrown by it.
     *
     * <p>
     * If the serializationPolicy parameter is not <code>null</code>, it is
     * used to determine what types can be encoded as part of this response. If
     * this parameter is <code>null</code>, then only subtypes of
     * {@link com.google.gwt.user.client.rpc.IsSerializable IsSerializable} or
     * types which have custom field serializers may be encoded.
     * </p>
     *
     * <p>
     * This method does no security checking; security checking must be done on
     * the method prior to this invocation.
     * </p>
     *
     * @param Object $target instance on which to invoke the serviceMethod
     * @param MappedMethod $serviceMethod the method to invoke
     * @param array $args arguments used for the method invocation
     * @param SerializationPolicy $serializationPolicy determines the serialization policy to be used
     * @return strinta string which encodes either the method's return or a checked
     *         exception thrown by the method
     *
     * @throws NullPointerException if the serviceMethod or the
     *           serializationPolicy are <code>null</code>
     * @throws SecurityException if the method cannot be accessed or if the number
     *           or type of actual and formal arguments differ
     * @throws SerializationException if an object could not be serialized by the
     *           stream
     * @throws UnexpectedException if the serviceMethod throws a checked exception
     *           that is not declared in its signature
     */
    public static function invokeAndEncodeResponse($target, MappedMethod $serviceMethod, $args, SerializationPolicy $serializationPolicy,MappedClassLoader $mappedClassLoader) {

        //Object $target,
        //ReflectionMethod $serviceMethod, array $args,
        //SerializationPolicy $serializationPolicy

        if ($serviceMethod === null) {
        	require_once(GWTPHP_DIR.'/maps/java/lang/NullPointerException.class.php');
            throw new NullPointerException("Not found matches serviceMethod (TIP: did you map your service method correctly?");
        }
        if ($serializationPolicy === null) {
        	require_once(GWTPHP_DIR.'/maps/java/lang/NullPointerException.class.php');
            throw new NullPointerException("serializationPolicy");
        }

        /*String*/
        $responsePayload = '';
        try {
            /*Object*/
            $result = $serviceMethod->invoke($target, $args);
            $responsePayload = RPC::encodeResponseForSuccess($serviceMethod,$result,$serializationPolicy,$mappedClassLoader);
            //} catch (IllegalAccessException $xe) {
            //      SecurityException securityException = new SecurityException(
            //          formatIllegalAccessErrorMessage(target, serviceMethod));
            //      securityException.initCause(e);
            //      throw securityException;
            //    } catch (IllegalArgumentException e) {
            //      SecurityException securityException = new SecurityException(
                //          formatIllegalArgumentErrorMessage(target, serviceMethod, args));
                //      securityException.initCause(e);
                //      throw securityException;
            } catch (Exception $ex) {
                //      // Try to encode the caught exception
                //      //
                //      Throwable cause = e.getCause();
                //
                $responsePayload = RPC::encodeResponseForFailure($serviceMethod, $ex,
                $serializationPolicy,$mappedClassLoader);
            }
            return $responsePayload;
            //}
            //catch (Exception $ex) {
            //print_r($ex) ;
                //}
            }


            /**
             * Returns a string that encodes the object. It is an error to try to encode
             * an object that is not assignable to the service method's return type.
             *
             * <p>
             * If the serializationPolicy parameter is not <code>null</code>, it is
             * used to determine what types can be encoded as part of this response. If
             * this parameter is <code>null</code>, then only subtypes of
             * {@link com.google.gwt.user.client.rpc.IsSerializable IsSerializable} or
             * types which have custom field serializers may be encoded.
             * </p>
             *
             * @param ReflectionMethod $serviceMethod the method whose result we are encoding
             * @param Object $object the instance that we wish to encode
             * @param SerializationPolicy $serializationPolicy determines the serialization policy to be used
             * @return String a string that encodes the object, if the object is compatible with
             *         the service method's declared return type
             *
             * @throws IllegalArgumentException if the result is not assignable to the
             *           service method's return type
             * @throws NullPointerException if the serviceMethod or the
             *           serializationPolicy are <code>null</code>
             * @throws SerializationException if the result cannot be serialized
             */
            public static function encodeResponseForSuccess(MappedMethod $serviceMethod,$object, SerializationPolicy $serializationPolicy,MappedClassLoader $mappedClassLoader) {

                if ($serviceMethod === null) {
        			require_once(GWTPHP_DIR.'/maps/java/lang/NullPointerException.class.php');
                    throw new NullPointerException("serviceMethod cannot be null");
                }

                if ($serializationPolicy === null) {
        			require_once(GWTPHP_DIR.'/maps/java/lang/NullPointerException.class.php');
                    throw new NullPointerException("serializationPolicy");
                }

                /*MappedClass*/
                $methodReturnType = $serviceMethod->getReturnType();
                if ($methodReturnType != '' && $object !== null) {
                    //$actualReturnType;
                    if ( $methodReturnType->isPrimitive()) {
                        //$actualReturnType = RPC::getPrimitiveClassFromWrapper(object.getClass());
                    } else {
                        //actualReturnType = object.getClass();
                    }

                    //			if (actualReturnType == null
                    //			|| !methodReturnType.isAssignableFrom(actualReturnType)) {
                    //				throw new IllegalArgumentException("Type '"
                    //				+ printTypeName(object.getClass())
                    //				+ "' does not match the return type in the method's signature: '"
                    //				+ getSourceRepresentation(serviceMethod) + "'");
                    //			}
                    }

                    // TODO: fix this mess
                    if ($methodReturnType!== null && ($methodReturnType->isPrimitive()
                    || $methodReturnType->isArray()
                    //|| $methodReturnType->isGeneric()
                    || JavaSignatureUtil::isVoid($methodReturnType->getSignature())
                    //|| JavaSignatureUtil::isNative($methodReturnType->getSignature())
                    )) {
                        	
                    } else if (is_object($object)) {
                        	
                        //echo "<br>\n : ".print_r($object,true);
                        //echo "<br>\n : ".print_r($methodReturnType,true);
                        //echo "<br>\n <hr>";
                        $_methodReturnType = $mappedClassLoader->findMappedClassByObject($object);
                        if ($_methodReturnType != null && $methodReturnType != null && !$methodReturnType->isGeneric()) $methodReturnType = $_methodReturnType;
                    }
                    return RPC::encodeResponse($methodReturnType, $object, false,$serializationPolicy);
            }

            /**
             * Returns a string that encodes an exception. If method is not
             * <code>null</code>, it is an error if the exception is not in the
             * method's list of checked exceptions.
             *
             * <p>
             * If the serializationPolicy parameter is not <code>null</code>, it is
             * used to determine what types can be encoded as part of this response. If
             * this parameter is <code>null</code>, then only subtypes of
             * {@link com.google.gwt.user.client.rpc.IsSerializable IsSerializable} or
             * types which have custom field serializers may be encoded.
             * </p>
             *
             * @param MappedMethod serviceMethod the method that threw the exception, may be
             *          <code>null</code>
             * @param Exception cause the {@link Throwable} that was thrown
             * @param SerializationPolicy serializationPolicy determines the serialization policy to be used
             * @return a string that encodes the exception
             *
             * @throws NullPointerException if the the cause or the serializationPolicy
             *           are <code>null</code>
             * @throws SerializationException if the result cannot be serialized
             * @throws UnexpectedException if the result was an unexpected exception (a
             *           checked exception not declared in the serviceMethod's signature)
             */
            public static function encodeResponseForFailure(MappedMethod $serviceMethod = null,
            Exception $cause, SerializationPolicy $serializationPolicy = null,MappedClassLoader $mappedClassLoader)
            {
                if ($cause === null) {
        			require_once(GWTPHP_DIR.'/maps/java/lang/NullPointerException.class.php');
                    throw new NullPointerException("cause cannot be null");
                }
                if ($serializationPolicy === null) {
                    $serializationPolicy = RPC::getDefaultSerializationPolicy();
                    //throw new NullPointerException("serializationPolicy");
                }

                if ($serviceMethod != null && !RPC::isExpectedException($serviceMethod, $cause)) {
                    
        			require_once(GWTPHP_DIR.'/maps/java/lang/UnexpectedException.class.php');
                    throw new UnexpectedException("Service method '"
                    . RPC::getSourceRepresentation($serviceMethod)
                    . "' threw an unexpected exception: " . $cause->__toString());
                }

                //class_exists('UnimplementedOperationException') || require(GWTPHP_DIR.'/exceptions/UnimplementedOperationException.class.php');
                //throw new UnimplementedOperationException("Exception serialization not implemented yet! " . print_r($cause,true));
                //ArrayMappedClassLoader::loadMappedClass('pl.rmalinowski.gwtphp.client.dto.SimpleException');
                $couseClass = $mappedClassLoader->findMappedClassByReflectionClass(new ReflectionObject($cause));
                return RPC::encodeResponse($couseClass, $cause, true, $serializationPolicy);
                // return RPC::encodeResponse($cause.getClass(), $cause, true, $serializationPolicy);
            }
            /**
             * Returns the source representation for a method signature.
             *
             * @param MappedMethod $method method to get the source signature for
             * @return String source representation for a method signature
             */
            private static function getSourceRepresentation(MappedMethod $method) {
                 
                return $method->getName(); // str_replace('$', '.',);
            }

            /**
             * Returns a string that encodes the results of an RPC call. Private overload
             * that takes a flag signaling the preamble of the response payload.
             *
             * @param Object $object the object that we wish to send back to the client
             * @param boolean $wasThrown if true, the object being returned was an exception thrown
             *          by the service method; if false, it was the result of the service
             *          method's invocation
             * @return a string that encodes the response from a service method
             * @throws SerializationException if the object cannot be serialized
             */
            //($methodReturnType, $object, false,$serializationPolicy);
            private static function encodeResponse(MappedClass $methodReturnType
            , $object,	$wasThrown, SerializationPolicy $serializationPolicy) {

                $stream = new ServerSerializationStreamWriter($serializationPolicy);

                $stream->prepareToWrite();
                if ($methodReturnType->getSignature() != TypeSignatures::$VOID) { //!= void.class
                    $stream->serializeValue($object, $methodReturnType);
                }

                $bufferStr = ($wasThrown ? "//EX" : "//OK") . $stream->toString();
                return $bufferStr;
            }

            private static function formatMethodNotFoundErrorMessage(MappedClass $serviceIntf,
            $serviceMethodName, $parameterTypes) {

                $sb = "";

                $sb.="Could not locate requested method '";
                $sb.=$serviceMethodName;
                $sb.="(";
                for ($i = 0; $i < count($parameterTypes); ++$i) {
                    if ($i > 0) {
                        $sb.=", ";
                    }
                    /*MappedClass[]*/
                    //$type = $parameterTypes[$i];
                    $sb.=RPC::printTypeName( $parameterTypes[$i]);
                }
                $sb.=")'";

                $sb.=" in interface '";
                $sb.=printTypeName($serviceIntf);
                $sb.="'";

                return $sb;
            }

            /**
             * Returns true if the {@link java.lang.reflect.Method Method} definition on
             * the service is specified to throw the exception contained in the
             * InvocationTargetException or false otherwise. NOTE we do not check that the
             * type is serializable here. We assume that it must be otherwise the
             * application would never have been allowed to run.
             *
             * @param MappedMethod serviceIntfMethod the method from the RPC request
             * @param Exception cause the exception that the method threw
             * @return boolean true if the exception's type is in the method's signature
             */
            private static function isExpectedException(MappedMethod $serviceIntfMethod,
            Exception $cause) {
                assert ($serviceIntfMethod != null);
                assert ($cause != null);

                /*Class[]*/ $exceptionsThrown = $serviceIntfMethod->getExceptionTypes();
                if (count($exceptionsThrown) <= 0) {
                    // The method is not specified to throw any exceptions
                    //
                    return false;
                }

                $causeType = new ReflectionObject($cause);// $cause.getClass();

                foreach ($exceptionsThrown as $exceptionThrown) {
                    assert ($exceptionThrown != null);
                    //$exceptionThrown = new SimpleMappedClass();

                    if (
                    ($causeType->getFileName() == $exceptionThrown->getReflectionClass()->getFileName()
                    && $causeType->getName() == $exceptionThrown->getReflectionClass()->getName())
                    || $causeType->isSubclassOf($exceptionThrown->getReflectionClass()->getName())) {
                        //.isAssignableFrom(causeType)) {
                        return true;
                    }
                }

                return false;
            }


            /**
             * Straight copy from
             * {@link com.google.gwt.dev.util.TypeInfo#getSourceRepresentation(Class)} to
             * avoid runtime dependency on gwt-dev.
             */
            private static function printTypeName(MappedClass $type) {
                // Primitives
                //
                if ($type->isPrimitive()) {
                    switch ($type) {
                        case TypeSignatures::$BOOLEAN:
                            return 'boolean';
                        case TypeSignatures::$BYTE:
                            return 'byte';
                        case TypeSignatures::$CHAR:
                            return 'char';
                        case TypeSignatures::$DOUBLE:
                            return 'double';
                        case TypeSignatures::$FLOAT:
                            return 'float';
                        case TypeSignatures::$INT:
                            return 'int';
                        case TypeSignatures::$LONG:
                            return 'long';
                        case TypeSignatures::$SHORT:
                            return 'short';
                        default: 'unknown';
                    }
                }


                // Arrays
                //
                if ($type->isArray()) {
                    $componentType = $type->getComponentType();
                    return RPC::printTypeName($componentType) + '[]';
                }

                // Everything else
                //
                return $type->getName();//.replace('$', '.');
            }

            /**
             * Returns a default serialization policy.
             *
             * @return SerializationPolicy the default serialization policy.
             */
            public static function getDefaultSerializationPolicy() {
                return LegacySerializationPolicy::getInstance();
            }

}

?>
