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
require_once(GWTPHP_DIR.'/lang/AbstractMappedClassLoader.class.php');
require_once(GWTPHP_DIR.'/lang/SimpleMappedClass.class.php');
require_once(GWTPHP_DIR.'/lang/SimpleMappedMethod.class.php');
require_once(GWTPHP_DIR.'/lang/SimpleMappedField.class.php');

require_once(GWTPHP_DIR.'/lang/JavaSignatureUtil.class.php');
require_once(GWTPHP_DIR.'/GWTPHPContext.class.php');
class ArrayMappedClassLoader extends AbstractMappedClassLoader {
	
	/**
	 * 
	 * @var array
	 */
	private $classMaps;
	
	protected static $commonClassMaps = array(	
			array( 
				'className' => 'com.google.gwt.user.client.rpc.SerializableException' ,
				'mappedBy' => 'gwtphp.SerializableException' ,
				'fields' => array (
								array (
									'name' => 'message',
									'type'=>'java.lang.String',								
									'typeCRC' => '2004016611',
								)
				
				
						)	 
				
			)
		);
	
	/**
	 * Enter description here...
	 *
	 * @param array $classMaps
	 * @param SimpleClassLoader $classLoader
	 * @param SimpleClassMapLoader $classMapLoader
	 */
	function __construct($classMaps, SimpleClassLoader $classLoader = null, SimpleClassMapLoader $classMapLoader = null) {
		$this->classMaps = $classMaps;
		foreach (ArrayMappedClassLoader::$commonClassMaps as $map) {
			$this->classMaps[] = $map;
		}
		
		$this->setClassLoader( ($classLoader !== null) ? $classLoader : $this->getDefaultClassLoader());
		$this->setClassMapLoader( ($classMapLoader !== null) ? $classMapLoader : $this->getDefaultClassMapLoader());
		
		
	}
		
	/**
	 * 
	 * @param string $className - signature
	 * @param boolean $cachable - to cache or not to cache
	 * @return MappedClass
	 * @throws ClassNotFoundException
	 * @throws ClassMapNotFoundException
	 */
	public function loadMappedClass($className,$cachable = true) {
		$_class = $this->getCachedMappedClass($className);
		if ($cachable && $_class != null) {
			return $_class;
		}
		if (JavaSignatureUtil::isPrimitive($className) 
			|| JavaSignatureUtil::isArray($className)
			|| JavaSignatureUtil::isVoid($className)
			) {
			$_class= $this->forSignature($className);
			
		} 
		else if (JavaSignatureUtil::isGeneric($className)) {
			$_type = JavaSignatureUtil::getSignatureForGenericType($className);
			$_pTypes = JavaSignatureUtil::getSignaturesForGenericTypeParameters($className);
			$_class = $this->loadMappedClass($_type,false);
			$_class->setGeneric(true);
			$_pClasses/*MappedClass*/ = array();
			foreach ($_pTypes as $_pType) {
				$_pClasses[] = $this->loadMappedClass($_pType);
			}
			$_class->setTypeParameters($_pClasses);
		}
		else if (($_class = $this->getNative($className)) !== null) {
			//ok class found -> go on...
			//return $_class;
		}
		else {			
			if (($_class = $this->findMappedClass($className)) == null) {
				//$phpClass = $this->classLoader->loadClass($_class->getMappedBy());
				
				//} else {
				require_once(GWTPHP_DIR.'/maps/java/lang/ClassMapNotFoundException.class.php');
				throw new ClassMapNotFoundException($className);
				//$phpClass = $this->classLoader->loadClass($className);
				//$_class = new SimpleMappedClass();
			}			
			//$_class->setSignature($className);
			//$_class->setPHPClass($phpClass);
		}

		//$this->cachedClasses[$_class->getSignature()] = $_class;
		//$this->addMappedClassToCache($_class->getSignature(),$_class);
		$cachable && $this->addMappedClassToCache($className,$_class);
		//$this->addMappedClassToCache($className,$_class);
		//$this->cachedClasses[$className] = $_class;
		
		return $_class;
		
	}
	
	/**
	 * Looking for MappedClass
	 *
	 * @param ReflectionClass $reflectionClass
	 * @return MappedClass
	 */
	function findMappedClassByReflectionClass(ReflectionClass $reflectionClass) {
		$mappedClass = $this->findCachedClassByReflectionClass($reflectionClass);
		if ($mappedClass != null) return $mappedClass;
		//SimpleRecursiveDTO
		//fixture.SimpleRecursiveDTO
		foreach ($this->classMaps as $classMap) {
			
			$cMap = $classMap['mappedBy'];
			$cMap = str_replace('.','/',$cMap);
			$cMap.=$this->getClassLoader()->getFilePostfix();
			
			$rName = $reflectionClass->getFileName();
			
			foreach ($this->getClassLoader()->getClassPaths() as $rootPath) {
					
					if ($rName == $rootPath.'/'.$cMap) {
						return $this->loadMappedClass($classMap['className']);
						//return null;
					}
					
				}
				
			//if ($classMap['mappedBy'] == $reflectionClass->getName()) {
				
				//$s = new SimpleClassLoader();
				//$s->getClassPaths()
//				foreach ($this->getClassLoader()->getClassPaths() as $rootPath) {
//					
//					if ($reflectionClass->getFileName() == $rootPath.$reflectionClass->getName().$this->getClassLoader()->getFilePostfix()) {
//						return $classMap;
//					}
//					
//				}
//			}
		}
		return null;
	}
	
	/**
	 * Looking for MappedClass
	 *
	 * @param Object $object
	 * @return MappedClass
	 */
	public function findMappedClassByObject($object) {		
		return $this->findMappedClassByReflectionClass(new ReflectionObject($object));
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $className
	 * @return MappedClass
	 */
	private function findMappedClass($className) {
		/*echo $className ."\n<br>";
		try {
			$this->getClassMapLoader()->loadClassMap($className);
			echo "FOUND -- \n<br>";
		} catch (ClassMapNotFoundException $ex) {
			
			echo $ex->getMessage()." NOT found -- \n<br>";
		}*/
		
		
		
		foreach ($this->classMaps as $classMap) {
			if ($classMap['className'] == $className) {
				$_class = new SimpleMappedClass();
				$_class->setClassLoader($this->getClassLoader());
				$_class->setSignature($className);
				$_class->setMappedName($classMap['mappedBy']);
				
				//$_class->setPHPClass($this->classLoader->loadClass($classMap['mappedBy']));
				$_methods = array();
				if (isset($classMap['methods'])) {	
					foreach ($classMap['methods'] as $methodMap) {
						
						$_method = new SimpleMappedMethod();
						$_retClass = $this->loadMappedClass($methodMap['returnType']);
						if (isset($methodMap['returnTypeCRC']))
							$_retClass->setCRC($methodMap['returnTypeCRC'],true);
						$_method->setReturnType($_retClass);
						$_method->setMappedName($methodMap['mappedName']);
						$_method->setName($methodMap['name']);
						
						$_params = array();					
						foreach ($methodMap['params'] as $paramMap) {
							$_params[] = $this->loadMappedClass($paramMap['type']);
						}
						
						$_method->setParameterTypes($_params);
						
						$_method->setDeclaringMappedClass($_class);
						
						$_exceptions = array();
						if (isset($methodMap['throws'])) {					
							foreach ($methodMap['throws'] as $exceptionMap) {
								$_exception = new SimpleMappedField();
								
								$_exception = $this->loadMappedClass($exceptionMap['type']);
													
								if (isset($exceptionMap['typeCRC']))
									$_exception->setCRC($exceptionMap['typeCRC'],true);
								
								
								$_exceptions[] = $_exception;
							}						
						}
						$_method->setExceptionTypes($_exceptions);
						
						$_methods[] = $_method;
					}
				}
				$_class->setMappedMethods($_methods);
				
				$_fields = array();
				if (isset($classMap['fields'])) {					
					foreach ($classMap['fields'] as $fieldMap) {
						$_field = new SimpleMappedField();
						$_field->setName($fieldMap['name']); 
						$_fieldType = $this->loadMappedClass($fieldMap['type']);
											
						if (isset($fieldMap['typeCRC']))
							$_fieldType->setCRC($fieldMap['typeCRC'],true);
							
						$_field->setType($_fieldType);	
					
						$_field->setDeclaringMappedClass($_class);
						$_fields[] = $_field;
					}						
				}
				$_class->setMappedFields($_fields);	
				
				
			
				
				if (isset($classMap['extends'])) {
					$_class->setSuperclass($this->loadMappedClass($classMap['extends']));
				}
				if (isset($classMap['typeCRC'])) {
					$_class->setCRC($classMap['typeCRC']);
				}
				return $_class; 
			}
		}
	}
	
	private function forSignature($signature) {
		if (JavaSignatureUtil::isVoid($signature)) {
			$_class = new SimpleMappedClass();
			$_class->setPrimitive(false);
			$_class->setSignature($signature);			
			return $_class;
		} else
		if (JavaSignatureUtil::isPrimitive($signature)) {
			$_class = new SimpleMappedClass();
			$_class->setPrimitive(true);
			$_class->setSignature($signature);
			
			return $_class;
		} else
		if (JavaSignatureUtil::isArray($signature)) {
			$_class = new SimpleMappedClass();
			$_class->setPrimitive(true);
			$_class->setSignature($signature);
			$_class->setArray(true);
			$_class->setComponentType( $this->loadMappedClass(JavaSignatureUtil::getSignatureForComponentTypeOfArray($signature)));

			return $_class;
		} else {
			require_once(GWTPHP_DIR.'/maps/java/lang/SignatureParseException.class.php');
			throw new SignatureParseException("Signature for not primitive or array type: ".$signature);
		}
	}
	
	/**
	 * returns default class loader (used by getClassLoader if class loader is null)
	 * @return ClassLoader
	 */
	public function getDefaultClassLoader() {
		return GWTPHPContext::getInstance()->getClassLoader();
		//new SimpleClassLoader();
	}
	/**
	 * returns default class loader (used by getClassMapLoader if class loader is null)
	 * @return ClassMapLoader
	 */
	public function getDefaultClassMapLoader() {
		return GWTPHPContext::getInstance()->getClassMapLoader();
		//new SimpleClassLoader();
	}
	
}

?>