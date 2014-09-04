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
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    $Revision$
 * @link       http://logging.apache.org/log4php
 */

/**
 * @group main
 */
class LoggerTest extends PHPUnit_Framework_TestCase {
	
	private $testConfig1 = array (
		'rootLogger' =>	array (
			'level' => 'ERROR',
			'appenders' => array (
				'default',
			),
		),
		'appenders' => array (
			'default' => array (
				'class' => 'LoggerAppenderEcho',
			),
		),
		'loggers' => array (
			'mylogger' => array (
				'additivity' => 'false',
				'level' => 'DEBUG',
				'appenders' => array (
					'default',
				),
			),
		),
	);
	
	// For testing additivity
	private $testConfig2 = array (
		'appenders' => array (
			'default' => array (
				'class' => 'LoggerAppenderEcho',
			),
		),
		'rootLogger' => array(
			'appenders' => array('default'),
		),
		'loggers' => array (
			'foo' => array (
				'appenders' => array (
					'default',
				),
			),
			'foo.bar' => array (
				'appenders' => array (
					'default',
				),
			),
			'foo.bar.baz' => array (
				'appenders' => array (
					'default',
				),
			),
		),
	);
	
	// For testing additivity
	private $testConfig3 = array (
		'appenders' => array (
			'default' => array (
				'class' => 'LoggerAppenderEcho',
			),
		),
		'rootLogger' => array(
			'appenders' => array('default'),
		),
		'loggers' => array (
			'foo' => array (
				'appenders' => array (
					'default',
				),
			),
			'foo.bar' => array (
				'appenders' => array (
					'default',
				),
			),
			'foo.bar.baz' => array (
				'level' => 'ERROR',
				'appenders' => array (
					'default',
				),
			),
		),
	);
	
	protected function setUp() {
		Logger::clear();
		Logger::resetConfiguration();
	}
	
	protected function tearDown() {
		Logger::clear();
		Logger::resetConfiguration();
	}
	
	public function testLoggerExist() {
		$l = Logger::getLogger('test');
		self::assertEquals($l->getName(), 'test');
		self::assertTrue(Logger::exists('test'));
	}
	
	public function testCanGetRootLogger() {
		$l = Logger::getRootLogger();
		self::assertEquals($l->getName(), 'root');
	}
	
	public function testCanGetASpecificLogger() {
		$l = Logger::getLogger('test');
		self::assertEquals($l->getName(), 'test');
	}
	
	public function testCanLogToAllLevels() {
		Logger::configure($this->testConfig1);
		
		$logger = Logger::getLogger('mylogger');
		ob_start();
		$logger->info('this is an info');
		$logger->warn('this is a warning');
		$logger->error('this is an error');
		$logger->debug('this is a debug message');
		$logger->fatal('this is a fatal message');
		$v = ob_get_contents();
		ob_end_clean();
		
		$e = 'INFO - this is an info'.PHP_EOL;
		$e .= 'WARN - this is a warning'.PHP_EOL;
		$e .= 'ERROR - this is an error'.PHP_EOL;
		$e .= 'DEBUG - this is a debug message'.PHP_EOL;
		$e .= 'FATAL - this is a fatal message'.PHP_EOL;
		
		self::assertEquals($v, $e);
	}
	
	public function testIsEnabledFor() {
		Logger::configure($this->testConfig1);
		
		$logger = Logger::getLogger('mylogger');
		
		self::assertFalse($logger->isTraceEnabled());
		self::assertTrue($logger->isDebugEnabled());
		self::assertTrue($logger->isInfoEnabled());
		self::assertTrue($logger->isWarnEnabled());
		self::assertTrue($logger->isErrorEnabled());
		self::assertTrue($logger->isFatalEnabled());
		
		$logger = Logger::getRootLogger();
		
		self::assertFalse($logger->isTraceEnabled());
		self::assertFalse($logger->isDebugEnabled());
		self::assertFalse($logger->isInfoEnabled());
		self::assertFalse($logger->isWarnEnabled());
		self::assertTrue($logger->isErrorEnabled());
		self::assertTrue($logger->isFatalEnabled());
	}
	
	public function testGetCurrentLoggers() {
		Logger::clear();
		Logger::resetConfiguration();
		
		self::assertEquals(0, count(Logger::getCurrentLoggers()));
		
		Logger::configure($this->testConfig1);
		self::assertEquals(1, count(Logger::getCurrentLoggers()));
		$list = Logger::getCurrentLoggers();
		self::assertEquals('mylogger', $list[0]->getName());
	}
	
	public function testAdditivity() {
		Logger::configure($this->testConfig2);
	
		$logger = Logger::getLogger('foo.bar.baz');
		ob_start();
		$logger->info('test');
		$actual = ob_get_contents();
		ob_end_clean();
	
		// The message should get logged 4 times: once by every logger in the 
		//  hierarchy (including root)
		$expected = str_repeat('INFO - test' . PHP_EOL, 4);
		self::assertSame($expected, $actual);
	}
	
	public function testAdditivity2() {
		Logger::configure($this->testConfig3);
	
		$logger = Logger::getLogger('foo.bar.baz');
		ob_start();
		$logger->info('test');
		$actual = ob_get_contents();
		ob_end_clean();
	
		// The message should get logged 3 times: once by every logger in the
		//  hierarchy, except foo.bar.baz which is set to level ERROR
		$expected = str_repeat('INFO - test' . PHP_EOL, 3);
		self::assertSame($expected, $actual);
	}
}
