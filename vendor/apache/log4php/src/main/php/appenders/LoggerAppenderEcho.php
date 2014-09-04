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
 */

/**
 * LoggerAppenderEcho uses the PHP echo() function to output events. 
 * 
 * This appender uses a layout.
 * 
 * ## Configurable parameters: ##
 * 
 * - **htmlLineBreaks** - If set to true, a <br /> element will be inserted 
 *     before each line break in the logged message. Default is false.
 *
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/echo.html Appender documentation
 */
class LoggerAppenderEcho extends LoggerAppender {
	/** 
	 * Used to mark first append. Set to false after first append.
	 * @var boolean 
	 */
	protected $firstAppend = true;
	
	/** 
	 * If set to true, a <br /> element will be inserted before each line
	 * break in the logged message. Default value is false. @var boolean 
	 */
	protected $htmlLineBreaks = false;
	
	public function close() {
		if($this->closed != true) {
			if(!$this->firstAppend) {
				echo $this->layout->getFooter();
			}
		}
		$this->closed = true;
	}

	public function append(LoggerLoggingEvent $event) {
		if($this->layout !== null) {
			if($this->firstAppend) {
				echo $this->layout->getHeader();
				$this->firstAppend = false;
			}
			$text = $this->layout->format($event);
			
			if ($this->htmlLineBreaks) {
				$text = nl2br($text);
			}
			echo $text;
		} 
	}
	
	/**
	 * Sets the 'htmlLineBreaks' parameter.
	 * @param boolean $value
	 */
	public function setHtmlLineBreaks($value) {
		$this->setBoolean('htmlLineBreaks', $value);
	}
	
	/**
	 * Returns the 'htmlLineBreaks' parameter.
	 * @returns boolean
	 */
	public function getHtmlLineBreaks() {
		return $this->htmlLineBreaks;
	}
}

