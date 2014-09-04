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
class LoggerAppenderEchoTest extends PHPUnit_Framework_TestCase {

	private $config1 = array(
		'rootLogger' => array(
			'appenders' => array('default'),
		),
		'appenders' => array(
			'default' => array(
				'class' => 'LoggerAppenderEcho',
				'layout' => array(
					'class' => 'LoggerLayoutSimple'
				),
			)
		)
	);
	
	private $config2 = array(
		'rootLogger' => array(
			'appenders' => array('default'),
		),
		'appenders' => array(
			'default' => array(
				'class' => 'LoggerAppenderEcho',
				'layout' => array(
					'class' => 'LoggerLayoutSimple'
				),
				'params' => array(
					'htmlLineBreaks' => true
				)
			)
		)
	);
	
	private $config3 = array(
		'rootLogger' => array(
			'appenders' => array('default'),
		),
		'appenders' => array(
			'default' => array(
				'class' => 'LoggerAppenderEcho',
				'layout' => array(
					'class' => 'LoggerLayoutSimple'
				),
				'params' => array(
					'htmlLineBreaks' => 'foo'
				)
			)
		)
	);
	
	public function testAppend() {
		Logger::configure($this->config1);
		$log = Logger::getRootLogger();

		$hlb = $log->getAppender('default')->getHtmlLineBreaks();
		$this->assertSame(false, $hlb);
		
		ob_start();
		$log->info("This is a test");
		$log->debug("And this too");
		$actual = ob_get_clean();
		$expected = "INFO - This is a test" . PHP_EOL . "DEBUG - And this too". PHP_EOL;
		
		$this->assertSame($expected, $actual);
	}
	
	public function testHtmlLineBreaks() {
		Logger::configure($this->config2);
		$log = Logger::getRootLogger();
		
		$hlb = $log->getAppender('default')->getHtmlLineBreaks();
		$this->assertSame(true, $hlb);
		
		ob_start();
		$log->info("This is a test" . PHP_EOL . "With more than one line");
		$log->debug("And this too");
		$actual = ob_get_clean();
		$expected = "INFO - This is a test<br />" . PHP_EOL . "With more than one line<br />" . PHP_EOL . "DEBUG - And this too<br />" . PHP_EOL;
		
		$this->assertSame($expected, $actual);
	}
	
// 	public function testHtmlLineBreaksInvalidOption() {
// 		Logger::configure($this->config3);
// 	}
	
	
	public function testEcho() {
		$appender = new LoggerAppenderEcho("myname ");
		
		$layout = new LoggerLayoutSimple();
		$appender->setLayout($layout);
		$appender->activateOptions();
		$event = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		
		$expected = "ERROR - testmessage" . PHP_EOL;
		ob_start();
		$appender->append($event);
		$actual = ob_get_clean();
		
		self::assertEquals($expected, $actual);
	}
	
	public function testRequiresLayout() {
		$appender = new LoggerAppenderEcho(); 
		self::assertTrue($appender->requiresLayout());
	}
	
	public function testEchoHtml() {
		$appender = new LoggerAppenderEcho("myname ");
		$appender->setHtmlLineBreaks(true);
		
		$layout = new LoggerLayoutSimple();
		$appender->setLayout($layout);
		$appender->activateOptions();
		
		// Single line message
		$event = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		
		$expected = "ERROR - testmessage<br />" . PHP_EOL;
		ob_start();
		$appender->append($event);
		$actual = ob_get_clean();
		self::assertEquals($expected, $actual);
		
		// Multi-line message
		$msg = "This message\nis in several lines\r\nto test various line breaks.";
		$expected = "ERROR - This message<br />\nis in several lines<br />\r\nto test various line breaks.<br />" . PHP_EOL;
		
		$event = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelError(), $msg);
		ob_start();
		$appender->append($event);
		$actual = ob_get_clean();
		self::assertEquals($expected, $actual);
	}

}
