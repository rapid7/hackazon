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
class LoggerAppenderMailTest extends PHPUnit_Framework_TestCase {
        
	public function testRequiresLayout() {
		$appender = new LoggerAppenderMail(); 
		self::assertTrue($appender->requiresLayout());
	}
	
	public function testMail() {
		$appender = new LoggerAppenderMail("myname ");
		
		$layout = new LoggerLayoutSimple();
		$appender->setLayout($layout);
		$appender->setDry(true);
		$appender->setTo('test@example.com');
		$appender->setFrom('Testsender');
		
		$appender->activateOptions();
		$event = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		$event2 = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage2");
		 
		ob_start();
		$appender->append($event);
		$appender->append($event2);
		$appender->close();
		$v = ob_get_contents();
		ob_end_clean();

		$e = "DRY MODE OF MAIL APP.: Send mail to: test@example.com with content: ERROR - testmessage".PHP_EOL."ERROR - testmessage2".PHP_EOL;
		self::assertEquals($e, $v);
    }
    
}
