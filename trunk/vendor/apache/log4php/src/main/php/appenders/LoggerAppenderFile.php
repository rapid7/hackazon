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
 * LoggerAppenderFile appends log events to a file.
 *
 * This appender uses a layout.
 * 
 * ## Configurable parameters: ##
 * 
 * - **file** - Path to the target file. Relative paths are resolved based on 
 *     the working directory.
 * - **append** - If set to true, the appender will append to the file, 
 *     otherwise the file contents will be overwritten.
 *
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/file.html Appender documentation
 */
class LoggerAppenderFile extends LoggerAppender {

	/**
	 * If set to true, the file is locked before appending. This allows 
	 * concurrent access. However, appending without locking is faster so
	 * it should be used where appropriate.
	 * 
	 * TODO: make this a configurable parameter
	 * 
	 * @var boolean
	 */
	protected $locking = true;
	
	/**
	 * If set to true, appends to file. Otherwise overwrites it.
	 * @var boolean
	 */
	protected $append = true;
	
	/**
	 * Path to the target file.
	 * @var string 
	 */
	protected $file;

	/**
	 * The file resource.
	 * @var resource
	 */
	protected $fp;
	
	/** 
	 * Helper function which can be easily overriden by daily file appender. 
	 */
	protected function getTargetFile() {
		return $this->file;
	}
	
	/**
	 * Acquires the target file resource, creates the destination folder if 
	 * necessary. Writes layout header to file.
	 * 
	 * @return boolean FALSE if opening failed
	 */
	protected function openFile() {
		$file = $this->getTargetFile();

		// Create the target folder if needed
		if(!is_file($file)) {
			$dir = dirname($file);

			if(!is_dir($dir)) {
				$success = mkdir($dir, 0777, true);
				if ($success === false) {
					$this->warn("Failed creating target directory [$dir]. Closing appender.");
					$this->closed = true;
					return false;
				}
			}
		}
		
		$mode = $this->append ? 'a' : 'w';
		$this->fp = fopen($file, $mode);
		if ($this->fp === false) {
			$this->warn("Failed opening target file. Closing appender.");
			$this->fp = null;
			$this->closed = true;
			return false;
		}
		
		// Required when appending with concurrent access
		if($this->append) {
			fseek($this->fp, 0, SEEK_END);
		}
		
		// Write the header
		$this->write($this->layout->getHeader());
	}
	
	/**
	 * Writes a string to the target file. Opens file if not already open.
	 * @param string $string Data to write.
	 */
	protected function write($string) {
		// Lazy file open
		if(!isset($this->fp)) {
			if ($this->openFile() === false) {
				return; // Do not write if file open failed.
			}
		}
		
		if ($this->locking) {
			$this->writeWithLocking($string);
		} else {
			$this->writeWithoutLocking($string);
		}
	}
	
	protected function writeWithLocking($string) {
		if(flock($this->fp, LOCK_EX)) {
			if(fwrite($this->fp, $string) === false) {
				$this->warn("Failed writing to file. Closing appender.");
				$this->closed = true;				
			}
			flock($this->fp, LOCK_UN);
		} else {
			$this->warn("Failed locking file for writing. Closing appender.");
			$this->closed = true;
		}
	}
	
	protected function writeWithoutLocking($string) {
		if(fwrite($this->fp, $string) === false) {
			$this->warn("Failed writing to file. Closing appender.");
			$this->closed = true;				
		}
	}
	
	public function activateOptions() {
		if (empty($this->file)) {
			$this->warn("Required parameter 'file' not set. Closing appender.");
			$this->closed = true;
			return;
		}
	}
	
	public function close() {
		if (is_resource($this->fp)) {
			$this->write($this->layout->getFooter());
			fclose($this->fp);
		}
		$this->fp = null;
		$this->closed = true;
	}

	public function append(LoggerLoggingEvent $event) {
		$this->write($this->layout->format($event));
	}
	
	/**
	 * Sets the 'file' parameter.
	 * @param string $file
	 */
	public function setFile($file) {
		$this->setString('file', $file);
	}
	
	/**
	 * Returns the 'file' parameter.
	 * @return string
	 */
	public function getFile() {
		return $this->file;
	}
	
	/**
	 * Returns the 'append' parameter.
	 * @return boolean
	 */
	public function getAppend() {
		return $this->append;
	}

	/**
	 * Sets the 'append' parameter.
	 * @param boolean $append
	 */
	public function setAppend($append) {
		$this->setBoolean('append', $append);
	}

	/**
	 * Sets the 'file' parmeter. Left for legacy reasons.
	 * @param string $fileName
	 * @deprecated Use setFile() instead.
	 */
	public function setFileName($fileName) {
		$this->setFile($fileName);
	}
	
	/**
	 * Returns the 'file' parmeter. Left for legacy reasons.
	 * @return string
	 * @deprecated Use getFile() instead.
	 */
	public function getFileName() {
		return $this->getFile();
	}
}
