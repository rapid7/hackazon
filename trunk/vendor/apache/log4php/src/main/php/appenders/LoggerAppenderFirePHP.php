<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Logs messages as HTTP headers using the FirePHP Insight API.
 * 
 * This appender requires the FirePHP server library version 1.0 or later.
 * 
 * ## Configurable parameters: ##
 * 
 * - **target** - (string) The target to which messages will be sent. Possible options are 
 *            'page' (default), 'request', 'package' and 'controller'. For more details,
 *            see FirePHP documentation.
 * 
 * This class was originally contributed by Bruce Ingalls (Bruce.Ingalls-at-gmail-dot-com).
 * 
 * @link https://github.com/firephp/firephp FirePHP homepage.
 * @link http://sourcemint.com/github.com/firephp/firephp/1:1.0.0b1rc6/-docs/Welcome FirePHP documentation.
 * @link http://sourcemint.com/github.com/firephp/firephp/1:1.0.0b1rc6/-docs/Configuration/Constants FirePHP constants documentation.
 * @link http://logging.apache.org/log4php/docs/appenders/firephp.html Appender documentation
 * 
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @since 2.3
 */
class LoggerAppenderFirePHP extends LoggerAppender {
	
	/**
	 * Instance of the Insight console class.
	 * @var Insight_Plugin_Console
	 */
	protected $console;
	
	/**
	 * The target for log messages. Possible values are: 'page' (default), 
	 * 'request', 'package' and 'contoller'.
	 */
	protected $target = 'page';

	public function activateOptions() {
		if (method_exists('FirePHP', 'to')) {
			$this->console = FirePHP::to($this->target)->console();
			$this->closed = false;
		} else {
			$this->warn('FirePHP is not installed correctly. Closing appender.');
		}
	}
	
	public function append(LoggerLoggingEvent $event) {
		$msg = $event->getMessage();
		
		// Skip formatting for objects and arrays which are handled by FirePHP.
		if (!is_array($msg) && !is_object($msg)) {
			$msg = $this->getLayout()->format($event);
		}
		
		switch ($event->getLevel()->toInt()) {
			case LoggerLevel::TRACE:
			case LoggerLevel::DEBUG:
				$this->console->log($msg);
				break;
			case LoggerLevel::INFO:
				$this->console->info($msg);
				break;
			case LoggerLevel::WARN:
				$this->console->warn($msg);
				break;
			case LoggerLevel::ERROR:
			case LoggerLevel::FATAL:
				$this->console->error($msg);
				break;
		}
	}
	
	/** Returns the target. */
	public function getTarget() {
		return $this->target;
	}

	/** Sets the target. */
	public function setTarget($target) {
		$this->setString('target', $target);
	}
}