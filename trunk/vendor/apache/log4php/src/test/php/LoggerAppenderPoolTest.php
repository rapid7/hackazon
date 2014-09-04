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
 * @subpackage filters
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    $Revision$
 * @link       http://logging.apache.org/log4php
 */

/**
 * @group filters
 */
class LoggerAppenderPoolTest extends PHPUnit_Framework_TestCase {
        
	private $appenderMock;
	
	public function setUp() {
		$this->appenderMock = $this->getMock('LoggerAppenderConsole', array(), array(), '', false);
	}
	
 	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage log4php: Cannot add unnamed appender to pool.
 	 */
	public function testAppenderHasNoName() {
		$this->appenderMock->expects($this->once())
						   ->method('getName')
						   ->will($this->returnValue(''));
						   
		LoggerAppenderPool::add($this->appenderMock);			
	}
	
 	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage log4php: Appender [foo] already exists in pool. Overwriting existing appender.
 	 */
	public function testAppenderIsAdded() {
		$this->appenderMock->expects($this->any())
						   ->method('getName')
						   ->will($this->returnValue('foo'));
						   
		LoggerAppenderPool::add($this->appenderMock);	
		LoggerAppenderPool::add($this->appenderMock);	

		$expected = 1;
		$actual = count(LoggerAppenderPool::getAppenders());
		$this->assertEquals($expected, $actual);
	}	
}
