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
class LoggerAppenderPDOTest extends PHPUnit_Framework_TestCase {

	const FILENAME = 'pdotest.sqlite';
	private static $dsn;
	private static $file;
	
	public static function setUpBeforeClass() {

		self::$file = PHPUNIT_TEMP_DIR . '/' . self::FILENAME;
		self::$dsn = 'sqlite:' . self::$file;
		
		if(extension_loaded('pdo_sqlite')) {
			$drop = 'DROP TABLE IF EXISTS log4php_log;';
			$create = 'CREATE TABLE log4php_log (
				timestamp VARCHAR(256),
				logger VARCHAR(256),
				level VARCHAR(32),
				message VARCHAR(4000),
				thread INTEGER,
				file VARCHAR(255),
				line VARCHAR(10)
			);';
			
			$pdo = new PDO(self::$dsn);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->exec($drop);
			$pdo->exec($create);
		}
	}
	
	/** To start with an empty database for each single test. */
	public function setUp() {
		if(!extension_loaded('pdo_sqlite')) {
			self::markTestSkipped("Please install 'pdo_sqlite' in order to run this test");
		}
	}

	/** Clean up after the last test was run. */
	public static function tearDownAfterClass() {
		@unlink(self::$file);
	}
	
	public function testRequiresLayout() {
		$appender = new LoggerAppenderPDO();
		self::assertFalse($appender->requiresLayout());
	}

	/** Tests new-style logging using prepared statements and the default SQL definition. */
	public function testSimpleWithDefaults() {
		// Log event
		$event = new LoggerLoggingEvent("LoggerAppenderPDOTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		$appender = new LoggerAppenderPDO("myname");
		$appender->setDSN(self::$dsn);
		$appender->activateOptions();
		$appender->append($event);
		$appender->close();

		// Test the default pattern
		$db = new PDO(self::$dsn);
		$query = "SELECT * FROM log4php_log";
		$sth = $db->query($query);
		$row = $sth->fetch(PDO::FETCH_NUM);
		
		self::assertTrue(is_array($row), "No rows found.");
		self::assertEquals(7, count($row));
		self::assertEquals(1, preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $row[0])); // datetime
		self::assertEquals('TEST', $row[1]); // logger name
		self::assertEquals('ERROR', $row[2]); // level
		self::assertEquals('testmessage', $row[3]); // message
		if (function_exists('posix_getpid')) {
			self::assertEquals(posix_getpid(), $row[4]); // process id
		}
		self::assertEquals('NA', $row[5]); // file, NA due to phpunit magic
		self::assertEquals('NA', $row[6]); // line, NA due to phpunit magic
	}


	/** Tests new style prepared statment logging with customized SQL. */
	public function testCustomizedSql() {
		
		$dateFormat = "Y-m-d H:i:s";
		
		// Prepare appender
		$appender = new LoggerAppenderPDO("myname");
		$appender->setDSN(self::$dsn);
		$appender->setInsertSql("INSERT INTO log4php_log (file, line, thread, timestamp, logger, level, message) VALUES (?,?,?,?,?,?,?)");
		$appender->setInsertPattern("%F,%L,%t,%d\{$dateFormat\},%c,%p,%m");
		$appender->activateOptions();

		// Action!
		$event = new LoggerLoggingEvent("LoggerAppenderPDOTest2", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		$appender->append($event);
		
		// Check
		$db = new PDO(self::$dsn);
		$result = $db->query("SELECT * FROM log4php_log");
		$row = $result->fetch(PDO::FETCH_OBJ);
		self::assertTrue(is_object($row));
		self::assertEquals("NA", $row->file); // "NA" due to phpunit magic
		self::assertEquals("NA", $row->line); // "NA" due to phpunit magic
		if (function_exists('posix_getpid')) {
			self::assertEquals(posix_getpid(), $row->thread);
		}
		self::assertEquals(1, preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $row->timestamp));
		self::assertEquals('TEST', $row->logger);
		self::assertEquals('ERROR', $row->level);
		self::assertEquals('testmessage', $row->message);
	}
	
	/** 
	 * Tests a warning is shown when connecting to invalid dns. 
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage invalid data source name
	 */
	public function testException() {
		$dsn = 'doenotexist';
		$appender = new LoggerAppenderPDO("myname");
		$appender->setDSN($dsn);
		$appender->activateOptions();
	}
	
	/**
	 * Check whether close() actually closes the database connection. 
	 */
	public function testClose() {
		$event = new LoggerLoggingEvent("LoggerAppenderPDOTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		
		$appender = new LoggerAppenderPDO("myname");
		$appender->setDSN(self::$dsn);
		$appender->activateOptions();
		$appender->append($event);
		$appender->close();
		
		self::assertNull($appender->getDatabaseHandle());
	}
}
