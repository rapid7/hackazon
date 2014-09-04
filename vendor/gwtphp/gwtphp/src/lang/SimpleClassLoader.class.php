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
require_once(GWTPHP_DIR.'/lang/AbstractClassLoader.class.php');

class SimpleClassLoader extends AbstractClassLoader implements ClassLoader {

	public function getFullClassPaths($className) {
		$classPaths = array();

		if ( ($pos = strpos($className,INNER_PHP_CLASS_SEPARATOR) ) !== false) {
			if (GWTPHPContext::getInstance()->getGwtCompatibilityVersion() < GWTPHPContext::GWT_VERSION_1_5_0) {
				$className = substr($className,0,$pos);
			} else {
				// since 1.5
				$className = JavaSignatureUtil::innecJavaClassNameToPHPClassName($className);
			}
		}

		$classNameToPath = (str_replace('.', DIRECTORY_SEPARATOR ,$className)).(parent::getFilePostfix());

		foreach (parent::getClassPaths() as $rootPath) {
			$classPaths[] = $rootPath.DIRECTORY_SEPARATOR.$classNameToPath;
		}
		//		$r = parent::getRootPath().'/';
		//		$d = str_replace('.', DIRECTORY_SEPARATOR ,$className);
		//		$p = parent::getFilePostfix();
		//		$cmd = $r.$d.$p;
		return $classPaths;
		//return $this->rootPath.str_replace('.', DIRECTORY_SEPARATOR ,$className).parent::getFilePostfix();
	}

	public function getClassSimpleName($className) {
		$classPath = str_replace('.',DIRECTORY_SEPARATOR,$className);
		$pos = strrpos($classPath, DIRECTORY_SEPARATOR);
		if ($pos === false) {
			return $classPath;
		} else {
			return substr($classPath,$pos-strlen($classPath)+1);
		}

	}

	public function instatineClass($classSimpleName) {
		return new ReflectionClass($classSimpleName);
	}

}

?>
