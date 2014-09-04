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
 * A NullAppender merely exists, it never outputs a message to any device.	
 *
 * This appender has no configurable parameters.
 * 
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/null.html Appender documentation
 */
class LoggerAppenderNull extends LoggerAppender {

	/** 
	 * This appender does not require a layout. 
	 */
	protected $requiresLayout = false;
	
	/**
	 * Do nothing. 
	 * 
	 * @param LoggerLoggingEvent $event
	 */
	public function append(LoggerLoggingEvent $event) {
	}
}
