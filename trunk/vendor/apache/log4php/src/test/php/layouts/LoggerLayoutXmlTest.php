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
class LoggerLayoutXmlTest extends PHPUnit_Framework_TestCase {
		
	public function testErrorLayout() {
		$event = LoggerTestHelper::getErrorEvent("testmessage");

		$layout = new LoggerLayoutXml();
		$layout->activateOptions();
		
		$actual = $layout->format($event);

		$thread = $event->getThreadName();
		$timestamp = number_format(($event->getTimeStamp() * 1000), 0, '', '');
		
		$expected = "<log4php:event logger=\"test\" level=\"ERROR\" thread=\"$thread\" timestamp=\"$timestamp\">" . PHP_EOL . 
			"<log4php:message><![CDATA[testmessage]]></log4php:message>" . PHP_EOL . 
			"<log4php:locationInfo class=\"LoggerLoggingEvent\" file=\"NA\" line=\"NA\" " . 
			"method=\"getLocationInformation\" />" . PHP_EOL . 
			"</log4php:event>" . PHP_EOL;

		self::assertEquals($expected, $actual);
	}
	
	public function testWarnLayout() {
		$event = LoggerTestHelper::getWarnEvent("testmessage");

		$layout = new LoggerLayoutXml();
		$layout->activateOptions();
		
		$actual = $layout->format($event);

		$thread = $event->getThreadName();
		$timestamp = number_format(($event->getTimeStamp() * 1000), 0, '', '');
		
		$expected = "<log4php:event logger=\"test\" level=\"WARN\" thread=\"$thread\" timestamp=\"$timestamp\">" . PHP_EOL . 
			"<log4php:message><![CDATA[testmessage]]></log4php:message>" . PHP_EOL . 
			"<log4php:locationInfo class=\"LoggerLoggingEvent\" file=\"NA\" line=\"NA\" "  . 
			"method=\"getLocationInformation\" />" . PHP_EOL . 
			"</log4php:event>" . PHP_EOL;
		
		self::assertEquals($expected, $actual);
	}
	
	public function testLog4JNamespaceErrorLayout() {
		$event = LoggerTestHelper::getErrorEvent("testmessage");

		$layout = new LoggerLayoutXml();
		$layout->setLog4jNamespace(true);
		$layout->activateOptions();
		
		$actual = $layout->format($event);

		$thread = $event->getThreadName();
		$timestamp = number_format(($event->getTimeStamp() * 1000), 0, '', '');
		
		$expected = "<log4j:event logger=\"test\" level=\"ERROR\" thread=\"$thread\" timestamp=\"$timestamp\">" . PHP_EOL . 
			"<log4j:message><![CDATA[testmessage]]></log4j:message>" . PHP_EOL . 
			"<log4j:locationInfo class=\"LoggerLoggingEvent\" file=\"NA\" line=\"NA\" "  . 
			"method=\"getLocationInformation\" />" . PHP_EOL . 
			"</log4j:event>" . PHP_EOL;

		self::assertEquals($expected, $actual);
	}
	
	public function testNDC()
	{
		LoggerNDC::push('foo');
		LoggerNDC::push('bar');
		
		$event = LoggerTestHelper::getErrorEvent("testmessage");
		
		$layout = new LoggerLayoutXml();
		$layout->activateOptions();
		
		$actual = $layout->format($event);

		$thread = $event->getThreadName();
		$timestamp = number_format(($event->getTimeStamp() * 1000), 0, '', '');
		
		$expected = "<log4php:event logger=\"test\" level=\"ERROR\" thread=\"$thread\" timestamp=\"$timestamp\">" . PHP_EOL . 
			"<log4php:message><![CDATA[testmessage]]></log4php:message>" . PHP_EOL . 
			"<log4php:NDC><![CDATA[<![CDATA[foo bar]]>]]></log4php:NDC>"  .  PHP_EOL  . 
			"<log4php:locationInfo class=\"LoggerLoggingEvent\" file=\"NA\" line=\"NA\" "  . 
			"method=\"getLocationInformation\" />" . PHP_EOL . 
			"</log4php:event>" . PHP_EOL;
		
		self::assertEquals($expected, $actual);
		
		LoggerNDC::clear();
	}
	
	public function testMDC()
	{
		LoggerMDC::put('foo', 'bar');
		LoggerMDC::put('bla', 'tra');
	
		$event = LoggerTestHelper::getErrorEvent("testmessage");
	
		$layout = new LoggerLayoutXml();
		$layout->activateOptions();
	
		$actual = $layout->format($event);
	
		$thread = $event->getThreadName();
		$timestamp = number_format(($event->getTimeStamp() * 1000), 0, '', '');
		
		$expected = "<log4php:event logger=\"test\" level=\"ERROR\" thread=\"$thread\" timestamp=\"$timestamp\">" . PHP_EOL .
				"<log4php:message><![CDATA[testmessage]]></log4php:message>" . PHP_EOL . 
				"<log4php:properties>" . PHP_EOL . 
				"<log4php:data name=\"foo\" value=\"bar\" />" . PHP_EOL . 
				"<log4php:data name=\"bla\" value=\"tra\" />" . PHP_EOL . 
				"</log4php:properties>" . PHP_EOL . 
				"<log4php:locationInfo class=\"LoggerLoggingEvent\" file=\"NA\" line=\"NA\" "  . 
				"method=\"getLocationInformation\" />" . PHP_EOL . 
				"</log4php:event>" . PHP_EOL;
	
		self::assertEquals($expected, $actual);
	
		LoggerMDC::clear();
	}
}
