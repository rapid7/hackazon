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
require_once(GWTPHP_DIR.'/lang/ClassLoader.class.php');

abstract class AbstractClassLoader implements ClassLoader {
	
	private static $DEFAULT_CLASS_FILE_POSTFIX = '.class.php';
	
	/**
	 * 
	 * @var string
	 */
	private $filePostfix ;
	
	/**
	 * 
	 * @var string
	 */
	private $classPaths = array();
	
	
	public function __construct($filePostfix = null) {		
		$this->filePostfix = ($filePostfix == null) ? AbstractClassLoader::$DEFAULT_CLASS_FILE_POSTFIX : $filePostfix;
	}
	
	
	/**
	 * 
	 * @param string $className
	 * @return ReflectionClass
	 * @throws ClassNotFoundException
	 */
	function loadClass($className, $startLookingFrom = null) {
		if (class_exists($this->getClassSimpleName($className))) {
			return $this->instatineClass($this->getClassSimpleName($className)); 
		}
		$classPaths = $this->getFullClassPaths($className);
		if (!is_array($classPaths)) {
			$classPaths = array($classPaths);
		}
		if ($startLookingFrom != null) {
			array_unshift($classPaths,$startLookingFrom);
		}
		foreach ($classPaths as $classPath) {
			//Logger::getLogger('AbstractClassLoader')->info('Search for class: '.$classPath);
			//echo $classPath . "\n";
			if (file_exists($classPath)) {
				require_once($classPath);
				return $this->instatineClass($this->getClassSimpleName($className));
			}
		}
		
		require_once(GWTPHP_DIR.'/maps/java/lang/ClassNotFoundException.class.php');
		throw new ClassNotFoundException($className);
		
	}
	
	public abstract function getFullClassPaths($className);
	/* {
		return $this->classPath.str_replace('.','/',$className).SimpleClassLoader::$postfix;		
	}
	*/
	//public abstract function getClassSimpleName($className);
	 /*{
		$classPath = str_replace('.','/',$className);		
		$pos = strrpos($classPath, '/');
		if ($pos === false) {
		    return $classPath;		
		} else {			
			return substr($classPath,$pos-strlen($classPath)+1);
		}
		
	}*/
	
	public abstract function instatineClass($classSimpleName) ;
/*	{
		return new ReflectionClass($classSimpleName);
	}*/
	
	
	/**	 
	 * Path to classes of your application. This is where classLoader start to lookup for class files
	 * @param string $classPath
	 * @return void
	 */
	public function setClassPath($classPath) {
		$this->classPaths[0] = $classPath;
	}
	/**
	 *
	 * @param String $classPath
	 * @return boolean (false if rootpath exist in rootPaths array
	 */
	public function addClassPath($classPath) {
		foreach ($this->classPaths as $path) {
			if ($path == $classPath) return false;
		}
		$this->classPaths[] = $classPath;
		return true;
	}
	
	/** 
	 *
	 * @return string
	 */
	public function getClassPath() {
		return $this->classPaths[0];
	}
	
	/** 
	 *
	 * @return string
	 */
	public function getClassPaths() {
		return $this->classPaths;
	}
	/**	 
	 *
	 * @param string $filePostfix
	 * @return void
	 */
	public function setFilePostfix($filePostfix) {
		$this->filePostfix = $filePostfix;
	}
	/** 
	 *
	 * @return string
	 */
	public function getFilePostfix() {
		return $this->filePostfix;
	}
	
	
}

?>