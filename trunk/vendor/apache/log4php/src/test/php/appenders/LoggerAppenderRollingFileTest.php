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
class LoggerAppenderRollingFileTest extends PHPUnit_Framework_TestCase {

	const WARNING_MASSAGE = 'WARN - my messageXYZ';
	
	protected function setUp() {
		@unlink(PHPUNIT_TEMP_DIR . '/TEST-rolling.txt');
		@unlink(PHPUNIT_TEMP_DIR . '/TEST-rolling.txt.1');
		@unlink(PHPUNIT_TEMP_DIR . '/TEST-rolling.txt.2');
	}
	
	public function testRequiresLayout() {
		$appender = new LoggerAppenderRollingFile();
		self::assertTrue($appender->requiresLayout());
	}

	public function testMaxFileSize() {
		$appender = new LoggerAppenderRollingFile("mylogger");

		$appender->setMaxFileSize('1KB');
		self::assertEquals(1024, $appender->getMaxFileSize());

		$appender->setMaxFileSize('2KB');
		self::assertEquals(2048, $appender->getMaxFileSize());

		$appender->setMaxFileSize('1MB');
		self::assertEquals(1048576, $appender->getMaxFileSize());

		$appender->setMaxFileSize('3MB');
		self::assertEquals(3145728, $appender->getMaxFileSize());

		$appender->setMaxFileSize('1GB');
		self::assertEquals(1073741824, $appender->getMaxFileSize());

		$appender->setMaxFileSize('10000');
		self::assertEquals(10000, $appender->getMaxFileSize());

		$appender->setMaxFileSize('100.5');
		self::assertEquals(100, $appender->getMaxFileSize());

		$appender->setMaxFileSize('1000.6');
		self::assertEquals(1000, $appender->getMaxFileSize());

		$appender->setMaxFileSize('1.5MB');
		self::assertEquals(1572864, $appender->getMaxFileSize());
	}

	/**
	 * @return LoggerAppenderRollingFile
	 */
	private function createRolloverAppender() {
		$layout = new LoggerLayoutSimple();
		
		$appender = new LoggerAppenderRollingFile("mylogger");
		$appender->setFile(PHPUNIT_TEMP_DIR . '/TEST-rolling.txt');
		$appender->setLayout($layout);
		$appender->setMaxFileSize('1KB');
		$appender->setMaxBackupIndex(2);
		$appender->activateOptions();
		
		return $appender;
	}

	public function testSimpleLogging() {
		$appender = $this->createRolloverAppender();
		
		$event = LoggerTestHelper::getWarnEvent("my message123");
		
		for($i = 0; $i < 1000; $i++) {
			$appender->append($event);
		}

		$appender->append(LoggerTestHelper::getWarnEvent("my messageXYZ"));

		$appender->close();

		$file = PHPUNIT_TEMP_DIR . '/TEST-rolling.txt';
		$data = file($file);
		$line = $data[count($data)-1];
		$e = "WARN - my messageXYZ".PHP_EOL;
		self::assertEquals($e, $line);

		$file = PHPUNIT_TEMP_DIR . '/TEST-rolling.txt.1';
		$this->checkFileContent($file);

		$file = PHPUNIT_TEMP_DIR . '/TEST-rolling.txt.2';
		$this->checkFileContent($file);

		// Should not roll over three times
		$this->assertFalse(file_exists(PHPUNIT_TEMP_DIR.'/TEST-rolling.txt.3'));
	}
	
	public function testLoggingViaLogger() {
		$logger = Logger::getLogger('mycat');
		$logger->setAdditivity(false);
		
		$appender = $this->createRolloverAppender();

		$logger->addAppender($appender);
		
		for($i = 0; $i < 1000; $i++) {
			$logger->warn("my message123");
		}
		
		$logger->warn("my messageXYZ");
		
		$file = PHPUNIT_TEMP_DIR.'/TEST-rolling.txt';
		$data = file($file);
		
		$line = $data[count($data)-1];
		$e = "WARN - my messageXYZ".PHP_EOL;
		self::assertEquals($e, $line);

		$file = PHPUNIT_TEMP_DIR.'/TEST-rolling.txt.1';
		$this->checkFileContent($file);

		$file = PHPUNIT_TEMP_DIR.'/TEST-rolling.txt.2';
		$this->checkFileContent($file);

		$this->assertFalse(file_exists(PHPUNIT_TEMP_DIR.'/TEST-rolling.txt.3'), 'should not roll over three times');
	}
	
	public function testRolloverWithCompression() {
		$logger = Logger::getLogger('mycat');
		$logger->setAdditivity(false);

		$appender = $this->createRolloverAppender();
		$appender->setCompress(true);
		
		$logger->addAppender($appender);
		
		for($i = 0; $i < 1000; $i++) {
			$logger->warn(self::WARNING_MASSAGE. $i);
		}
		
		$logger->warn("my messageXYZ");

		$file = PHPUNIT_TEMP_DIR . '/TEST-rolling.txt';
		$data = file($file);
		
		$line = $data[count($data)-1];
		$e = self::WARNING_MASSAGE.PHP_EOL;
		self::assertEquals($e, $line);

		$firstCompressedRollingFile = PHPUNIT_TEMP_DIR . '/TEST-rolling.txt.1.gz';
		$this->assertTrue(file_exists($firstCompressedRollingFile),'TEST-rolling.txt.1.gz not found');

		$firstUncompressedRollingField = PHPUNIT_TEMP_DIR . '/TEST-rolling.txt.1';
		$this->assertFalse(file_exists($firstUncompressedRollingField),'TEST-rolling.txt.1 should be replaced by compressed');
		
		$secondCompressedRollingFile = PHPUNIT_TEMP_DIR . '/TEST-rolling.txt.2.gz';
		$this->assertTrue(file_exists($secondCompressedRollingFile), 'TEST-rolling.txt.2.gz not found');
		
		$secondUncompressedRollingField = PHPUNIT_TEMP_DIR . '/TEST-rolling.txt.2';
		$this->assertFalse(file_exists($secondUncompressedRollingField),'TEST-rolling.txt.2 should be replaced by compressed');
		
	}	

	private function checkFileContent($file) {
		$data = file($file);
		$this->checkText($data);		
	}

	private function checkText($text) {
		$line = $text[count($text)-1];
		$e = "WARN - my message123".PHP_EOL;
		foreach($text as $r) {
			self::assertEquals($e, $r);
		}
	}
	
	protected function tearDown() {
		@unlink(PHPUNIT_TEMP_DIR.'/TEST-rolling.txt');
		@unlink(PHPUNIT_TEMP_DIR.'/TEST-rolling.txt.1');
		@unlink(PHPUNIT_TEMP_DIR.'/TEST-rolling.txt.2');
		@unlink(PHPUNIT_TEMP_DIR.'/TEST-rolling.txt.1.gz');
		@unlink(PHPUNIT_TEMP_DIR.'/TEST-rolling.txt.2.gz');
	}
}
