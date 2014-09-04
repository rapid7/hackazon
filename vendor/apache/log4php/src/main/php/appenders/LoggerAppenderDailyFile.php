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
 * An Appender that automatically creates a new logfile each day.
 *
 * The file is rolled over once a day. That means, for each day a new file 
 * is created. A formatted version of the date pattern is used as to create 
 * the file name using the {@link PHP_MANUAL#sprintf} function.
 *
 * This appender uses a layout.
 * 
 * ##Configurable parameters:##
 * 
 * - **datePattern** - Format for the date in the file path, follows formatting
 *     rules used by the PHP date() function. Default value: "Ymd".
 * - **file** - Path to the target file. Should contain a %s which gets 
 *     substituted by the date.
 * - **append** - If set to true, the appender will append to the file, 
 *     otherwise the file contents will be overwritten. Defaults to true.
 * 
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/daily-file.html Appender documentation
 */
class LoggerAppenderDailyFile extends LoggerAppenderFile {

	/**
	 * The 'datePattern' parameter.
	 * Determines how date will be formatted in file name.
	 * @var string
	 */
	protected $datePattern = "Ymd";
	
	/**
	 * Current date which was used when opening a file.
	 * Used to determine if a rollover is needed when the date changes.
	 * @var string
	 */
	protected $currentDate;

	/** Additional validation for the date pattern. */
	public function activateOptions() {
		parent::activateOptions();
	
		if (empty($this->datePattern)) {
			$this->warn("Required parameter 'datePattern' not set. Closing appender.");
			$this->closed = true;
			return;
		}
	}

	/**
	 * Appends a logging event.
	 * 
	 * If the target file changes because of passage of time (e.g. at midnight) 
	 * the current file is closed. A new file, with the new date, will be 
	 * opened by the write() method. 
	 */
	public function append(LoggerLoggingEvent $event) {
		$eventDate = $this->getDate($event->getTimestamp());
		
		// Initial setting of current date
		if (!isset($this->currentDate)) {
			$this->currentDate = $eventDate;
		} 
		
		// Check if rollover is needed
		else if ($this->currentDate !== $eventDate) {
			$this->currentDate = $eventDate;
			
			// Close the file if it's open.
			// Note: $this->close() is not called here because it would set
			//       $this->closed to true and the appender would not recieve
			//       any more logging requests
			if (is_resource($this->fp)) {
				$this->write($this->layout->getFooter());
				fclose($this->fp);
			}
			$this->fp = null;
		}
	
		parent::append($event);
	}
	
	/** Renders the date using the configured <var>datePattern<var>. */
	protected function getDate($timestamp = null) {
		return date($this->datePattern, $timestamp);
	}
	
	/**
	 * Determines target file. Replaces %s in file path with a date. 
	 */
	protected function getTargetFile() {
		return str_replace('%s', $this->currentDate, $this->file);
	}
	
	/**
	 * Sets the 'datePattern' parameter.
	 * @param string $datePattern
	 */
	public function setDatePattern($datePattern) {
		$this->setString('datePattern', $datePattern);
	}
	
	/**
	 * Returns the 'datePattern' parameter.
	 * @return string
	 */
	public function getDatePattern() {
		return $this->datePattern;
	}
}
