<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *	   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * Returns a value from a superglobal array corresponding to the 
 * given key.
 * 
 * Option: the key to look up within the superglobal array
 * 
 * Also, it is possible that a superglobal variable is not populated by PHP
 * because of the settings in the variables-order ini directive. In this case
 * the converter will return an empty value.
 * 
 * @see http://php.net/manual/en/language.variables.superglobals.php
 * @see http://www.php.net/manual/en/ini.core.php#ini.variables-order
 * 
 * @package log4php
 * @subpackage pattern
 * @version $Revision$
 * @since 2.3
 */
abstract class LoggerPatternConverterSuperglobal extends LoggerPatternConverter {

	/** 
	 * Name of the superglobal variable, to be defined by subclasses. 
	 * For example: "_SERVER" or "_ENV". 
	 */
	protected $name;
	
	protected $value = '';
	
	public function activateOptions() {
		// Read the key from options array
		if (isset($this->option) && $this->option !== '') {
			$key = $this->option;
		}
	
		/*
		 * There is a bug in PHP which doesn't allow superglobals to be 
		 * accessed when their name is stored in a variable, e.g.:
		 * 
		 * $name = '_SERVER';
		 * $array = $$name;
		 * 
		 * This code does not work when run from within a method (only when run
		 * in global scope). But the following code does work: 
		 * 
		 * $name = '_SERVER';
		 * global $$name;
		 * $array = $$name;
		 * 
		 * That's why global is used here.
		 */
		global ${$this->name};
			
		// Check the given superglobal exists. It is possible that it is not initialized.
		if (!isset(${$this->name})) {
			$class = get_class($this);
			trigger_error("log4php: $class: Cannot find superglobal variable \${$this->name}.", E_USER_WARNING);
			return;
		}
		
		$source = ${$this->name};
		
		// When the key is set, display the matching value
		if (isset($key)) {
			if (isset($source[$key])) {
				$this->value = $source[$key]; 
			}
		}
		
		// When the key is not set, display all values
		else {
			$values = array();
			foreach($source as $key => $value) {
				$values[] = "$key=$value";
			}
			$this->value = implode(', ', $values);			
		}
	}
	
	public function convert(LoggerLoggingEvent $event) {
		return $this->value;
	}
}
