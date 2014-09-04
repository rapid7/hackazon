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
 * LoggerAppenderConsole appends log events either to the standard output 
 * stream (php://stdout) or the standard error stream (php://stderr).
 * 
 * **Note**: Use this Appender with command-line php scripts. On web scripts 
 * this appender has no effects.
 *
 * This appender uses a layout.
 *
 * ## Configurable parameters: ##
 * 
 * - **target** - the target stream: "stdout" or "stderr"
 * 
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/console.html Appender documentation
 */
 class LoggerAppenderConsole extends LoggerAppender {

	/** The standard otuput stream.  */
	const STDOUT = 'php://stdout';
	
	/** The standard error stream.*/
	const STDERR = 'php://stderr';

	/** The 'target' parameter. */
	protected $target = self::STDOUT;
	
	/**
	 * Stream resource for the target stream.
	 * @var resource
	 */
	protected $fp = null;

	public function activateOptions() {
		$this->fp = fopen($this->target, 'w');
		if(is_resource($this->fp) && $this->layout !== null) {
			fwrite($this->fp, $this->layout->getHeader());
		}
		$this->closed = (bool)is_resource($this->fp) === false;
	}
	
	
	public function close() {
		if($this->closed != true) {
			if (is_resource($this->fp) && $this->layout !== null) {
				fwrite($this->fp, $this->layout->getFooter());
				fclose($this->fp);
			}
			$this->closed = true;
		}
	}

	public function append(LoggerLoggingEvent $event) {
		if (is_resource($this->fp) && $this->layout !== null) {
			fwrite($this->fp, $this->layout->format($event));
		}
	}
	
	/**
	 * Sets the 'target' parameter.
	 * @param string $target
	 */
	public function setTarget($target) {
		$value = trim($target);
		if ($value == self::STDOUT || strtoupper($value) == 'STDOUT') {
			$this->target = self::STDOUT;
		} elseif ($value == self::STDERR || strtoupper($value) == 'STDERR') {
			$this->target = self::STDERR;
		} else {
			$target = var_export($target);
			$this->warn("Invalid value given for 'target' property: [$target]. Property not set.");
		}
	}
	
	/**
	 * Returns the value of the 'target' parameter.
	 * @return string
	 */
	public function getTarget() {
		return $this->target;
	}
}
