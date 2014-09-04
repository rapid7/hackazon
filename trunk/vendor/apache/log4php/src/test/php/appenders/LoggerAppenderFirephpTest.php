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
 * @internal   Phpmd clean.
 */

@include_once('FirePHPCore/FirePHP.class.php');

/**
 * @group appenders
 */
class LoggerAppenderFirePHPTest extends PHPUnit_Framework_TestCase {

	private $config = array(
		'rootLogger' => array(
			'appenders' => array('default'),
		),
		'appenders' => array(
			'default' => array(
				'class' => 'LoggerAppenderFirePHP',
				'layout' => array(
					'class' => 'LoggerLayoutPattern',
				),
				'params' => array('target' => 'page')
			)
		)
	);

	public function setUp() {
		if(!method_exists('FirePHP', 'to')) {
			self::markTestSkipped("Please install 'FirePHP' in order to run this test");
		}
	}
	
	private function createEvent($message, $level) {
		$eventMock = new LoggerLoggingEvent("LoggerAppenderFirePHPTest", new Logger("TEST"), LoggerLevel::toLevel($level), $message);
	
		return $eventMock;
	}	
	
	public function testSetTarget() {
		$appender = new LoggerAppenderFirePHP();
		$appender->setTarget('page');
		self::assertSame('page', $appender->getTarget());
	}

	public function testAppend_HandleDebug() {
		$console = new FirePHPSpy();
		
		$appender = new TestableLoggerAppenderFirePhp();
		$appender->setConsole($console);
		
		$expectedMessage = 'trace message';
		$expectedLevel = 'debug';
		
		$appender->append($this->createEvent($expectedMessage, $expectedLevel));
		
		$this->assertLog($console, $expectedMessage, $expectedLevel, 'log');
	}
	
	public function testAppend_HandleWarn() {
		$console = new FirePHPSpy();
	
		$appender = new TestableLoggerAppenderFirePhp();
		$appender->setConsole($console);
	
		$expectedMessage = 'debug message';
		$expectedLevel = 'warn';
	
		$appender->append($this->createEvent($expectedMessage, $expectedLevel));
		
		$this->assertLog($console, $expectedMessage, $expectedLevel, 'warn');
	}
	
	public function testAppend_HandleError() {
		$console = new FirePHPSpy();
	
		$appender = new TestableLoggerAppenderFirePhp();
		$appender->setConsole($console);
	
		$expectedMessage = 'error message';
		$expectedLevel = 'error';
	
		$appender->append($this->createEvent($expectedMessage, $expectedLevel));
		
		$this->assertLog($console, $expectedMessage, $expectedLevel, 'error');
	}	
	
	public function testAppend_HandleFatal() {
		$console = new FirePHPSpy();
	
		$appender = new TestableLoggerAppenderFirePhp();
		$appender->setConsole($console);
	
		$expectedMessage = "fatal message";
		$expectedLevel = 'fatal';
	
		$appender->append($this->createEvent($expectedMessage, $expectedLevel));

		$this->assertLog($console, $expectedMessage, $expectedLevel, 'error');
	}
	
	public function testAppend_HandleDefault() {
		$console = new FirePHPSpy();
	
		$appender = new TestableLoggerAppenderFirePhp();
		$appender->setConsole($console);
		
		$expectedMessage = 'info message';
		$expectedLevel = 'info';
	
		$appender->append($this->createEvent($expectedMessage, $expectedLevel));
	
		$this->assertLog($console, $expectedMessage, $expectedLevel, 'info');
	}
	
	public function assertLog($console, $expectedMessage, $logLevel, $calledMethod) {
		$event = $this->createEvent($expectedMessage, $logLevel);
		
		$layout = new LoggerLayoutSimple();
		$message = $layout->format($event);
		
		$this->assertEquals($message, $console->getMessage(), 'log message is wrong');
		$this->assertEquals(1, $console->getCalls(), 'wasn\'t called once');
		$this->assertEquals($calledMethod, $console->getCalledMethod(), 'wrong log-method was called');
	}
}

class TestableLoggerAppenderFirePhp extends LoggerAppenderFirePHP {
	public function setConsole($console) {
		$this->console = $console;
	}
}

class FirePHPSpy {
	private $calls = 0;
	private $message = '';
	private $calledMethod = '';
	
	public function getCalls() {
		return $this->calls;
	}
	
	public function getMessage() {
		return $this->message;
	}
	
	public function log($message) {
		$this->calls++;
		$this->calledMethod = 'log';
		$this->message = $message;
	}
	
	public function debug($message) {
		$this->calls++;
		$this->calledMethod = 'debug';
		$this->message = $message;		
	}
	
	public function warn($message) {
		$this->calls++;
		$this->calledMethod = 'warn';
		$this->message = $message;		
	}
	
	public function error($message) {
		$this->calls++;
		$this->calledMethod = 'error';
		$this->message = $message;
	}
	
	public function info($message) {
		$this->calls++;
		$this->calledMethod = 'info';
		$this->message = $message;
	}
	
	public function getCalledMethod() {
		return $this->calledMethod;
	}
}
