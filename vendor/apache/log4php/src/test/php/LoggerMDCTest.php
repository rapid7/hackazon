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
class LoggerMDCTest extends PHPUnit_Framework_TestCase {
	
	/** A pattern with 1 key. */
	private $pattern1 = "%-5p %c: %X{key1} %m";
	
	/** A pattern with 2 keys. */
	private $pattern2 = "%-5p %c: %X{key1} %X{key2} %m";
	
	/** A pattern with 3 keys (one is numeric). */
	private $pattern3 = "%-5p %c: %X{key1} %X{key2} %X{3} %m";
	
	/** A pattern with a non-existant key. */
	private $pattern4 = "%-5p %c: %X{key_does_not_exist} %m";
	
	/** A pattern without a key. */
	private $pattern5 = "%-5p %c: %X %m";
	
	protected function setUp() {
		LoggerMDC::clear();
	}
	
	protected function tearDown() {
		LoggerMDC::clear();
	}
	
	public function testPatterns() {

		// Create some data to test with
		LoggerMDC::put('key1', 'valueofkey1');
		LoggerMDC::put('key2', 'valueofkey2');
		LoggerMDC::put(3, 'valueofkey3');
		
		$expected = array(
			'key1' => 'valueofkey1',
			'key2' => 'valueofkey2',
			3 => 'valueofkey3',
		);
		$actual = LoggerMDC::getMap();
		
		self::assertSame($expected, $actual);
		
		$event = LoggerTestHelper::getInfoEvent("Test message");

		// Pattern with 1 key
		$actual = $this->formatEvent($event, $this->pattern1);
		$expected = "INFO  test: valueofkey1 Test message";
		self::assertEquals($expected, $actual);
		
		// Pattern with 2 keys
		$actual = $this->formatEvent($event, $this->pattern2);
		$expected = "INFO  test: valueofkey1 valueofkey2 Test message";
		self::assertEquals($expected, $actual);
		
		// Pattern with 3 keys (one numeric)
		$actual = $this->formatEvent($event, $this->pattern3);
		$expected = "INFO  test: valueofkey1 valueofkey2 valueofkey3 Test message";
		self::assertEquals($expected, $actual);
		
		// Pattern with non-existant key
		$actual = $this->formatEvent($event, $this->pattern4);
		$expected = "INFO  test:  Test message";
		self::assertEquals($expected, $actual);
		
		// Pattern with an empty key
    	$actual = $this->formatEvent($event, $this->pattern5);
		$expected = "INFO  test: key1=valueofkey1, key2=valueofkey2, 3=valueofkey3 Test message";
		self::assertEquals($expected, $actual);
		
		// Test key removal
		LoggerMDC::remove('key1');
		$value = LoggerMDC::get('key1');
		self::assertEquals('', $value);
		
		// Pattern with 1 key, now removed
		$actual = $this->formatEvent($event, $this->pattern1);
		$expected = "INFO  test:  Test message";
		self::assertEquals($expected, $actual);
    }
    
	private function formatEvent($event, $pattern) {
		$layout = new LoggerLayoutPattern();
		$layout->setConversionPattern($pattern);
		$layout->activateOptions();
		return $layout->format($event);
	}
}

?>
