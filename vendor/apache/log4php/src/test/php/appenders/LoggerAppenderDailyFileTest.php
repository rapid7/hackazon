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
class LoggerAppenderDailyFileTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		@unlink(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.' . date('Ymd'));
		@unlink(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.' . date('Y'));
	}
	
	public function testRequiresLayout() {
		$appender = new LoggerAppenderDailyFile(); 
		self::assertTrue($appender->requiresLayout());
	}
	
	public function testDefaultLayout() {
		$appender = new LoggerAppenderDailyFile();
		$actual = $appender->getLayout();
		self::assertInstanceOf('LoggerLayoutSimple', $actual);
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage Required parameter 'file' not set.
	 */
	public function testRequiredParamWarning1() {
		$appender = new LoggerAppenderDailyFile();
		$appender->activateOptions();
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage Required parameter 'datePattern' not set.
	 */
	public function testRequiredParamWarning2() {
		$appender = new LoggerAppenderDailyFile();
		$appender->setFile('file.log');
		$appender->setDatePattern('');
		$appender->activateOptions();
	}
	
	public function testGetDatePattern() {
		$appender = new LoggerAppenderDailyFile();

		// Default pattern
		$actual = $appender->getDatePattern();
		self::assertEquals('Ymd', $actual);
		
		// Custom pattern
		$appender->setDatePattern('xyz');
		$actual = $appender->getDatePattern();
		self::assertEquals('xyz', $actual);
	}
	
	/**
	 * For greater code coverage!
	 * Override the warning so remaining code is reached.
	 */
	public function testRequiredParamWarning3() {
		$appender = new LoggerAppenderDailyFile();
		$appender->setFile('file.log');
		$appender->setDatePattern('');
		@$appender->activateOptions();
	}
	
	public function testLazyFileOpen() {
		$event = LoggerTestHelper::getWarnEvent("my message");
		$file = PHPUNIT_TEMP_DIR . '/lazy-file.%s.log';
		$pattern = 'Y-m-d'; 
		
		$date = date($pattern, $event->getTimeStamp());
		$path =  PHPUNIT_TEMP_DIR . "/lazy-file.$date.log";
		
		if (file_exists($path)) {
			unlink($path);
		}
		
		$appender = new LoggerAppenderDailyFile();
		$appender->setFile($file);
		$appender->setDatePattern('Y-m-d');
		$appender->activateOptions();
		
		// File should not exist before first append
		self::assertFileNotExists($path);
		$appender->append($event);
		self::assertFileExists($path);
	}
	
	public function testRollover()
	{
		$message = uniqid();
		$level = LoggerLevel::getLevelDebug();
		
		$file = PHPUNIT_TEMP_DIR . '/lazy-file.%s.log';
		$pattern = 'Y-m-d';
		
		// Get some timestamps for events - different date for each
		$ts1 = mktime(10, 0, 0, 7, 3, 1980);
		$ts2 = mktime(10, 0, 0, 7, 4, 1980);
		$ts3 = mktime(10, 0, 0, 7, 5, 1980);
		
		$e1 = new LoggerLoggingEvent(__CLASS__, 'test', $level, $message, $ts1);
		$e2 = new LoggerLoggingEvent(__CLASS__, 'test', $level, $message, $ts2);
		$e3 = new LoggerLoggingEvent(__CLASS__, 'test', $level, $message, $ts3);
		
		// Expected paths
		$path1 = PHPUNIT_TEMP_DIR . '/lazy-file.1980-07-03.log';
		$path2 = PHPUNIT_TEMP_DIR . '/lazy-file.1980-07-04.log';
		$path3 = PHPUNIT_TEMP_DIR . '/lazy-file.1980-07-05.log';
		
		@unlink($path1);
		@unlink($path2);
		@unlink($path3);

		$appender = new LoggerAppenderDailyFile();
		$appender->setFile($file);
		$appender->setDatePattern('Y-m-d');
		$appender->activateOptions();
		
		$appender->append($e1);
		$appender->append($e2);
		$appender->append($e3);
		
		$actual1 = file_get_contents($path1);
		$actual2 = file_get_contents($path2);
		$actual3 = file_get_contents($path3);
		
		$expected1 = "DEBUG - $message" . PHP_EOL;
		$expected2 = "DEBUG - $message" . PHP_EOL;
		$expected3 = "DEBUG - $message" . PHP_EOL;

		self::assertSame($expected1, $actual1);
		self::assertSame($expected2, $actual2);
		self::assertSame($expected3, $actual3);
	}
	
	public function testSimpleLogging() {
		$event = LoggerTestHelper::getWarnEvent("my message");

		$appender = new LoggerAppenderDailyFile(); 
		$appender->setFile(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.%s');
		$appender->activateOptions();
		$appender->append($event);
		$appender->close();

		$actual = file_get_contents(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.' . date("Ymd"));		
		$expected = "WARN - my message".PHP_EOL;
		self::assertEquals($expected, $actual);
	}
	 
	public function testChangedDateFormat() {
		$event = LoggerTestHelper::getWarnEvent("my message");
		
		$appender = new LoggerAppenderDailyFile(); 
		$appender->setDatePattern('Y');
		$appender->setFile(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.%s');
		$appender->activateOptions();
		$appender->append($event);
		$appender->close();

		$actual = file_get_contents(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.' . date("Y"));		
		$expected = "WARN - my message".PHP_EOL;
		self::assertEquals($expected, $actual);
	} 
}
