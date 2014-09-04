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
class LoggerNDCTest extends PHPUnit_Framework_TestCase {
	
	public function testItemHandling()
	{
		// Test the empty stack
		self::assertSame('', LoggerNDC::get());
		self::assertSame('', LoggerNDC::peek());
		self::assertSame(0, LoggerNDC::getDepth());
		self::assertSame('', LoggerNDC::pop());
		
		// Add some data to the stack
		LoggerNDC::push('1');
		LoggerNDC::push('2');
		LoggerNDC::push('3');
		
		self::assertSame('1 2 3', LoggerNDC::get());
		self::assertSame('3', LoggerNDC::peek());
		self::assertSame(3, LoggerNDC::getDepth());

		// Remove last item
		self::assertSame('3', LoggerNDC::pop());
		self::assertSame('1 2', LoggerNDC::get());
		self::assertSame('2', LoggerNDC::peek());
		self::assertSame(2, LoggerNDC::getDepth());

		// Remove all items
		LoggerNDC::remove();

		// Test the empty stack
		self::assertSame('', LoggerNDC::get());
		self::assertSame('', LoggerNDC::peek());
		self::assertSame(0, LoggerNDC::getDepth());
		self::assertSame('', LoggerNDC::pop());
	}
	
	public function testMaxDepth()
	{
		// Clear stack; add some testing data
		LoggerNDC::clear();
		LoggerNDC::push('1');
		LoggerNDC::push('2');
		LoggerNDC::push('3');
		LoggerNDC::push('4');
		LoggerNDC::push('5');
		LoggerNDC::push('6');
		
		self::assertSame('1 2 3 4 5 6', LoggerNDC::get());
		
		// Edge case, should not change stack
		LoggerNDC::setMaxDepth(6);
		self::assertSame('1 2 3 4 5 6', LoggerNDC::get());
		self::assertSame(6, LoggerNDC::getDepth());
		
		LoggerNDC::setMaxDepth(3);
		self::assertSame('1 2 3', LoggerNDC::get());
		self::assertSame(3, LoggerNDC::getDepth());
		
		LoggerNDC::setMaxDepth(0);
		self::assertSame('', LoggerNDC::get());
		self::assertSame(0, LoggerNDC::getDepth());
	}
}

?>
