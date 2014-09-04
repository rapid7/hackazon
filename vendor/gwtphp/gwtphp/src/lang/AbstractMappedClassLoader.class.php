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
require_once(GWTPHP_DIR.'/lang/MappedClassLoader.class.php');

abstract class AbstractMappedClassLoader implements MappedClassLoader {

	/**
	 *
	 * @var array<String,MappedClass>
	 */
	private static $cachedClasses /*<String,MappedClass>*/ = array();

	
		
	/**
	 * 
	 * @var ClassLoader
	 */
	private $classLoader;
	
	
	/**
	 * 
	 * @var ClassMapLoader
	 */
	private $classMapLoader;
	
	
	
		
		
	/**
	 * returns cached class
	 *
	 * @param string $className
	 * @return MappedClass
	 */
	public function getCachedMappedClass($className) {	    
		if (isset(AbstractMappedClassLoader::$cachedClasses[$className]))
			return AbstractMappedClassLoader::$cachedClasses[$className];
		else return null;
	}
	
	public function findCachedClassByReflectionClass(ReflectionClass $reflectionClass) {
		foreach (AbstractMappedClassLoader::$cachedClasses as  $cachedClass) {
//			$cachedClass = new MappedClass();
			if(!$cachedClass->isPrimitive() 
			&& $cachedClass->getReflectionClass() != null 
			&& $cachedClass->getReflectionClass()->getName() == $reflectionClass->getName() 
			&& $cachedClass->getReflectionClass()->getFileName() == $reflectionClass->getName() ) 
				return $cachedClass;
		}
		return null;
	}
	
	/**
	 * add or overwrite MappedClass in local cache
	 *
	 * @param string $className
	 * @param MappedClass $mappedClass
	 */
	public function addMappedClassToCache($className,MappedClass $mappedClass) {
		AbstractMappedClassLoader::$cachedClasses[$className] = $mappedClass;
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
	 * @overriden
	 * @return ClassLoader
	 */
	public function getClassLoader() {
		return ($this->classLoader == null) ? $this->classLoader = $this->getDefaultClassLoader() : $this->classLoader;
	}
	
	/**	 
	 *
	 * @param ClassMapLoader $classMapLoader
	 * @return void
	 */
	public function setClassMapLoader(ClassMapLoader $classMapLoader) {
		$this->classMapLoader = $classMapLoader;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $className
	 * @return SimpleMappedClass
	 */
	public function getNative($className) {	
		if (JavaSignatureUtil::isNative($className)) {
			$class = new SimpleMappedClass();
			$class->setClassLoader($this->getClassLoader());
			$class->setSignature($className);
			$class->setMappedName($className);
			$class->setCRC(JavaSignatureUtil::getSerializationSignatureForNative($className));
			return $class;
		}
		else return null;
	}
	
	/** 
	 *
	 * @return ClassMapLoader
	 */
	public function getClassMapLoader() {
		return ($this->classMapLoader == null) ? $this->classMapLoader = $this->getDefaultClassMapLoader() : $this->classMapLoader;
	}
	
	/**
	 * returns default class loader (used by getClassLoader if class loader is null)
	 * @return ClassLoader
	 */
	public abstract function getDefaultClassLoader();
	
	/**
	 * returns default class loader (used by getClassMapLoader if class loader is null)
	 * @return ClassMapLoader
	 */
	public abstract function getDefaultClassMapLoader();
}

?>