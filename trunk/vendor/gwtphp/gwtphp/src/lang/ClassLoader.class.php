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

/**
 * ClassLoader - responsible for load files and inits ReflectionClass
 * 
 * 
 *
 */
interface  ClassLoader {
	/**
	 * take as parameter full class name ie: org.me.MyClass
	 * @param string $className
	 * @param string $startLookingFrom class path, if many class path start looking from here
	 * @return ReflectionClass
	 * @throws ClassNotFoundException
	 */
	function loadClass($className, $startLookingFrom = null);
	
	/** 
	 *
	 * @return string
	 */
	public function getClassPaths();
	
	/** 
	 *
	 * @return string
	 */
	public function getFilePostfix() ;
	
	public function getClassSimpleName($className); 
}

?>