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
 * LoggerAppenderSocket appends to a network socket.
 *
 * ## Configurable parameters: ##
 * 
 * - **remoteHost** - Target remote host.
 * - **port** - Target port (optional, defaults to 4446).
 * - **timeout** - Connection timeout in seconds (optional, defaults to 
 *     'default_socket_timeout' from php.ini)
 * 
 * The socket will by default be opened in blocking mode.
 * 
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/socket.html Appender documentation
 */ 
class LoggerAppenderSocket extends LoggerAppender {
	
	/** 
	 * Target host.
	 * @see http://php.net/manual/en/function.fsockopen.php 
	 */
	protected $remoteHost;
	
	/** Target port */
	protected $port = 4446;
	
	/** Connection timeout in ms. */
	protected $timeout;
	
	// ******************************************
	// *** Appender methods                   ***
	// ******************************************
	
	/** Override the default layout to use serialized. */
	public function getDefaultLayout() {
		return new LoggerLayoutSerialized();
	}
	
	public function activateOptions() {
		if (empty($this->remoteHost)) {
			$this->warn("Required parameter [remoteHost] not set. Closing appender.");
			$this->closed = true;
			return;
		}
	
		if (empty($this->timeout)) {
			$this->timeout = ini_get("default_socket_timeout");
		}
	
		$this->closed = false;
	}
	
	public function append(LoggerLoggingEvent $event) {
		$socket = fsockopen($this->remoteHost, $this->port, $errno, $errstr, $this->timeout);
		if ($socket === false) {
			$this->warn("Could not open socket to {$this->remoteHost}:{$this->port}. Closing appender.");
			$this->closed = true;
			return;
		}
	
		if (false === fwrite($socket, $this->layout->format($event))) {
			$this->warn("Error writing to socket. Closing appender.");
			$this->closed = true;
		}
		fclose($socket);
	}
	
	// ******************************************
	// *** Accessor methods                   ***
	// ******************************************
	
	/** Sets the target host. */
	public function setRemoteHost($hostname) {
		$this->setString('remoteHost', $hostname);
	}
	
	/** Sets the target port */
	public function setPort($port) {
		$this->setPositiveInteger('port', $port);
	}
	 
	/** Sets the timeout. */
	public function setTimeout($timeout) {
		$this->setPositiveInteger('timeout', $timeout);
	}
	
	/** Returns the target host. */
	public function getRemoteHost() {
		return $this->getRemoteHost();
	}
	
	/** Returns the target port. */
	public function getPort() {
		return $this->port;
	}
	
	/** Returns the timeout */
	public function getTimeout() {
		return $this->timeout;
	}
}
