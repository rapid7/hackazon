<?PHP
/**
 * GWTPHP is a PHP port of the GWT rpc package.
 * 
 * <p>This framework is based on GWT (see {@link http://code.google.com/webtoolkit/ gwt-webtoolkit} for details).</p>
 * <p>Design, strategies and part of the methods documentation are developed by Google team 
 * 
 * <p>PHP port, extensions and modifications by Rafal M.Malinowski. All rights reserved.<br>
 * For more information, please see {@link http://rmalinowski.pl/}.</p>
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
 *  
 * @package gwtphp.rpc
 */

class RPCRequest {
	

	/**
	 * The method for this request.
	 *
	 * @var MappedMethod
	 */
	private $method;


	/**
	 * The parameters for this request.
	 * 
	 * @var Object[]
	 */
	private $parameters;

	/**
     * {@link SerializationPolicy} used for decoding this request and for encoding
     * the responses.
     * @var SerializationPolicy
     */
	private $serializationPolicy;

	
	/**
	 * 
	 * @var MappedClassLoader
	 */
	private $mappedClassLoader;
	
	
	/** 
	 *
	 * @return MappedClassLoader
	 */
	public function getMappedClassLoader() {
		return $this->mappedClassLoader;
	}
		
	
	/**
     * Construct an RPCRequest.
     * @param MappedClassLoader $mappedClassLoader
     */
	public function __construct(MappedMethod $method, $parameters,
		SerializationPolicy $serializationPolicy,MappedClassLoader $mappedClassLoader) {
		$this->method = $method;
		$this->parameters = $parameters;
		$this->serializationPolicy = $serializationPolicy;
		$this->mappedClassLoader = $mappedClassLoader;
	}


   /**
	* Get the request's method.
	* @return MappedMethod
	*/
	public function getMethod() {
		return $this->method;
	}


   /**
	* Get the request's parameters.
	* @return array
	*/
	public function  getParameters() {
		return $this->parameters;
	}

   /**
	* Returns the {@link SerializationPolicy} used to decode this request. This
	* is also the <code>SerializationPolicy</code> that should be used to
	* encode responses.
	* 
	* @return SerializationPolicy used to decode this request
	*/
	public function  getSerializationPolicy() {
		return $this->serializationPolicy;
	}



}

?>