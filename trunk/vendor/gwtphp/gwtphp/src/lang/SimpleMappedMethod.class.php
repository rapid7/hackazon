<?php
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
 * @package gwtphp.lang
 */

require_once(GWTPHP_DIR.'/lang/MappedMethod.class.php');

class SimpleMappedMethod implements MappedMethod  {
	
	
	/**
	 * 
	 * @var MappedClass
	 */
	private $declaringMappedClass;
	
	/**
	 * 
	 * @var string
	 */
	private $name;
	
	/**
	 * 
	 * @var string
	 */
	private $mappedName;
	
	
	/**
	 * 
	 * @var MappedClass[]
	 */
	private $exceptionTypes;
	
	/**
	 * 
	 * @var MappedClass[]
	 */
	private $parameterTypes;
	
	
	/**
	 * 
	 * @var MappedClass
	 */
	private $returnType;
	

	/**	 
	 *
	 * @param MappedClass $declaringMappedClass
	 * @return void
	 */
	public function setDeclaringMappedClass(MappedClass $declaringMappedClass) {
		$this->declaringMappedClass = $declaringMappedClass;
	}
				
	
	/**
	 * Returns the MappedClass object representing the class or interface 
	 * that declares the mapped method represented by this MappedMethod object.
	 * 
	 * @return MappedClass
	 */
	public function getDeclaringMappedClass() {
		return $this->declaringMappedClass;
	}
	
	/**
	 * Returns the Java language modifiers for the method represented by this Method object, as an integer. 
	 * @return int
	 */
	//public function getModifiers() {}
	

	
	/**	 
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}
	/**
	 * Returns the name of the method represented by this Method object, as a String.
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	

	/**	 
	 *
	 * @param string $mappedName
	 * @return void
	 */
	public function setMappedName($mappedName) {
		$this->mappedName = $mappedName;
	}
	
	/**
	 * Returns the name of the method represented by this Method object, as a String.
	 * @return string
	 */
	public function getMappedName() {
		return $this->mappedName;
	}
	
	
	/**	 
	 *
	 * @param MappedClass[] $exceptionTypes
	 * @return void
	 */
	public function setExceptionTypes($exceptionTypes) {
		$this->exceptionTypes = $exceptionTypes;
	}

	
	/**
	 * Returns an array of Class objects that represent the types of the exceptions declared to be thrown by the underlying method represented by this Method object. 
	 * @return MappedClass[]
	 */
	public function getExceptionTypes() { 
		return $this->exceptionTypes;
	}
	
	
	
	/**	 
	 *
	 * @param MappedClass[] $parameterTypes
	 * @return void
	 */
	public function setParameterTypes($parameterTypes) {
		$this->parameterTypes = $parameterTypes;
	}

	
	/**
	 * Returns an array of Class objects that represent the formal parameter types, 
	 * in declaration order, of the method represented by this Method object. 
	 * @return MappedClass[]
	 */
	public function getParameterTypes() { 
		return $this->parameterTypes;
	}
	
	
	/**	 
	 *
	 * @param MappedClass $returnType
	 * @return void
	 */
	public function setReturnType(MappedClass $returnType) {
		$this->returnType = $returnType;
	}
	

		
	
	/**
	 * Returns a Class object that represents the formal return type 
	 * of the method represented by this Method object. 
	 * @return MappedClass
	 */
	public function getReturnType() { 
		return $this->returnType;
	}
	
	
	/**
	 * Invokes the underlying mapped method represented by this MappedMethod object, 
	 * on the specified object with the specified parameters.
	 *
	 * @param Object $target
	 * @param Object[] $args
	 * @return Object
	 */
	public function invoke($target, $args) {
		
		if ($target == null) {
			$instance = $this->getDeclaringMappedClass()->newInstance();
			$method = $this->getDeclaringMappedClass()->getReflectionClass()->getMethod($this->getMappedName());
			return ($method->invokeArgs($instance,$args));
		} else if ($target instanceof ReflectionClass ){
			$instance = $target->newInstance();			
			$method = $target->getMethod($this->getMappedName());
			return ($method->invokeArgs($instance,$args));
		} else {
			$clazz = new ReflectionClass(get_class($target));
			$method = $clazz->getMethod($this->getMappedName());
			return ($method->invokeArgs($target,$args));
			//require_once(GWTPHP_DIR.'/maps/java/lang/UnimplementedOperationException.class.php');
			//throw new UnimplementedOperationException("Invoking method for null target not implemented yet");
		}
	}
}

?>