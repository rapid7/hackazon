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

require_once(GWTPHP_DIR.'/lang/MappedField.class.php');

class SimpleMappedField implements MappedField {
	
	
	/**
	 * 
	 * @var MappedClass
	 */
	private $type;
	
	
	/**
	 * 
	 * @var String
	 */
	private $name;
	
	/**
	 * 
	 * @var MappedClass
	 */
	private $declaringMappedClass;
	
		
	
	/**	 
	 *
	 * @param MappedClass $type
	 * @return void
	 */
	public function setType(MappedClass $type) {
		$this->type = $type;
	}
	
	/**
	 * Returns a MappedClass object that identifies the declared type for the field represented by this MappedField object.
	 * @return MappedClass
	 */
	public function getType() {
		return $this->type;
	}
		
		
	/**	 
	 *
	 * @param String $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}
	

	/**
	 * Returns the name of the field represented by this Field object.
	 * @return String
	 */
	public function getName() {
		return $this->name;
	}
	
	/**	 
	 *
	 * @param MappedClass $declaringMappedClass
	 * @return void
	 */
	public function setDeclaringMappedClass(MappedClass $declaringMappedClass) {
		$this->declaringMappedClass = $declaringMappedClass;
	}
				
	
	/**
	 * Returns the MappedClass object representing the class or interface 
	 * that declares the mapped method represented by this MappedMethod object.
	 * 
	 * @return MappedClass
	 */
	public function getDeclaringMappedClass() {
		return $this->declaringMappedClass;
	}
	
}

?>