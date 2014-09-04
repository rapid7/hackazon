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
 * @group layouts
 */
class LoggerLayoutPatternTest extends PHPUnit_Framework_TestCase {

	/** Pattern used for testing. */
	private $pattern = "%-6level %logger: %msg from %class::%method() in %file at %line%n";
	
	public function testComplexLayout() {
		
		$config = LoggerTestHelper::getEchoPatternConfig($this->pattern);
		Logger::configure($config);
		
		ob_start();
		$log = Logger::getLogger('LoggerTest');
		$log->error("my message"); $line = __LINE__;
		$actual = ob_get_contents();
		ob_end_clean();
		
		$file = __FILE__;
		$class = __CLASS__;
		$method = __FUNCTION__;
		
		$expected = "ERROR  LoggerTest: my message from $class::$method() in $file at $line" . PHP_EOL;
		self::assertSame($expected, $actual);
		
		Logger::resetConfiguration();
    }
}
