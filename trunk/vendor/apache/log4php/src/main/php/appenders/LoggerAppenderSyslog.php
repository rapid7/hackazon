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
 * Log events to a system log using the PHP syslog() function.
 *
 * This appenders requires a layout.
 *
 * ## Configurable parameters: ##
 * 
 * - **ident** - The ident of the syslog message.
 * - **priority** - The priority for the syslog message (used when overriding 
 *     priority).
 * - **facility** - The facility for the syslog message
 * - **overridePriority** - If set to true, the message priority will always 
 *     use the value defined in {@link $priority}, otherwise the priority will
 *     be determined by the message's log level.  
 * - **option** - The option value for the syslog message. 
 *
 * Recognised syslog options are:
 * 
 * - CONS 	 - if there is an error while sending data to the system logger, write directly to the system console
 * - NDELAY - open the connection to the logger immediately
 * - ODELAY - delay opening the connection until the first message is logged (default)
 * - PERROR - print log message also to standard error
 * - PID    - include PID with each message
 * 
 * Multiple options can be set by delimiting them with a pipe character, 
 * e.g.: "CONS|PID|PERROR".
 * 
 * Recognised syslog priorities are:
 * 
 * - EMERG
 * - ALERT
 * - CRIT
 * - ERR
 * - WARNING
 * - NOTICE
 * - INFO
 * - DEBUG
 *
 * Levels are mapped as follows:
 * 
 * - <b>FATAL</b> to LOG_ALERT
 * - <b>ERROR</b> to LOG_ERR 
 * - <b>WARN</b> to LOG_WARNING
 * - <b>INFO</b> to LOG_INFO
 * - <b>DEBUG</b> to LOG_DEBUG
 * - <b>TRACE</b> to LOG_DEBUG
 *
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/syslog.html Appender documentation
 */ 
class LoggerAppenderSyslog extends LoggerAppender {
	
	/**
	 * The ident string is added to each message. Typically the name of your application.
	 * 
	 * @var string 
	 */
	protected $ident = "Apache log4php";

	/**
	 * The syslog priority to use when overriding priority. This setting is 
	 * required if {@link overridePriority} is set to true. 
	 * 
	 * @var string 
	 */
	protected $priority;
	
	/**
	 * The option used when opening the syslog connection.
	 * 
	 * @var string
	 */
	protected $option = 'PID|CONS';
	
	/**
	 * The facility value indicates the source of the message.
	 *
	 * @var string
	 */
	protected $facility = 'USER';
	
	/**
	 * If set to true, the message priority will always use the value defined 
	 * in {@link $priority}, otherwise the priority will be determined by the 
	 * message's log level.
	 *
	 * @var string
	 */
	protected $overridePriority = false;

	/**
	 * Holds the int value of the {@link $priority}.
	 * @var int
	 */
	private $intPriority;
	
	/**
	 * Holds the int value of the {@link $facility}.
	 * @var int
	 */
	private $intFacility;
	
	/**
	 * Holds the int value of the {@link $option}.
	 * @var int
	 */
	private $intOption;

	/**
	 * Sets the {@link $ident}.
	 *
	 * @param string $ident
	 */
	public function setIdent($ident) {
		$this->ident = $ident; 
	}
	
	/**
	 * Sets the {@link $priority}.
	 *
	 * @param string $priority
	 */
	public function setPriority($priority) {
		$this->priority = $priority;
	}
	
	/**
	 * Sets the {@link $facility}.
	 *
	 * @param string $facility
	 */
	public function setFacility($facility) {
		$this->facility = $facility;
	} 
	
	/**
	 * Sets the {@link $overridePriority}.
	 *
	 * @param string $overridePriority
	 */
	public function setOverridePriority($overridePriority) {
		$this->overridePriority = $overridePriority;
	} 
	
	/**
	* Sets the 'option' parameter.
	*
	* @param string $option
	*/
	public function setOption($option) {
		$this->option = $option;
	}
	
	/**
	* Returns the 'ident' parameter.
	*
	* @return string $ident
	*/
	public function getIdent() {
		return $this->ident;
	}
	
	/**
	 * Returns the 'priority' parameter.
	 *
	 * @return string
	 */
	public function getPriority() {
		return $this->priority;
	}
	
	/**
	 * Returns the 'facility' parameter.
	 *
	 * @return string
	 */
	public function getFacility() {
		return $this->facility;
	}
	
	/**
	 * Returns the 'overridePriority' parameter.
	 *
	 * @return string
	 */
	public function getOverridePriority() {
		return $this->overridePriority;
	}
	
	/**
	 * Returns the 'option' parameter.
	 *
	 * @return string
	 */
	public function getOption() {
		return $this->option;
	}
	
	
	public function activateOptions() {
		$this->intPriority = $this->parsePriority();
		$this->intOption   = $this->parseOption();
		$this->intFacility = $this->parseFacility();
		
		$this->closed = false;
	}
	
	public function close() {
		if($this->closed != true) {
			closelog();
			$this->closed = true;
		}
	}

	/** 
	 * Appends the event to syslog.
	 * 
	 * Log is opened and closed each time because if it is not closed, it
	 * can cause the Apache httpd server to log to whatever ident/facility 
	 * was used in openlog().
	 *
	 * @see http://www.php.net/manual/en/function.syslog.php#97843
	 */
	public function append(LoggerLoggingEvent $event) {
		$priority = $this->getSyslogPriority($event->getLevel());
		$message = $this->layout->format($event);
	
		openlog($this->ident, $this->intOption, $this->intFacility);
		syslog($priority, $message);
		closelog();
	}
	
	/** Determines which syslog priority to use based on the given level. */
	private function getSyslogPriority(LoggerLevel $level) {
		if($this->overridePriority) {
			return $this->intPriority;
		}
		return $level->getSyslogEquivalent();
	}
	
	/** Parses a syslog option string and returns the correspodning int value. */
	private function parseOption() {
		$value = 0;
		$options = explode('|', $this->option);
	
		foreach($options as $option) {
			if (!empty($option)) {
				$constant = "LOG_" . trim($option);
				if (defined($constant)) {
					$value |= constant($constant);
				} else {
					trigger_error("log4php: Invalid syslog option provided: $option. Whole option string: {$this->option}.", E_USER_WARNING);
				}
			}
		}
		return $value;
	}
	
	/** Parses the facility string and returns the corresponding int value. */
	private function parseFacility() {
		if (!empty($this->facility)) {   
			$constant = "LOG_" . trim($this->facility);
			if (defined($constant)) {
				return constant($constant);
			} else {
				trigger_error("log4php: Invalid syslog facility provided: {$this->facility}.", E_USER_WARNING);
			}
		}
	}

	/** Parses the priority string and returns the corresponding int value. */
	private function parsePriority() {
		if (!empty($this->priority)) {
			$constant = "LOG_" . trim($this->priority);
			if (defined($constant)) {
				return constant($constant);
			} else {
				trigger_error("log4php: Invalid syslog priority provided: {$this->priority}.", E_USER_WARNING);
			}
		}	
	}
}
