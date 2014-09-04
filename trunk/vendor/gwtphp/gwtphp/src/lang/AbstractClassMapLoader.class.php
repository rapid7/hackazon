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
require_once(GWTPHP_DIR.'/lang/ClassMapLoader.class.php');

abstract class AbstractClassMapLoader implements ClassMapLoader {

    private static $DEFAULT_CLASS_MAP_FILE_POSTFIX = '.gwtphpmap.inc.php';


    /**
     *
     * @var string
     */
    private $filePostfix ;

    /**
     *
     * @var string
     */
    private $classMapPaths = array();


    public function __construct($filePostfix = null) {
        $this->filePostfix = ($filePostfix == null)
        ? AbstractClassMapLoader::$DEFAULT_CLASS_MAP_FILE_POSTFIX
        : $filePostfix;


    }


    /**
     * take as parameter full class name ie: org.me.MyClass
     * @param string $className
     * @param string $startLookingFrom class path, if many class map paths start looking from here
     * @return array
     * @throws ClassMapNotFoundException
     */
    function loadClassMap($className, $startLookingFrom = null) {
		$className = explode('/', $className, 2);
		$className = $className[0];
		
        $classMapPaths = $this->getFullClassMapPaths($className);
        if (!is_array($classMapPaths)) {
            $classMapPaths = array($classMapPaths);
        }
        if ($startLookingFrom != null) {
            array_unshift($classMapPaths,$startLookingFrom);
        }
        foreach ($classMapPaths as $classMapPath) {
            //Logger::getLogger('AbstractClassLoader')->info('Search for class: '.$classMapPath);
            if (file_exists($classMapPath)) {
                return $this->findGWTPHPMapInFile($classMapPath);
                //return $this->instatineClass($this->getClassSimpleName($className));
            }
        }
         
        require_once(GWTPHP_DIR.'/maps/java/lang/ClassMapNotFoundException.class.php');
        throw new ClassMapNotFoundException('Class map not found for class: '.$className);

    }
    
    public function findGWTPHPMapInFile($mapFilePath) {
    	require($mapFilePath);
        //TODO: check if exist and if is array
        if (isset($gwtphpmap) && is_array($gwtphpmap)) {
            return $gwtphpmap;
        } else {            
            require_once(GWTPHP_DIR.'/maps/java/lang/ClassMapNotFoundException.class.php');
            throw new ClassMapNotFoundException('Found map file without $gwtphpmap array in: '.$mapFilePath);
        }
    }

    public abstract function getFullClassMapPaths($className);


    /**
     * Path to classes of your application. This is where classLoader start to lookup for class files
     * @param string $classMapPath
     * @return void
     */
    public function setClassMapPath($classMapPath) {
        $this->classMapPaths[0] = $classMapPath;
    }
    /**
     *
     * @param String $classMapPath
     * @return boolean (false if rootpath exist in rootPaths array
     */
    public function addClassMapPath($classMapPath) {
        foreach ($this->classMapPaths as $path) {
            if ($path == $classMapPath) return false;
        }
        $this->classMapPaths[] = $classMapPath;
        return true;
    }

    /**
     *
     * @return string
     */
    public function getClassMapPath() {
        return $this->classMapPaths[0];
    }

    /**
     *
     * @return string
     */
    public function getClassMapPaths() {
        return $this->classMapPaths;
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