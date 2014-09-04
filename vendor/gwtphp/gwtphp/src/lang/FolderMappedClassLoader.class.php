<?php
/*
 * GWTPHP is a port to PHP of the GWT RPC package.
 * 
 * <p>This framework is based on GWT (see {@link http://code.google.com/webtoolkit/ gwt-webtoolkit} for details).</p>
 * <p>Design, strategies and part of the methods documentation are developed by Google Team  </p>
 * 
 * <p>PHP port, extensions and modifications by Rafal M.Malinowski. All rights reserved.<br>
 * Additional modifications, GWT generators and linkers by Yifei Teng. All rights reserved.<br>
 * For more information, please see {@link https://github.com/tengyifei/gwtphp}</p>
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

class FolderMappedClassLoader extends AbstractMappedClassLoader {
	
	private $logger;
	
	/**
	 * @param SimpleClassLoader $classLoader
	 * @param SimpleClassMapLoader $classMapLoader
	 */
	function __construct( SimpleClassLoader $classLoader = null, SimpleClassMapLoader $classMapLoader = null) {
		
		$this->setClassLoader( ($classLoader !== null) ? $classLoader : $this->getDefaultClassLoader());
		$this->setClassMapLoader( ($classMapLoader !== null) ? $classMapLoader : $this->getDefaultClassMapLoader());
		
		$this->logger = Logger::getLogger('gwtphp.rpc.RPC');
	}
		
	/**
	 * 
	 * @param string $className - signature
	 * @param boolean $cachable - to cache or not to cache (sometimes we do not cache generic classes)
	 * @return MappedClass
	 * @throws ClassNotFoundException
	 * @throws ClassMapNotFoundException
	 */
	public function loadMappedClass($className, $cachable = true) {
		$className = explode('/', $className, 2);		//ignore CRC information
		$className = $className[0];
		
		$_class = $this->getCachedMappedClass($className);
		if ($cachable && $_class != null) {
			return $_class;
		}
		
		$this->logger->debug("Load mapped class: ".$className);
		if (JavaSignatureUtil::isPrimitive($className) 
			|| JavaSignatureUtil::isArray($className)
			|| JavaSignatureUtil::isVoid($className)
			) {
			$_class= $this->forSignature($className);
			$cachable && $this->addMappedClassToCache($className,$_class);
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
			$cachable && $this->addMappedClassToCache($className,$_class);
		}
		else if (($_class = $this->getNative($className)) !== null) {
			//ok class found -> go on...
			$cachable && $this->addMappedClassToCache($className,$_class);
		}
		else {			
			if (($_class = $this->findMappedClass($className,$cachable)) == null) {
				require_once(GWTPHP_DIR.'/maps/java/lang/ClassMapNotFoundException.class.php');
				throw new ClassMapNotFoundException("Class map not found for class: ".$className);
			}
		}

		
		return $_class;
		
	}
	/**
	 * 
	 * 
	 * @param string $classFileName
	 * @return string class map name
	 * @throws ClassFileNotFoundException
	 */
	private function convertClassFileNameToMapFileName($classFileName) {

		$pos = strrpos($classFileName, $this->getClassLoader()->getFilePostfix());		
		if (false === $pos) {
			require_once(GWTPHP_DIR.'/maps/java/lang/ClassFileNotFoundException.class.php');
			throw new ClassFileNotFoundException("RuntimeException: Unknown format of class file name: '$classFileName'. We expect following postfix: ".$this->getClassLoader()->getFilePostfix());			
		}
		
		$mapFileName = substr($classFileName,0,$pos).$this->getClassMapLoader()->getFilePostfix();
		return $mapFileName;
	}
	
	/**
	 * Looking for MappedClass
	 *
	 * @param ReflectionClass $reflectionClass
	 * @return MappedClass
	 * @throws ClassMapNotFoundException
	 */
	public function findMappedClassByReflectionClass(ReflectionClass $reflectionClass) {
		$classMapFileName = $this->convertClassFileNameToMapFileName($reflectionClass->getFileName());
		
		$this->logger->debug("Load class map file: ".$classMapFileName);
		if (file_exists($classMapFileName)) {
			$gwtphpmap = $this->getClassMapLoader()->findGWTPHPMapInFile($classMapFileName);
			
			if (isset($gwtphpmap['className'])) {
				//return $this->classMapToMappedClass($innermap);
				return $this->loadMappedClass($gwtphpmap['className']);
			} else if (is_array($gwtphpmap)) {
				foreach ($gwtphpmap as $innermap) {
					//&& $reflectionClass->getFileName() == ""
					//$varib =  $this->getClassLoader()->getClassSimpleName($innermap['mappedBy']) ;
					if (isset($innermap['className']) 
					&& isset($innermap['mappedBy']) 
					&& $this->getClassLoader()
						->getClassSimpleName($innermap['mappedBy']) == $reflectionClass->getName()) {

						return $this->loadMappedClass($innermap['className']);
						//return $this->classMapToMappedClass($innermap);
					}
				}
			} 
			
			require_once(GWTPHP_DIR.'/maps/java/lang/ClassMapNotFoundException.class.php');			
			throw new ClassMapNotFoundException('Found class map array but without className in file: '
				.$this->convertClassFileNameToMapFileName($reflectionClass->getFileName()));
		}
		
		require_once(GWTPHP_DIR.'/maps/java/lang/ClassMapNotFoundException.class.php');			
		throw new ClassMapNotFoundException('Not found class map: '.$classMapFileName);
	}
	
	/**
	 * Looking for MappedClass
	 *
	 * @param Object $object
	 * @return MappedClass
	 * @throws ClassMapNotFoundException
	 */
	public function findMappedClassByObject($object) {		
		return $this->findMappedClassByReflectionClass(new ReflectionObject($object));
	}
	
	/**
	 *
	 * @param string $className
	 * @return MappedClass
	 * @throws ClassMapNotFoundException
	 * 
	 */
	private function findMappedClass($className,$cachable = true) {
		
		$this->logger->debug("Find mapped class: ".$className);
		
		$classMap = $this->getClassMapLoader()->loadClassMap($className);		
		if (null !== $classMap && is_array($classMap)) {
			$searchedClassName = JavaSignatureUtil::innecJavaClassNameToPHPClassName($className);
			if ( isset($classMap['className']) && $classMap['className'] == $className) {
				return $this->classMapToMappedClass($classMap,$cachable);
			} else {
				foreach ($classMap as $innerClassMap) {
					if ( isset($innerClassMap['className']) && $innerClassMap['className'] == $className) {
						return $this->classMapToMappedClass($innerClassMap,$cachable);
					} 
				}
			}
		}
		
//			if ($classMap['className'] == $className) {
			
		
	}
	
	private function classMapToMappedClass($classMap,$cachable = true) {
		if (null !== $classMap) {
			$className = $classMap['className'];
			
				$_class = new SimpleMappedClass();

				// to prevent infinitive recursive loop we cache class before initiate it  
				if ($cachable) $this->addMappedClassToCache($className,$_class);
				
				$_class->setClassLoader($this->getClassLoader());
				$_class->setSignature($className);
				if (isset($classMap['mappedBy']))
					$_class->setMappedName($classMap['mappedBy']);
				else 
					$_class->setMappedName($className);
				if (isset($classMap['isInterface']) && $classMap['isInterface'] == 'true') {					
					$_class->setInterface(true);
				}
				if (isset($classMap['isAbstract']) && $classMap['isAbstract'] == 'true') {					
					$_class->setAbstract(true);
				}
				//$_class->setPHPClass($this->classLoader->loadClass($classMap['mappedBy']));
				$_methods = array();
				if (isset($classMap['methods'])) {	
					foreach ($classMap['methods'] as $methodMap) {
						
						$_method = new SimpleMappedMethod();
						$_retClass = $this->loadMappedClass($methodMap['returnType']);
						if (isset($methodMap['returnTypeCRC'])) {
							$_retClass->setCRC($methodMap['returnTypeCRC'],true);
						} else {
							$crc = SerializabilityUtil::getSerializationSignature($methodMap['returnType']);
							if (null !== $crc) {
								$_retClass->setCRC($crc,false);
							}
						}
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
								//$_exception = new SimpleMappedField();
								
								$_exception = $this->loadMappedClass($exceptionMap['type']);
													
								if (isset($exceptionMap['typeCRC'])) {
									$_exception->setCRC($exceptionMap['typeCRC'],true);
								} else {
									$crc = SerializabilityUtil::getSerializationSignature($exceptionMap['type']);
									if (null !== $crc) {
										$_exception->setCRC($crc,false);
									} 
//									else {
//										require_once(GWTPHP_DIR.'/maps/com/google/gwt/user/client/rpc/SerializableException.class.php');
//										throw new SerializableException("Did not found serialization signature for : "
//										.$_exception->getName()." in class map file (".$_exception->getName()
//										.".gwtphpmap.inc.php). Did you forget to put 'typeCRC' value in array map?.");
//									}
								}
								
								
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
											
						if (isset($fieldMap['typeCRC'])) {
							$_fieldType->setCRC($fieldMap['typeCRC'],true);
						} else {
							$crc = SerializabilityUtil::getSerializationSignature($fieldMap['type']);
							if (null !== $crc) {
								$_fieldType->setCRC($crc,false);
							}
						}	
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
				} else {
							$crc = SerializabilityUtil::getSerializationSignature($className);
							if (null !== $crc) {
								$_retClass->setCRC($crc,false);
							}
						}	
				return $_class; 
			} 
			else return null;
	}
	/**
	 * 
	 *
	 * @param string $signature
	 * @return SimpleMappedClass
	 * @throws SignatureParseException
	 */
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
			$_class->setPrimitive(false);
			$_class->setSignature(JavaSignatureUtil::isGeneric($signature)
				? 	JavaSignatureUtil::getSignatureForGenericType($signature)
				:	$signature );
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
	}
	
	/**
	 * returns default class loader (used by getClassMapLoader if class loader is null)
	 * @return ClassMapLoader
	 */
	public function getDefaultClassMapLoader() {
		return GWTPHPContext::getInstance()->getClassMapLoader();
	}
	
}

?>