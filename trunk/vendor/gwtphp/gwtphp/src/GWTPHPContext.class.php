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
 * @package gwtphp
 */
require_once(GWTPHP_DIR.'/lang/SimpleClassMapLoader.class.php');
require_once(GWTPHP_DIR.'/lang/FolderMappedClassLoader.class.php');

define('FORCE_CAST_TO_PHP_PRIMITIVE_TYPES',false);


// TODO: change to singleton 
class GWTPHPContext {
	
		
	/**
	 * 
	 *
	 * @var string
	 */
	private static $GWTPHP_MAPS_FOLDER = '/maps';
	
	/**
	 * 
	 *
	 * @var GWTPHPContext
	 */
	private static $instance;
	
	const GWT_VERSION_1_5_0 = 1500;
	const GWT_VERSION_1_4_62 = 1462;
	
	/**
	 * gwt compability version
	 * 1.4.62 -> 1462
	 * 1.5.0  -> 1500
	 * @var int
	 */
	private $gwtVersion = 1500;
	
	
	private function __construct() {
		
	}
	
	/**
	 * 
	 *
	 * @return GWTPHPContext
	 */
	public static function getInstance() {
		return (null === self::$instance) ? self::$instance = new GWTPHPContext() : self::$instance;
	}
	
	
	/**
	 * @var ClassLoader
	 */
	private $classLoader;
	
	/**
	 * 
	 * @var ClassMapLoader
	 */
	private $classMapLoader;
	
	/**
	 * @var string
	 */
	private $servicesRootDir;
	
	/**
	 * @var string
	 */
	private $dtoRootDir;
	
	/**
	 * 
	 * @var string
	 */
	private $gwtphpRootDir;
	
	
	/**
	 * 
	 * @var MappedClassLoader
	 */
	private $mappedClassLoader;
	
	
	/**	 
	 *
	 * @param MappedClassLoader $mappedClassLoader
	 * @return void
	 */
	public function setMappedClassLoader(MappedClassLoader $mappedClassLoader) {
		$this->mappedClassLoader = $mappedClassLoader;
	}
	
	/** 
	 *
	 * @return MappedClassLoader
	 */
	public function getMappedClassLoader() {		
		return  ($this->mappedClassLoader==null)
		? $this->mappedClassLoader = new FolderMappedClassLoader()
		: $this->mappedClassLoader;
	}
		
	
	/**	 
	 *
	 * @param string $gwtphpRootDir
	 * @return void
	 */
	public function setGWTPHPRootDir($gwtphpRootDir) {
		$this->gwtphpRootDir = $gwtphpRootDir;
	}
	
	/** 
	 *
	 * @return string
	 */
	public function getGWTPHPRootDir() {
		return $this->gwtphpRootDir;
	}
		
	
	/**	 
	 * @param string $dtoRootDir
	 * @return void
	 */
	public function setDTORootDir( $dtoRootDir) {
		$this->dtoRootDir = $dtoRootDir;
	}
	
	/** 
	 * @return string
	 */
	public function getDTORootDir() {
		return $this->dtoRootDir;
	}
		
	/**	 
	 * @param string $servicesRootDir
	 * @return void
	 */
	public function setServicesRootDir($servicesRootDir) {
		$this->servicesRootDir = $servicesRootDir;
	}
	
	/**
	 * @return string
	 */
	public function getServicesRootDir() {
		return $this->servicesRootDir;
	}
		
	/**
	 * 
	 *
	 * @return SimpleClassLoader
	 */
	private function initClassLoader(AbstractClassLoader $classLoader = null) {
		$classLoader = (null === $classLoader) ? new SimpleClassLoader() : $classLoader ;
				
		if ($this->getGWTPHPRootDir() != null) { 
			//TODO: change this: /..
			$classLoader->addClassPath($this->getGWTPHPRootDir());
			$classLoader->addClassPath($this->getGWTPHPRootDir().self::$GWTPHP_MAPS_FOLDER);
		}
		if ($this->getDTORootDir() != null) { 
			$classLoader->addClassPath($this->getDTORootDir());
		}
		if ($this->getServicesRootDir() != null) { 
			
			$classLoader->addClassPath($this->getServicesRootDir());
		}
		return $classLoader;
	}
	
	/**
	 * @return ClassLoader
	 */
	public function getClassLoader() {
		return  ($this->classLoader == null)
		? $this->classLoader = $this->initClassLoader()
		: $this->classLoader;
	}
	
	
	
	/**
	 * Sets GWTPHP Context ClassLoader.
	 * This ClassLoader will be used by all GWTPHP components
	 *
	 * @param ClassLoader $classLoader
	 */
	public function setClassLoader(ClassLoader $classLoader) {
		$this->classLoader = $classLoader;
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
	 * 
	 *
	 * @return SimpleClassMapLoader
	 */
	private function initClassMapLoader(AbstractClassMapLoader $classMapLoader = null) {
		$classMapLoader = (null === $classMapLoader) ? new SimpleClassMapLoader() : $classMapLoader ;
		
		if ($this->getServicesRootDir() != null) {
			$classMapLoader->addClassMapPath($this->getServicesRootDir());
		}
		if ($this->getDTORootDir() != null) {
			$classMapLoader->addClassMapPath($this->getDTORootDir());
		}
		if ($this->getGWTPHPRootDir() != null) { 
			$classMapLoader->addClassMapPath($this->getGWTPHPRootDir().self::$GWTPHP_MAPS_FOLDER);
		}
		return $classMapLoader;
	}
	
	/** 
	 *
	 * @return ClassMapLoader
	 */
	public function getClassMapLoader() {
		return  ($this->classMapLoader == null)
		? $this->classMapLoader = $this->initClassMapLoader()
		: $this->classMapLoader;
	}
		
	public function setGWTCompatibilityWithVersion_1_5_0() {
		$this->setGwtCompatibilityVersion(1500);
	}
	public function setGWTCompatibilityWithVersion_1_4_62() {
		$this->setGwtCompatibilityVersion(1462);
	}
	
	/**
	 * @return int
	 */
	public function getGwtCompatibilityVersion() {
		return $this->gwtVersion;
	}
	
	/** 
	 * @param int $gwtVersion
	 */
	public function setGwtCompatibilityVersion($gwtVersion) {
		$this->gwtVersion = $gwtVersion;
	}
}

?>