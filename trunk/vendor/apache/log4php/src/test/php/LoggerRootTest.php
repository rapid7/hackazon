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
class LoggerRootTest extends PHPUnit_Framework_TestCase {
    
	public function testInitialSetup() {
		$root = new LoggerRoot();
		self::assertSame(LoggerLevel::getLevelAll(), $root->getLevel());
		self::assertSame(LoggerLevel::getLevelAll(), $root->getEffectiveLevel());
		self::assertSame('root', $root->getName());
		self::assertNull($root->getParent());
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage log4php: LoggerRoot cannot have a parent.
	 */
	public function testSetParentWarning() {
		$root = new LoggerRoot();
		$logger = new Logger('test');
		$root->setParent($logger);
	}
	
	public function testSetParentResult() {
		$root = new LoggerRoot();
		$logger = new Logger('test');
		@$root->setParent($logger);
		self::assertNull($root->getParent());
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage log4php: Cannot set LoggerRoot level to null.
	 */
	public function testNullLevelWarning() {
		$root = new LoggerRoot();
		$root->setLevel(null);
	}
}
