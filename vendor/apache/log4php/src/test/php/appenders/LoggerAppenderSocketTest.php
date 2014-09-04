<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   tests
 * @package    log4php
 * @subpackage appenders
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    $Revision$
 * @link       http://logging.apache.org/log4php
 */

/**
 * @group appenders
 */
class LoggerAppenderSocketTest extends PHPUnit_Framework_TestCase {

	/** Port on which the socket server will run. */
	const SOCKET_PORT = 12345;

	/** The socket server process resource. */
	private $server;
	
	/** The pipes array for the server process. */
	private $pipes;
	
	public function setUp() {
		Logger::clear();
	}
	
	public function tearDown() {
		Logger::clear();
	}
	
	public function testRequiresLayout() {
		$appender = new LoggerAppenderSocket();
		self::assertTrue($appender->requiresLayout());
	}
	
	public function testLogging()
	{
		Logger::configure(array(
		    'appenders' => array(
		        'default' => array(
		            'class' => 'LoggerAppenderSocket',
		            'params' => array(
		                'remoteHost' => 'localhost',
		                'port' => self::SOCKET_PORT
		            ),
		            'layout' => array(
		            	'class' => 'LoggerLayoutSimple'
		            )
		        ),
		    ),
		    'rootLogger' => array(
		        'appenders' => array('default'),
		    ),
		));

		$this->startServer();
		
		$logger = Logger::getLogger("myLogger");
		$logger->trace("This message is a test");
		$logger->debug("This message is a test");
		$logger->info("This message is a test");
		$logger->warn("This message is a test");
		$logger->error("This message is a test");
		$logger->fatal("This message is a test");
		
		$actual = $this->getPlayback();
		$this->stopServer();
		
		$expected = "DEBUG - This message is a test" . 
		            "INFO - This message is a test" . 
		            "WARN - This message is a test" . 
		            "ERROR - This message is a test" . 
		            "FATAL - This message is a test";

		$this->assertEquals($expected, $actual);
	}
	
	/** Starts a socket server in a separate process. */
	private function startServer() {
		$serverLog = PHPUNIT_TEMP_DIR . '/socketServer.log';
		$descriptorspec = array(
			0 => array("pipe", "r"),  // stdin
			1 => array("file", $serverLog, "a"),// stdout
			2 => array("file", $serverLog, "a") // stderr
		);

		$cmd = "php " . dirname(__FILE__) . '/socketServer.php';
		$this->server = proc_open($cmd, $descriptorspec, $this->pipes);
		if ($this->server === false) {
			throw new Exception("Failed starting the socket server process.");
		}
		
		// Sleep a bit to allow server to start
		usleep(200000);
		
		// Verify the server is running
		$status = proc_get_status($this->server);
		if (!$status['running']) {
			throw new Exception("Socket server process failed to start. Check the log at [$serverLog].");
		}
	}
	
	/** Sends a message to the socket server and returns the reply. */
	private function socketSend($msg) {
		$sock = fsockopen('localhost', self::SOCKET_PORT, $errno, $errstr);
		if ($sock === false) {
			throw new Exception("Unable to open socket. Error: [$errno] $errstr");	
		}
		
		fputs($sock, "$msg\n");
		$reply = '';
		while(!feof($sock)) {
			$reply .= fgets($sock);
		}
		fclose($sock);
		return trim($reply);
	}
	
	/** Retrieves a playback of all sent messages from the socket server. */
	private function getPlayback() {
		return $this->socketSend('playback');
	}
	
	/** Stops the socket server and closes the process. */
	private function stopServer() {
		$this->socketSend('shutdown');
		foreach($this->pipes as $pipe) {
			fclose($pipe);
		}
		proc_close($this->server);
	}
}
