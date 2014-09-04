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
require_once(GWTPHP_DIR.'/lang/AbstractClassMapLoader.class.php');
/**
* @author rmalinowski
*/
class SimpleClassMapLoader extends AbstractClassMapLoader  {

	/**
     * 
     *
     * @param string $className
     * @return array
     */
	public function getFullClassMapPaths($className) {
		$classMapPaths = array();

		if ( ($pos = strpos($className,INNER_JAVA_CLASS_SEPARATOR) ) !== false) {
			// since 1.4 - depracted (od tej chwili kazda inner classa to plik postaci ParentClass___InnerClass.class.php
			if (GWTPHPContext::getInstance()->getGwtCompatibilityVersion() < GWTPHPContext::GWT_VERSION_1_5_0) {
				$className = substr($className,0,$pos);
			} else {
				// since 1.5
				$className = str_replace(INNER_JAVA_CLASS_SEPARATOR,INNER_PHP_CLASS_SEPARATOR,$className);
			}
		}

		$classNameToPath = (str_replace('.', DIRECTORY_SEPARATOR ,$className)).(parent::getFilePostfix());
		//echo '<br><br>Looking for ' . $className;
		foreach (parent::getClassMapPaths() as $classMapPath) {

			$classMapPaths[] = $classMapPath.DIRECTORY_SEPARATOR.$classNameToPath;
		}
		return $classMapPaths;
	}

}
?>