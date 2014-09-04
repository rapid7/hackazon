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
 * Returns the name of the logger which created the logging request.
 * 
 * Takes one option, which is an integer. If the option is given, the logger 
 * name will be shortened to the given length, if possible.
 * 
 * @package log4php
 * @subpackage pattern
 * @version $Revision$
 * @since 2.3
 */
class LoggerPatternConverterLogger extends LoggerPatternConverter {

	/** Length to which to shorten the name. */
	private $length;
	
	/** Holds processed logger names. */
	private $cache = array();
	
	public function activateOptions() {
		// Parse the option (desired output length)
		if (isset($this->option) && is_numeric($this->option) && $this->option >= 0) {
			$this->length = (integer) $this->option;
		}
	}
	
	public function convert(LoggerLoggingEvent $event) {
		$name = $event->getLoggerName();
		
		if (!isset($this->cache[$name])) {

			// If length is set return shortened logger name 
			if (isset($this->length)) {
				$this->cache[$name] = LoggerUtils::shortenClassName($name, $this->length);
			} 
			
			// If no length is specified return full logger name
			else {
				$this->cache[$name] = $name;
			}
		} 
		
		return $this->cache[$name];
	}
}
