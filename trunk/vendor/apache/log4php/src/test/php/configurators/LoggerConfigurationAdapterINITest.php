<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *	   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   tests
 * @package	   log4php
 * @subpackage configurators
 * @license	   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    $Revision$
 * @link       http://logging.apache.org/log4php
 */

/**
 * @group configurators
 */
class LoggerConfigurationAdapterINITest extends PHPUnit_Framework_TestCase {
	
	/** Expected output of parsing config1.ini. */
	private $expected1 = array(
		'threshold' => 'debug',
		'rootLogger' => array(
			'level' => 'DEBUG',
			'appenders' => array('default'),
		),
		'appenders' => array(
			'default' => array(
				'class' => 'LoggerAppenderEcho',
				'layout' => array(
					'class' => 'LoggerLayoutTTCC',
				),
			),
			'file' => array(
				'class' => 'LoggerAppenderDailyFile',
				'layout' => array(
					'class' => 'LoggerLayoutPattern',
					'params' => array(
						'conversionPattern' => '%d{ISO8601} [%p] %c: %m (at %F line %L)%n',
					),
				),
				'params' => array(
					'datePattern' => 'Ymd',
					'file' => 'target/examples/daily_%s.log',
				),
				'threshold' => 'warn'
			),
		),
		'loggers' => array(
			'foo' => array(
				'level' => 'warn',
				'appenders' => array('default'),
			),
			'foo.bar' => array(
				'level' => 'debug',
				'appenders' => array('file'),
				'additivity' => 'true',
			),
			'foo.bar.baz' => array(
				'level' => 'trace',
				'appenders' => array('default', 'file'),
				'additivity' => 'false',
			),
		),
		'renderers' => array(
			array(
				'renderedClass' => 'Fruit',
				'renderingClass' => 'FruitRenderer',
			),
			array(
				'renderedClass' => 'Beer',
				'renderingClass' => 'BeerRenderer',
			),
		),
	);	
	
	public function testConfig() {
		$url = PHPUNIT_CONFIG_DIR . '/adapters/ini/config_valid.ini';
		$adapter = new LoggerConfigurationAdapterINI();
		$actual = $adapter->convert($url);
	
		$this->assertSame($this->expected1, $actual);
	}
	
	/**
	 * Test exception is thrown when file cannot be found.
 	 * @expectedException LoggerException
 	 * @expectedExceptionMessage File [you/will/never/find/me.ini] does not exist.
	 */
	public function testNonExistantFileException() {
		$adapter = new LoggerConfigurationAdapterINI();
		$adapter->convert('you/will/never/find/me.ini');
	}
	
	/**
	 * Test exception is thrown when file is not a valid ini file.
	 * @expectedException LoggerException
	 * @expectedExceptionMessage Error parsing configuration file
	 */
	public function testInvalidFileException() {
		$url =  PHPUNIT_CONFIG_DIR . '/adapters/ini/config_invalid_syntax.ini';
		$adapter = new LoggerConfigurationAdapterINI();
		$adapter->convert($url);
	}

	/**
	 * Test a warning is triggered when configurator doesn't understand a line.
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage log4php: Don't know how to parse the following line: "log4php.appender.default.layout.param.bla = LoggerLayoutTTCC". Skipping.
	 */
	public function testInvalidLineWarning1() {
		$url =  PHPUNIT_CONFIG_DIR . '/adapters/ini/config_invalid_appender_declaration_1.ini';
		$adapter = new LoggerConfigurationAdapterINI();
		$adapter->convert($url);
	}
	
	/**
	 * Test a warning is triggered when configurator doesn't understand a line.
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage log4php: Don't know how to parse the following line: "log4php.appender.default.not-layout.param = LoggerLayoutTTCC". Skipping.
	 */
	public function testInvalidLineWarning2() {
		$url =  PHPUNIT_CONFIG_DIR . '/adapters/ini/config_invalid_appender_declaration_2.ini';
		$adapter = new LoggerConfigurationAdapterINI();
		$adapter->convert($url);
	}

	/**
	 * Check that various boolean equivalents from ini file convert properly 
	 * to boolean. 
	 */
	public function testBooleanValues() {
		$values = parse_ini_file(PHPUNIT_CONFIG_DIR . '/adapters/ini/values.ini');
		
		$actual = LoggerOptionConverter::toBooleanEx($values['unquoted_true']);
		self::assertTrue($actual);
		
		$actual = LoggerOptionConverter::toBooleanEx($values['unquoted_yes']);
		self::assertTrue($actual);
		
		$actual = LoggerOptionConverter::toBooleanEx($values['unquoted_false']);
		self::assertFalse($actual);
		
		$actual = LoggerOptionConverter::toBooleanEx($values['unquoted_no']);
		self::assertFalse($actual);
		
		$actual = LoggerOptionConverter::toBooleanEx($values['quoted_true']);
		self::assertTrue($actual);
		
		$actual = LoggerOptionConverter::toBooleanEx($values['quoted_false']);
		self::assertFalse($actual);
		
		$actual = LoggerOptionConverter::toBooleanEx($values['unquoted_one']);
		self::assertTrue($actual);
		
		$actual = LoggerOptionConverter::toBooleanEx($values['unquoted_zero']);
		self::assertFalse($actual);
	}
	
}

?>