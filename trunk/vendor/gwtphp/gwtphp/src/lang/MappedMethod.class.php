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

interface MappedMethod {
	
	
	/**
	 * Returns the MappedClass object representing the class or interface 
	 * that declares the mapped method represented by this MappedMethod object.
	 * 
	 * @return MappedClass
	 */
	public function getDeclaringMappedClass();
	
	/**
	 * Returns the Java language modifiers for the method represented by this Method object, as an integer. 
	 * @return int
	 */
	//public function getModifiers();
	
	
	/**
	 *   Returns the name of the method represented by this Method object, as a String.
	 * @return string
	 */
	public function getName();
	
	/**
	 *   Returns the name of the method represented by this Method object, as a String.
	 * @return string
	 */
	public function getMappedName();
	
	
	/**
	 * Returns an array of Class objects that represent the types of the exceptions declared to be thrown by the underlying method represented by this Method object. 
	 * @return MappedClass
	 */
	public function getExceptionTypes();
	
	
	/**
	 * Returns an array of Class objects that represent the formal parameter types, 
	 * in declaration order, of the method represented by this Method object. 
	 * @return MappedClass[]
	 */
	public function getParameterTypes();
	
	
	/**
	 * Returns a Class object that represents the formal return type 
	 * of the method represented by this Method object. 
	 * @return MappedClass
	 */
	public function getReturnType();
	
	
	/**
	 * Invokes the underlying mapped method represented by this MappedMethod object, 
	 * on the specified object with the specified parameters.
	 *
	 * @param Object $target
	 * @param Object[] $args
	 * @return Object
	 */
	public function invoke($target, $args);
   
}
?>