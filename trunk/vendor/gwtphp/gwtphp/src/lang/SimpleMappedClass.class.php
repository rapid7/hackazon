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
require_once(GWTPHP_DIR.'/lang/MappedClass.class.php');

class SimpleMappedClass implements MappedClass {
	/**
	 * @var string
	 */
	private $signature = "";

	/**
	 * is primitive type flag
	 * @var boolean
	 */
	private $primitive = false;

	/**
	 * is generic type flag
	 * @var boolean
	 */
	private $generic = false;

	/**
	 * is interface type flag
	 * @var boolean
	 */
	private $interface = false;
	
	private $isabstract = false;
	
	/**
	 * is array flag
	 * @var boolean
	 */
	private $array = false;

	
	/**
	 * 
	 * @var ReflectionClass
	 */
	private $reflectionClass;


	/**
	 * 
	 * @var MappedClass
	 */
	private $componentType;	
	
	
	/**
	 * 
	 * @var array<MappedClass>
	 */
	private $typeParameters;
	
	
	
	/**
	 * 
	 * @var MappedClass
	 */
	private $superclass;
	
	
	/**	 
	 *
	 * @param MappedClass $superclass
	 * @return void
	 */
	public function setSuperclass(MappedClass $superclass) {
		$this->superclass = $superclass;
	}
	
	/** 
	 *
	 * @return MappedClass
	 */
	public function getSuperclass() {
		return $this->superclass;
	}
		
	

	/**
	 * 
	 * @var string
	 */
	private $mappedName;
	
	
	/**
	 * 
	 * @var MappedMethod[]
	 */
	private $mappedMethods;
	
	
	/**
	 * 
	 * @var MappedField[]
	 */
	private $mappedFields;
	
	
	
	
	/**
	 * 
	 * @var ClassLoader
	 */
	private $classLoader;

	
	/**
	 * 
	 * @var string
	 */
	private $crc;
	
	
	/**	 
	 * 
	 * @param string $crc
	 * @return void
	 * @throws CRCParseException
	 */
	public function setCRC($crc,$parse = false) {
		if ($parse && $this->isArray() ) {			
			$pos = strpos($crc,'[');
			if ($pos === false && !$this->getComponentType()->isPrimitive()) {
				require_once(GWTPHP_DIR.'/maps/java/lang/CRCParseException.class.php');
				throw new CRCParseException('\'[\' not found in:'.$crc);
				
			}
			//echo $pos;
			//echo "<br>\n";
			$this->crc = substr($crc,0,$pos);
			
			if ($this->getComponentType()->isPrimitive() && !$this->getComponentType()->isArray()) {				
				return;
			}
			//$baseCrc = substr($crc,0,$pos);
			//echo $baseCrc;
			//echo "<br>\n";
			$nextCRC = substr($crc,-strlen($crc)+$pos+1);
			if ($nextCRC[0]=='[' && $this->getComponentType()->isArray()) {
				$this->getComponentType()->setCRC($nextCRC,true);
			} else if ($nextCRC[0]=='L' && !$this->getComponentType()->isPrimitive()) {
				$this->getComponentType()->setCRC(substr($nextCRC,1,strlen($nextCRC)-2),true);
			} else if (($this->getComponentType()->isArray() || $this->getComponentType()->isArray()) && $nextCRC[0]!='[' && $nextCRC[0]!='L') {
				$this->getComponentType()->setCRC($nextCRC,true);
			} else {
				require_once(GWTPHP_DIR.'/maps/java/lang/CRCParseException.class.php');
				throw new CRCParseException($crc);
				
			}
			//echo $nextCRC;
			//echo "<br>\n";
		}
		else {
			try {
				if ($this->isGeneric()) {
					$this->crc = JavaSignatureUtil::getSignatureForGenericType($crc);
					$crcParams = JavaSignatureUtil::getSignaturesForGenericTypeParameters($crc);
					
					$params = $this->getTypeParameters();
					
					$crcParamsCount = count($crcParams);
					$paramsCount = count($params);
					if ($crcParamsCount != $paramsCount) {
						require_once(GWTPHP_DIR.'/maps/java/lang/CRCParseException.class.php');
						throw new CRCParseException("Parsing generic CRC error: (probably not equal count of typeParameters: $paramsCount and count of typeCRC: $crcParamsCount) CRC: " .$crc);
					}
					for ($i = 0; $i < $paramsCount; ++$i) {
						$params[$i]->setCRC($crcParams[$i]);
					}
					
				} else {
					$this->crc = $crc;
				}
			} catch (SignatureParseException $ex) {
				require_once(GWTPHP_DIR.'/maps/java/lang/CRCParseException.class.php');
				throw new CRCParseException('Parsing generic CRC error: (probably not equal count of \'< and \'> '.$crc);
			}
			
		}
		
	}
	
	/** 
	 * CRC examples:
	 * Signature / CRC
	 * I / 1438268394
	 * java.lang.Integer / 3438268394
	 * [Ljava.lang.Integer; / 3787802054[L3438268394;
	 * [[Ljava.lang.Integer; / 3984106052[3787802054[L3438268394;
	 * @return string
	 */
	public function getCRC() {		
		return $this->crc;
	}
		
		
	/**
	 * 
	 * @param string $methodName
	 * @param MappedClass[] $parameterTypes
	 * @return MappedMethod
	 * @throws NoSuchMethodException
	 */
	public function getDeclaredMethod( $methodName, $parameterTypes) {
		$methodRet = null;
		foreach ($this->getMappedMethods() as $method) {
			if ($method->getName() == $methodName) {
				$methodRet = $method;
				foreach ($method->getParameterTypes() as $idx => $parameterType) {
					if ($parameterType->isGeneric()) {
						//if generic then only signatures check
						
						if ($parameterType->getSignature() != $parameterTypes[$idx]->getSignature()) {
							$methodRet = null;
							break;
						}
					} else 
					if ($parameterType !== $parameterTypes[$idx]) {
						$methodRet = null;
						break;
					}
				}
				
			}
		}
		if ($methodRet === null && $this->getSuperclass() !== null) {
			$methodRet = $this->getSuperclass()->getDeclaredMethod( $methodName, $parameterTypes);
		}
		if ($methodRet === null) {
			require_once(GWTPHP_DIR.'/maps/java/lang/NoSuchMethodException.class.php');
			throw new NoSuchMethodException($methodName);
		}
		return $methodRet;
	}
	
	/**
	 * @return Object
	 * @throws InstantiationException
	 * @throws IllegalAccessException
	 */
	public function newInstance() {
		if ($this->isPrimitive()) return $var;
		if ($this->isArray()) return array();
		return $this->getReflectionClass()->newInstance();
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
	 *
	 * @return string
	 */
	public function getMappedName() {
		return $this->mappedName;
	}

	
	
	/**	 
	 *
	 * @param MappedMethod[] $mappedMethods
	 * @return void
	 */
	public function setMappedMethods( $mappedMethods) {
		$this->mappedMethods = $mappedMethods;
	}
	
	/** 
	 *
	 * @return MappedMethod[]
	 */
	public function getMappedMethods() {
		return $this->mappedMethods;
	}
	
	/**	 
	 *
	 * @param MappedField[] $mappedFields
	 * @return void
	 */
	public function setMappedFields( $mappedFields) {
		$this->mappedFields = $mappedFields;
	}
	
	/** 
	 *
	 * @return MappedField[]
	 */
	public function getMappedFields() {
		return $this->mappedFields;
	}
		
	
		/** 
	 *
	 * @return MappedField[]
	 */
	public function getDeclaredFields() {
		return $this->mappedFields;
	}
		
	/**
	 * 
	 * @return string
	 *
	 */
	public function getName() {
		if( $this->isPrimitive() ) {
			return JavaSignatureUtil::signatureToName($this->getSignature());
		}
		return $this->getSignature();
		//return $this->getPHPClass()->getName();
	}
	
	/**
	 * @return string
	 */
	public function getSimpleName() {
		return $this->getSimpleForName($this->getName());
	}
	
	/**
	 * @return string
	 */
	public function getSimpleMappedName() {
		return $this->getSimpleForName($this->getMappedName());
	}
	
	/**
	 * @return string
	 */
	private function getSimpleForName($name) {		
		return substr($name,strrpos($name,'.')+1,strlen($name));
	}
	
	
	public function __construct() {
	}
	/**
	 *
	 * @param boolean $array
	 * @return void
	 */
	public function setArray($array) {
		$this->array = ($array === true);
	}

	/**
	 *
	 * @return boolean
	 */
	public function isArray() {
		return $this->array;
	}

	/**
	 * @param boolean $primitive
	 * @return void
	 */
	public function setPrimitive($primitive) {
		$this->primitive = ($primitive === true);
	}
	/**
	 * @return boolean
	 */
	public function isPrimitive() {
		return $this->primitive;
	}
	
	/**
	 * @param string $signature
	 * @return void
	 */
	public function setSignature($signature) {
		$this->signature = $signature;
	}
	/**
	 * @return string
	 */
	public function getSignature() {
		return $this->signature;
	}
		
	/**	 
	 *
	 * @param ReflectionClass $reflectionClass
	 * @return void
	 */
	public function setReflectionClass(ReflectionClass $reflectionClass) {
		$this->reflectionClass = $reflectionClass;
	}
	
	/** 
	 *
	 * @return ReflectionClass (null if mappedName == null)
	 */
	public function getReflectionClass() {
		
		if ($this->reflectionClass == null && $this->getMappedName() != null) {
			if ($this->getSuperclass()!==null) {
				$this->getSuperclass()->getReflectionClass();
			}
			$this->reflectionClass = $this->getClassLoader()->loadClass($this->getMappedName());
		}
		return $this->reflectionClass;
	}	
	
	/**
	 *
	 * @param MappedClass $componentType
	 * @return void
	 */
	public function setComponentType(MappedClass $componentType) {
		$this->componentType = $componentType;
	}

	/**
	 *
	 * @return MappedClass
	 */
	public function getComponentType() {
		return $this->componentType;
	}
	
	
	/**	 
	 *
	 * @param ClassLoader $classLoader
	 * @return void
	 */
	public function setClassLoader(ClassLoader $classLoader) {
		$this->classLoader = $classLoader;
	}
	
	/** 
	 *
	 * @return ClassLoader
	 */
	public function getClassLoader() {
		return $this->classLoader;
	}
	//public static function forName($name,$initialize,MappedClassLoader $mappedClassLoader) {
	//	return $mappedClassLoader->loadMappedClass($name);
	//}
	
	/**	 
	 *
	 * @param array<MappedClass> $typeParameters
	 * @return void
	 */
	public function setTypeParameters($typeParameters) {
		$this->typeParameters = $typeParameters;
	}
	
	/** 
	 *
	 * @return array<MappedClass>
	 */
	public function getTypeParameters() {
		return $this->typeParameters;
	}
	
	/**
	 * @param boolean
	 */
	public function setGeneric($flag) {
		$this->generic = ($flag === true);
	}
		/**
	 * @return boolean
	 */
	public function isGeneric() {
		return $this->generic;
	}
	
	/**
	 * @return boolean
	 */
	public function isInterface() {
		return $this->interface;
	}
	
	/**
	 * @return boolean
	 */
	public function isAbstract() {
		return $this->isabstract;
	}
	
	/**
	 * @return void
	 */
	public function setInterface($flag) {
		$this->interface = ($flag === true);
	}
	
	/**
	 * @return void
	 */
	public function setAbstract($flag) {
		$this->isabstract = ($flag === true);
	}
		
}

?>