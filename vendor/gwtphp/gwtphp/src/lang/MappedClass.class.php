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
 * @package gwtphp.lang
 */

interface MappedClass {
	
	/**
	 *
	 * @return MappedClass
	 */
	public function getComponentType() ;
	
	/**
	 * Returns the MappedClass representing the superclass of the entity 
	 * (class, interface, primitive type or void) represented by this Class.
	 * @return MappedClass
	 */
	public function getSuperclass();
	
	/**
	 * @return string
	 */
	public function getSignature() ;
	
	/**
	 *
	 * @return boolean
	 */
	public function isArray();
	
	/**
	 * @return boolean
	 */
	public function isPrimitive();
	
	/**
	 * @return boolean
	 */
	public function isGeneric();
	
	/**
	 * @return boolean
	 */
	public function isInterface();
	
	public function isAbstract();
	
	/**
	 * @return boolean
	 */
	public function setInterface($flag);
	
	public function setAbstract($flag);
	
	/**
	 * @return boolean
	 */
	public function setGeneric($flag);
	
	
	/**
	 * @return string
	 */
	public function getName();
	
	/**
	 * @return string
	 */
	public function getMappedName();
	
	/**
	 * @return string
	 */
	public function getSimpleName();
	
	/**
	 * @return string
	 */
	public function getSimpleMappedName();
	
	/**
	 * 
	 * @return string
	 *
	 */
	public function getMappedMethods();
	
	/**
	 * 
	 * @param string $methodName
	 * @param MappedClass[] $parameterTypes
	 * @return MappedMethod
	 * @throws NoSuchMethodException
	 */
	public function getDeclaredMethod( $methodName, $parameterTypes);
	
	/**
	 * @return MappedClass[]
	 */
	public function getDeclaredFields();
	
	
	
	/**
	 * @return Object
	 * @throws InstantiationException
	 * @throws IllegalAccessException
	 */
	public function newInstance() ;
	//public static function forName($name,$initialize,MappedClassLoader $mappedClassLoader);

	/**
	 * Returns the class loader for the class.
	 * ClassLoader
	 */
	public function getClassLoader();
	/**
	 * @return string
	 *
	 */
	public function getCRC();
	
	/**
	 * 
	 *
	 * @param string $crc
	 * @throws CRCParseException
	 */
	public function setCRC($crc,$parse=false); 
	   /** 
	 *
	 * @return ReflectionClass
	 */
	public function getReflectionClass() ;   
	
	
	/** 
	 *
	 * @return array<MappedClass>
	 */
	public function getTypeParameters() ;
	
	/**	 
	 *
	 * @param array<MappedClass> $typeParameters
	 * @return void
	 */
	public function setTypeParameters($typeParameters) ;
}

?>