<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
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
 * Testclass for the MongoDB appender.
 * 
 * This class has been originally contributed from Vladimir Gorej 
 * (http://github.com/log4mongo/log4mongo-php).
 * 
 * @group appenders
 */
class LoggerAppenderMongoDBTest extends PHPUnit_Framework_TestCase {
		
	protected $appender;
	protected $event;
	
	protected function setUp() {
		if (!extension_loaded('mongo')) {
			$this->markTestSkipped(
				'The Mongo extension is not available.'
			);
		} else {
			$this->appender = new LoggerAppenderMongoDB('mongo_appender');
			$this->event = LoggerTestHelper::getErrorEvent('mongo logging event', 'test_mongo');
		}
	}

	protected function tearDown() {
		unset($this->appender);
	}
	
	public function testHost() {
		$expected = 'mongodb://localhost';
		$this->appender->setHost($expected);
		$result = $this->appender->getHost();
		$this->assertEquals($expected, $result);
	}
	
	public function testPort() {
		$expected = 27017;
		$this->appender->setPort($expected);
		$result = $this->appender->getPort();
		$this->assertEquals($expected, $result);
	}

	public function testDatabaseName() {
		$expected = 'log4php_mongodb';
		$this->appender->setDatabaseName($expected);
		$result	= $this->appender->getDatabaseName();
		$this->assertEquals($expected, $result);
	}
	
	public function testCollectionName() {
		$expected = 'logs';
		$this->appender->setCollectionName($expected);
		$result = $this->appender->getCollectionName();
		$this->assertEquals($expected, $result);
	}
	
	public function testUserName() {
		$expected = 'char0n';
		$this->appender->setUserName($expected);
		$result = $this->appender->getUserName();
		$this->assertEquals($expected, $result);
	}
	
	public function testPassword() {
		$expected = 'secret pass';
		$this->appender->setPassword($expected);
		$result	= $this->appender->getPassword();
		$this->assertEquals($expected, $result);
	}

	public function testTimeout() {
		$expected = 4000;
		$this->appender->setTimeout($expected);
		$result	= $this->appender->getTimeout();
		$this->assertEquals($expected, $result);
	}

	public function testActivateOptions() {
		$this->appender->activateOptions();
		$this->assertInstanceOf('Mongo', $this->appender->getConnection());
		$this->assertInstanceOf('MongoCollection', $this->appender->getCollection());
	}

	public function testActivateOptionsNoCredentials() {
		$this->appender->setUserName(null);
		$this->appender->setPassword(null);
		$this->appender->activateOptions();
		$this->assertInstanceOf('Mongo', $this->appender->getConnection());
		$this->assertInstanceOf('MongoCollection', $this->appender->getCollection());
	}

	public function testFormat() {
		$this->appender->activateOptions();
		$record = $this->logOne($this->event);
		
		$this->assertEquals('ERROR', $record['level']);
		$this->assertEquals('mongo logging event', $record['message']);
		$this->assertEquals('test_mongo', $record['loggerName']);
		
		$this->assertEquals('NA', $record['fileName']);		
		$this->assertEquals('getLocationInformation', $record['method']);
		$this->assertEquals('NA', $record['lineNumber']);
		$this->assertEquals('LoggerLoggingEvent', $record['className']);
		
		$this->assertTrue(is_int($record['thread']));
		$this->assertSame(getmypid(), $record['thread']);
		$this->assertTrue(is_int($record['lineNumber']) || $record['lineNumber'] == 'NA');
	}

	public function testFormatThrowableInfo() {
		$this->appender->activateOptions();
		$event = new LoggerLoggingEvent(
			'testFqcn',
			new Logger('test.Logger'),
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new Exception('test exception', 1)
		);
		
		$record = $this->logOne($event);
		
		$this->assertArrayHasKey('exception', $record);
		$this->assertEquals(1, $record['exception']['code']);
		$this->assertEquals('test exception', $record['exception']['message']);
		$this->assertContains('[internal function]: LoggerAppenderMongoDBTest', $record['exception']['stackTrace']);
	}

	 public function testFormatThrowableInfoWithInnerException() {

		 // Skip test if PHP version is lower than 5.3.0 (no inner exception support)
		 if (version_compare(PHP_VERSION, '5.3.0') < 0) {
			 $this->markTestSkipped();
		 }

		 $this->appender->activateOptions();
		 $event = new LoggerLoggingEvent(
			 'testFqcn',
			 new Logger('test.Logger'),
			 LoggerLevel::getLevelWarn(),
			 'test message',
			 microtime(true),
			 new Exception('test exception', 1, new Exception('test exception inner', 2))
		 );

		 $record = $this->logOne($event);

		 $this->assertArrayHasKey('exception', $record);
		 $this->assertEquals(1, $record['exception']['code']);
		 $this->assertEquals('test exception', $record['exception']['message']);
		 $this->assertContains('[internal function]: LoggerAppenderMongoDBTest', $record['exception']['stackTrace']);

		 $this->assertArrayHasKey('innerException', $record['exception']);
		 $this->assertEquals(2, $record['exception']['innerException']['code']);
		 $this->assertEquals('test exception inner', $record['exception']['innerException']['message']);
	 }


	 public function testClose() {
		 $this->appender->activateOptions();
		 $this->assertInstanceOf('Mongo', $this->appender->getConnection());
		 $this->assertInstanceOf('MongoCollection', $this->appender->getCollection());
		 $this->appender->close();
		 $this->assertNull($this->appender->getConnection());
		 $this->assertNull($this->appender->getCollection());
	 }

	/**
	 * Logs the event and returns the record from the database.
	 * @param LoggerLoggingEvent $event
	 * @return array
	 */
	private function logOne($event)
	{
		$collection = $this->appender->getCollection();
		$collection->drop();
		$this->appender->append($event);
		$record = $collection->findOne();
		$this->assertNotNull($record, 'Could not read the record from the database.');
		return $record;
	}
}
