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
 * @subpackage pattern
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    $Revision$
 * @link       http://logging.apache.org/log4php
 */

/** Converter referencing non-existant superglobal variable. */
class LoggerInvalidSuperglobalConverter extends LoggerPatternConverterSuperglobal {
	protected $name = '_FOO';
}

/**
 * @group pattern
 */
class LoggerPatternConverterTest extends PHPUnit_Framework_TestCase {

	/**
	 * A logging event for testing.
	 * @var LoggerLoggingEvent
	 */
	private $event;

	/**
	 * Fromatting info used with the logging event.
	 * @var LoggerFormattingInfos
	 */
	private $info;

	public function __construct() {
		$this->event = LoggerTestHelper::getInfoEvent('foobar');
		$this->info = new LoggerFormattingInfo();
	}

	public function testCookie() {
		// Fake a couple of cookies
		$_COOKIE['test1'] = 'value1';
		$_COOKIE['test2'] = 'value2';

		$converter = new LoggerPatternConverterCookie($this->info, 'test1');
		$actual = $converter->convert($this->event);
		$expected = 'value1';
		self::assertSame($expected, $actual);

		$converter = new LoggerPatternConverterCookie($this->info, 'test2');
		$actual = $converter->convert($this->event);
		$expected = 'value2';
		self::assertSame($expected, $actual);

		$converter = new LoggerPatternConverterCookie($this->info);
		$actual = $converter->convert($this->event);
		$expected = "test1=value1, test2=value2";
		self::assertSame($expected, $actual);
	}

	public function testDate() {
		$converter = new LoggerPatternConverterDate($this->info, 'c');
		$actual = $converter->convert($this->event);
		$expected = date('c', $this->event->getTimeStamp());
		self::assertSame($expected, $actual);

		// Format defaults to 'c'
		$converter = new LoggerPatternConverterDate($this->info);
		$actual = $converter->convert($this->event);
		$expected = date('c', $this->event->getTimeStamp());
		self::assertSame($expected, $actual);
		
		$converter = new LoggerPatternConverterDate($this->info, '');
		$actual = $converter->convert($this->event);
		$expected = date('c', $this->event->getTimeStamp());
		self::assertSame($expected, $actual);

		// Test ABSOLUTE
		$converter = new LoggerPatternConverterDate($this->info, 'ABSOLUTE');
		$actual = $converter->convert($this->event);
		$expected = date('H:i:s', $this->event->getTimeStamp());
		self::assertSame($expected, $actual);

		// Test DATE
		$converter = new LoggerPatternConverterDate($this->info, 'DATE');
		$actual = $converter->convert($this->event);
		$expected = date('d M Y H:i:s.', $this->event->getTimeStamp());

		$timestamp = $this->event->getTimeStamp();
		$ms = floor(($timestamp - floor($timestamp)) * 1000);
		$ms = str_pad($ms, 3, '0', STR_PAD_LEFT);

		$expected .= $ms;

		self::assertSame($expected, $actual);
	}

	public function testEnvironment() {
		// Fake a couple of environment values
		$_ENV['test1'] = 'value1';
		$_ENV['test2'] = 'value2';

		$converter = new LoggerPatternConverterEnvironment($this->info, 'test1');
		$actual = $converter->convert($this->event);
		$expected = 'value1';
		self::assertSame($expected, $actual);

		$converter = new LoggerPatternConverterEnvironment($this->info, 'test2');
		$actual = $converter->convert($this->event);
		$expected = 'value2';
		self::assertSame($expected, $actual);
	}

	public function testLevel() {
		$converter = new LoggerPatternConverterLevel($this->info);
		$actual = $converter->convert($this->event);
		$expected = $this->event->getLevel()->toString();
		self::assertEquals($expected, $actual);
	}

	public function testLiteral() {
		$converter = new LoggerPatternConverterLiteral('foo bar baz');
		$actual = $converter->convert($this->event);
		$expected = 'foo bar baz';
		self::assertEquals($expected, $actual);
	}

	public function testLoggerWithoutOption() {
		$event = LoggerTestHelper::getInfoEvent('foo', 'TestLoggerName');
		$converter = new LoggerPatternConverterLogger($this->info);

		$actual = $converter->convert($event);
		$expected = 'TestLoggerName';
		self::assertEquals($expected, $actual);
	}

	public function testLoggerWithOption0() {
		$event = LoggerTestHelper::getInfoEvent('foo', 'TestLoggerName');
		$converter = new LoggerPatternConverterLogger($this->info, '0');

		$actual = $converter->convert($event);
		$expected = 'TestLoggerName';
		self::assertEquals($expected, $actual);
	}

	public function testLocation() {
		$config = LoggerTestHelper::getEchoPatternConfig("%file:%line:%class:%method");
		Logger::configure($config);

		// Test by capturing output. Logging methods of a Logger object must
		// be used for the location info to be formed correctly.
		ob_start();
		$log = Logger::getLogger('foo');
		$log->info('foo'); $line = __LINE__; // Do NOT move this to next line.
		$actual = ob_get_contents();
		ob_end_clean();

		$expected = implode(':', array(__FILE__, $line, __CLASS__, __FUNCTION__));
		self::assertSame($expected, $actual);

		Logger::resetConfiguration();
	}
	
	public function testLocation2() {
		$config = LoggerTestHelper::getEchoPatternConfig("%location");
		Logger::configure($config);
	
		// Test by capturing output. Logging methods of a Logger object must
		// be used for the location info to be formed correctly.
		ob_start();
		$log = Logger::getLogger('foo');
		$log->info('foo'); $line = __LINE__; // Do NOT move this to next line.
		$actual = ob_get_contents();
		ob_end_clean();
	
		$class = __CLASS__;
		$func = __FUNCTION__;
		$file = __FILE__;
		
		$expected = "$class.$func($file:$line)";
		self::assertSame($expected, $actual);
	
		Logger::resetConfiguration();
	}

	public function testMessage() {
		$expected = "This is a message.";
		$event = LoggerTestHelper::getInfoEvent($expected);
		$converter = new LoggerPatternConverterMessage($this->info);
		$actual = $converter->convert($event);
		self::assertSame($expected, $actual);
	}

	public function testMDC() {
		LoggerMDC::put('foo', 'bar');
		LoggerMDC::put('bla', 'tra');

		// Entire context
		$converter = new LoggerPatternConverterMDC($this->info);
		$actual = $converter->convert($this->event);
		$expected = 'foo=bar, bla=tra';
		self::assertSame($expected, $actual);

		// Just foo
		$converter = new LoggerPatternConverterMDC($this->info, 'foo');
		$actual = $converter->convert($this->event);
		$expected = 'bar';
		self::assertSame($expected, $actual);

		// Non existant key
		$converter = new LoggerPatternConverterMDC($this->info, 'doesnotexist');
		$actual = $converter->convert($this->event);
		$expected = '';
		self::assertSame($expected, $actual);

		LoggerMDC::clear();
	}

	public function testNDC() {
		LoggerNDC::push('foo');
		LoggerNDC::push('bar');
		LoggerNDC::push('baz');

		$converter = new LoggerPatternConverterNDC($this->info);
		$expected = 'foo bar baz';
		$actual = $converter->convert($this->event);
		self::assertEquals($expected, $actual);
	}

	public function testNewline() {
		$converter = new LoggerPatternConverterNewLine($this->info);
		$actual = $converter->convert($this->event);
		$expected = PHP_EOL;
		self::assertSame($expected, $actual);
	}

	public function testProcess() {
		$converter = new LoggerPatternConverterProcess($this->info);
		$actual = $converter->convert($this->event);
		$expected = getmypid();
		self::assertSame($expected, $actual);
	}

	public function testRelative() {
		$converter = new LoggerPatternConverterRelative($this->info);
		$expected = number_format($this->event->getTimeStamp() - $this->event->getStartTime(), 4);
		$actual = $converter->convert($this->event);
		self::assertSame($expected, $actual);
	}

	public function testRequest() {
		// Fake a couple of request values
		$_REQUEST['test1'] = 'value1';
		$_REQUEST['test2'] = 'value2';

		// Entire request
		$converter = new LoggerPatternConverterRequest($this->info);
		$actual = $converter->convert($this->event);
		$expected = 'test1=value1, test2=value2';
		self::assertSame($expected, $actual);

		// Just test2
		$converter = new LoggerPatternConverterRequest($this->info, 'test2');
		$actual = $converter->convert($this->event);
		$expected = 'value2';
		self::assertSame($expected, $actual);
	}

	public function testServer() {
		// Fake a server value
		$_SERVER['test1'] = 'value1';

		$converter = new LoggerPatternConverterServer($this->info, 'test1');
		$actual = $converter->convert($this->event);
		$expected = 'value1';
		self::assertSame($expected, $actual);
	}

	public function testSession() {
		// Fake a session value
		$_SESSION['test1'] = 'value1';

		$converter = new LoggerPatternConverterSession($this->info, 'test1');
		$actual = $converter->convert($this->event);
		$expected = 'value1';
		self::assertSame($expected, $actual);
	}

	public function testSessionID() {
		$converter = new LoggerPatternConverterSessionID($this->info);
		$actual = $converter->convert($this->event);
		$expected = session_id();
		self::assertSame($expected, $actual);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage log4php: LoggerInvalidSuperglobalConverter: Cannot find superglobal variable $_FOO
	 */
	public function testNonexistantSuperglobal() {
		$converter = new LoggerInvalidSuperglobalConverter($this->info);
		$actual = $converter->convert($this->event);
	}

	public function testFormattingTrimRight() {
		$event = LoggerTestHelper::getInfoEvent('0123456789');

		$info = new LoggerFormattingInfo();
		$info->max = 5;

		$converter = new LoggerPatternConverterMessage($info);
		$actual = "";
		$converter->format($actual, $event);
		$expected = "01234";
		self::assertSame($expected, $actual);
	}

	public function testFormattingTrimLeft() {
		$event = LoggerTestHelper::getInfoEvent('0123456789');

		$info = new LoggerFormattingInfo();
		$info->max = 5;
		$info->trimLeft = true;

		$converter = new LoggerPatternConverterMessage($info);
		$actual = "";
		$converter->format($actual, $event);
		$expected = "56789";
		self::assertSame($expected, $actual);
	}

	public function testFormattingPadEmpty() {
		$event = LoggerTestHelper::getInfoEvent('');

		$info = new LoggerFormattingInfo();
		$info->min = 5;

		$converter = new LoggerPatternConverterMessage($info);
		$actual = "";
		$converter->format($actual, $event);
		$expected = "     ";
		self::assertSame($expected, $actual);
	}

	public function testFormattingPadLeft() {
		$event = LoggerTestHelper::getInfoEvent('0');

		$info = new LoggerFormattingInfo();
		$info->min = 5;

		$converter = new LoggerPatternConverterMessage($info);
		$actual = "";
		$converter->format($actual, $event);
		$expected = "    0";
		self::assertSame($expected, $actual);
	}

	public function testFormattingPadRight() {
		$event = LoggerTestHelper::getInfoEvent('0');

		$info = new LoggerFormattingInfo();
		$info->min = 5;
		$info->padLeft = false;

		$converter = new LoggerPatternConverterMessage($info);
		$actual = "";
		$converter->format($actual, $event);
		$expected = "0    ";
		self::assertSame($expected, $actual);
	}
}
