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
 *
 * @package log4php
 */

/**
 * Layout which formats the events using PHP's serialize() function.
 * 
 * Available options:
 * - locationInfo - If set to true, the event's location information will also
 *                  be serialized (slow, defaults to false).
 * 
 * @version $Revision$
 * @package log4php
 * @subpackage layouts
 * @since 2.2
 */  
class LoggerLayoutSerialized extends LoggerLayout {
	
	/** Whether to include the event's location information (slow). */
	protected $locationInfo = false;
	
	/** Sets the location information flag. */
	public function setLocationInfo($value) {
		$this->setBoolean('locationInfo', $value);
	}
	
	/** Returns the location information flag. */
	public function getLocationInfo() {
		return $this->locationInfo;
	}
	
	public function format(LoggerLoggingEvent $event) {
		// If required, initialize the location data
		if($this->locationInfo) {
			$event->getLocationInformation();
		}
		return serialize($event) . PHP_EOL;
	}
}
