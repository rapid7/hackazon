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
 * LoggerAppenderMailEvent appends individual log events via email.
 * 
 * This appender is similar to LoggerAppenderMail, except that it sends each 
 * each log event in an individual email message at the time when it occurs.
 * 
 * This appender uses a layout.
 * 
 * ## Configurable parameters: ##
 * 
 * - **to** - Email address(es) to which the log will be sent. Multiple email
 *     addresses may be specified by separating them with a comma.
 * - **from** - Email address which will be used in the From field.
 * - **subject** - Subject of the email message.
 * - **smtpHost** - Used to override the SMTP server. Only works on Windows.
 * - **port** - Used to override the default SMTP server port. Only works on 
 *     Windows.
 *
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/mail-event.html Appender documentation
 */
class LoggerAppenderMailEvent extends LoggerAppender {

	/** 
	 * Email address to put in From field of the email.
	 * @var string
	 */
	protected $from;

	/** 
	 * Mail server port (widnows only).
	 * @var integer 
	 */
	protected $port = 25;

	/** 
	 * Mail server hostname (windows only).
	 * @var string   
	 */
	protected $smtpHost;

	/** 
	 * The subject of the email.
	 * @var string
	 */
	protected $subject = 'Log4php Report';

	/**
	 * One or more comma separated email addresses to which to send the email. 
	 * @var string
	 */
	protected $to = null;
	
	/** 
	 * Indiciates whether this appender should run in dry mode.
	 * @deprecated
	 * @var boolean 
	 */
	protected $dry = false;
	
	public function activateOptions() {
		if (empty($this->to)) {
			$this->warn("Required parameter 'to' not set. Closing appender.");
			$this->close = true;
			return;
		}
		
		$sendmail_from = ini_get('sendmail_from');
		if (empty($this->from) and empty($sendmail_from)) {
			$this->warn("Required parameter 'from' not set. Closing appender.");
			$this->close = true;
			return;
		}
		
		$this->closed = false;
	}

	public function append(LoggerLoggingEvent $event) {
		$smtpHost = $this->smtpHost;
		$prevSmtpHost = ini_get('SMTP');
		if(!empty($smtpHost)) {
			ini_set('SMTP', $smtpHost);
		}
	
		$smtpPort = $this->port;
		$prevSmtpPort= ini_get('smtp_port');
		if($smtpPort > 0 and $smtpPort < 65535) {
			ini_set('smtp_port', $smtpPort);
		}
	
		// On unix only sendmail_path, which is PHP_INI_SYSTEM i.e. not changeable here, is used.
	
		$addHeader = empty($this->from) ? '' : "From: {$this->from}\r\n";
	
		if(!$this->dry) {
			$result = mail($this->to, $this->subject, $this->layout->getHeader() . $this->layout->format($event) . $this->layout->getFooter($event), $addHeader);
		} else {
			echo "DRY MODE OF MAIL APP.: Send mail to: ".$this->to." with additional headers '".trim($addHeader)."' and content: ".$this->layout->format($event);
		}
			
		ini_set('SMTP', $prevSmtpHost);
		ini_set('smtp_port', $prevSmtpPort);
	}
	
	/** Sets the 'from' parameter. */
	public function setFrom($from) {
		$this->setString('from', $from);
	}
	
	/** Returns the 'from' parameter. */
	public function getFrom() {
		return $this->from;
	}
	
	/** Sets the 'port' parameter. */
	public function setPort($port) {
		$this->setPositiveInteger('port', $port);
	}
	
	/** Returns the 'port' parameter. */
	public function getPort() {
		return $this->port;
	}
	
	/** Sets the 'smtpHost' parameter. */
	public function setSmtpHost($smtpHost) {
		$this->setString('smtpHost', $smtpHost);
	}
	
	/** Returns the 'smtpHost' parameter. */
	public function getSmtpHost() {
		return $this->smtpHost;
	}
	
	/** Sets the 'subject' parameter. */
	public function setSubject($subject) {
		$this->setString('subject',  $subject);
	}
	
	/** Returns the 'subject' parameter. */
	public function getSubject() {
		return $this->subject;
	}
	
	/** Sets the 'to' parameter. */
	public function setTo($to) {
		$this->setString('to',  $to);
	}
	
	/** Returns the 'to' parameter. */
	public function getTo() {
		return $this->to;
	}

	/** Enables or disables dry mode. */
	public function setDry($dry) {
		$this->setBoolean('dry', $dry);
	}
}
