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
 * Tests the syslog appender.
 * 
 * Many of these tests rely on reflection features introduced in 5.3 and 
 * will be skipped if run on a lower version. 
 * 
 * This test will only write a single entry to the syslog.
 * 
 * @group appenders
 */
class LoggerAppenderSyslogTest extends PHPUnit_Framework_TestCase {

	public function testSettersGetters() {
		
		// Setters should accept any value, without validation 
		$expected = "Random string value";
		
		$appender = new LoggerAppenderSyslog();
		$appender->setIdent($expected);
		$appender->setFacility($expected);
		$appender->setOverridePriority($expected);
		$appender->setPriority($expected);
		$appender->setOption($expected);
		
		$actuals = array(
			$appender->getIdent(),
			$appender->getFacility(),
			$appender->getOverridePriority(),
			$appender->getPriority(),
			$appender->getOption()
		);
		
		foreach($actuals as $actual) {
			$this->assertSame($expected, $actual);
		}
	}
	
	public function testRequiresLayout() {
		$appender = new LoggerAppenderSyslog();
		$this->assertTrue($appender->requiresLayout());
	}
	
	public function testLogging() {
		$appender = new LoggerAppenderSyslog("myname");
		$appender->setLayout(new LoggerLayoutSimple());
		$appender->activateOptions();
		
		$event = new LoggerLoggingEvent(__CLASS__, new Logger("TestLogger"), LoggerLevel::getLevelError(), "testmessage");
		$appender->append($event);
	}

	/** Tests parsing of "option" parameter. */
	public function testOption() {
		if(!method_exists('ReflectionProperty', 'setAccessible')) {
			$this->markTestSkipped("ReflectionProperty::setAccessible() required to perform this test (available in PHP 5.3.2+).");
		}
		
		$options = array(
			'CONS' => LOG_CONS,
			'NDELAY' => LOG_NDELAY,
			'ODELAY' => LOG_ODELAY,
			'PERROR' => LOG_PERROR,
			'PID' => LOG_PID,
			
			// test some combinations
			'CONS|NDELAY' => LOG_CONS | LOG_NDELAY,
			'PID|PERROR' => LOG_PID | LOG_PERROR,
			'CONS|PID|NDELAY' => LOG_CONS | LOG_PID | LOG_NDELAY
		);

		// Defaults
		$defaultStr = "PID|CONS";
		$default = LOG_PID | LOG_CONS;
		
		// This makes reading of a private property possible
		$property = new ReflectionProperty('LoggerAppenderSyslog', 'intOption');
		$property->setAccessible(true);
		
		// Check default value first
		$appender = new LoggerAppenderSyslog();
		$appender->activateOptions();
		$actual = $property->getValue($appender);
		$this->assertSame($default, $actual, "Failed setting default option [$defaultStr]");
		
		foreach($options as $option => $expected) {
			$appender = new LoggerAppenderSyslog();
			$appender->setOption($option);
			$appender->activateOptions();
			
			$actual = $property->getValue($appender);
			$this->assertSame($expected, $actual, "Failed setting option [$option].");
		}
	}
	
	/** Tests parsing of "priority" parameter. */
	public function testPriority() {
		if(!method_exists('ReflectionProperty', 'setAccessible')) {
			$this->markTestSkipped("ReflectionProperty::setAccessible() required to perform this test (available in PHP 5.3.2+).");
		}
	
		$default = null;
		$defaultStr = 'null';
	
		$priorities = array(
			'EMERG' => LOG_EMERG,
			'ALERT' => LOG_ALERT,
			'CRIT' => LOG_CRIT,
			'ERR' => LOG_ERR,
			'WARNING' => LOG_WARNING,
			'NOTICE' => LOG_NOTICE,
			'INFO' => LOG_INFO,
			'DEBUG' => LOG_DEBUG
		);
	
		// This makes reading of a private property possible
		$property = new ReflectionProperty('LoggerAppenderSyslog', 'intPriority');
		$property->setAccessible(true);
	
		// Check default value first
		$appender = new LoggerAppenderSyslog();
		$appender->activateOptions();
		$actual = $property->getValue($appender);
		$this->assertSame($default, $actual, "Failed setting default priority [$defaultStr].");
	
		foreach($priorities as $priority => $expected) {
			$appender = new LoggerAppenderSyslog();
			$appender->setPriority($priority);
			$appender->activateOptions();
				
			$actual = $property->getValue($appender);
			$this->assertSame($expected, $actual, "Failed setting priority [$priority].");
		}
	}
	
	/** Tests parsing of "facility" parameter. */
	public function testFacility() {
		if(!method_exists('ReflectionProperty', 'setAccessible')) {
			$this->markTestSkipped("ReflectionProperty::setAccessible() required to perform this test (available in PHP 5.3.2+).");
		}
	
		// Default value is the same on all OSs
		$default = LOG_USER;
		$defaultStr = 'USER';

		// All possible facility strings (some of which might not exist depending on the OS)
		$strings = array(
			'KERN', 'USER', 'MAIL', 'DAEMON', 'AUTH',
			'SYSLOG', 'LPR', 'NEWS', 'UUCP', 'CRON', 'AUTHPRIV',
			'LOCAL0', 'LOCAL1', 'LOCAL2', 'LOCAL3', 'LOCAL4',
			'LOCAL5', 'LOCAL6', 'LOCAL7',
		);
		
		// Only test facilities which exist on this OS
		$facilities = array();
		foreach($strings as $string) {
			$const = "LOG_$string";
			if (defined($const)) {
				$facilities[$string] = constant($const); 
			}
		}
		
		// This makes reading of a private property possible
		$property = new ReflectionProperty('LoggerAppenderSyslog', 'intFacility');
		$property->setAccessible(true);
	
		// Check default value first
		$appender = new LoggerAppenderSyslog();
		$appender->activateOptions();
		$actual = $property->getValue($appender);
		$this->assertSame($default, $default, "Failed setting default facility [$defaultStr].");
	
		foreach($facilities as $facility => $expected) {
			$appender = new LoggerAppenderSyslog();
			$appender->setFacility($facility);
			$appender->activateOptions();
	
			$actual = $property->getValue($appender);
			$this->assertSame($expected, $actual, "Failed setting priority [$facility].");
		}
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInvalidOption() {
		$appender = new LoggerAppenderSyslog();
		$appender->setOption('CONS|XYZ');
		$appender->activateOptions();
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInvalidPriority() {
		$appender = new LoggerAppenderSyslog();
		$appender->setPriority('XYZ');
		$appender->activateOptions();
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInvalidFacility() {
		$appender = new LoggerAppenderSyslog();
		$appender->setFacility('XYZ');
		$appender->activateOptions();
	}
	
	
	public function testPriorityOverride() {
		if(!method_exists('ReflectionProperty', 'setAccessible')) {
			$this->markTestSkipped("ReflectionProperty::setAccessible() required to perform this test (available in PHP 5.3.2+).");
		}
		
		$appender = new LoggerAppenderSyslog();
		$appender->setPriority('EMERG');
		$appender->setOverridePriority(true);
		$appender->activateOptions();
		
		$levels = array(
			LoggerLevel::getLevelTrace(),
			LoggerLevel::getLevelDebug(),
			LoggerLevel::getLevelInfo(),
			LoggerLevel::getLevelWarn(),
			LoggerLevel::getLevelError(),
			LoggerLevel::getLevelFatal(),
		);
		
		$expected = LOG_EMERG;
		
		$method = new ReflectionMethod('LoggerAppenderSyslog', 'getSyslogPriority');
		$method->setAccessible(true);
		
		foreach($levels as $level) {
			$actual = $method->invoke($appender, $level);
			$this->assertSame($expected, $actual);		
		}
	}
}
