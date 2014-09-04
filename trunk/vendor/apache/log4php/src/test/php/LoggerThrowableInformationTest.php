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
class LoggerThrowableInformationTest extends PHPUnit_Framework_TestCase {

	public function testConstructor() {
		$ex = new Exception();
		$tInfo = new LoggerThrowableInformation($ex);
		
		$result	  = $tInfo->getStringRepresentation();
		$this->assertInternalType('array', $result);
	}
	
	public function testExceptionChain() {
		$ex1 = new LoggerThrowableInformationTestException('Message1');
		$ex2 = new LoggerThrowableInformationTestException('Message2', 0, $ex1);
		$ex3 = new LoggerThrowableInformationTestException('Message3', 0, $ex2);

		$tInfo	  = new LoggerThrowableInformation($ex3);
		$result	 = $tInfo->getStringRepresentation();
		$this->assertInternalType('array', $result);
	}
	
	public function testGetThrowable() {
		$ex = new LoggerThrowableInformationTestException('Message1');		
		$tInfo = new LoggerThrowableInformation($ex);
		$result = $tInfo->getThrowable();		
		$this->assertEquals($ex, $result);
	}
}


if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
	class LoggerThrowableInformationTestException extends Exception { }
} else {
	class LoggerThrowableInformationTestException extends Exception {
		
		protected $previous;
		
		public function __construct($message = '', $code = 0, Exception $previous = null) {
			parent::__construct($message, $code);
			$this->previous = $previous;
		}
		
		public function getPrevious() {
			return $this->previous;
		}
	}
}
?>