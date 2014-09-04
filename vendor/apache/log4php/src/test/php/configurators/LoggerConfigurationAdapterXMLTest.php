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
class LoggerConfigurationAdapterXMLTest extends PHPUnit_Framework_TestCase {
	
	/** Expected output of parsing config1.xml.*/
	private $expected1 = array(
		'appenders' => array(
			'default' => array(
				'class' => 'LoggerAppenderEcho',
				'layout' => array(
					'class' => 'LoggerLayoutTTCC',
				),
				'filters' => array(
					array(
						'class' => 'LoggerFilterLevelRange',
						'params' => array(
							'levelMin' => 'ERROR',
							'levelMax' => 'FATAL',
							'acceptOnMatch' => 'false',
						),
					),
					array(
						'class' => 'LoggerFilterDenyAll',
					),
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
			'foo.bar.baz' => array(
				'level' => 'trace',
				'additivity' => 'false',
				'appenders' => array('default'),
			),
			'foo.bar' => array(
				'level' => 'debug',
				'additivity' => 'true',
				'appenders' => array('file'),
			),
			'foo' => array(
				'level' => 'warn',
				'appenders' => array('default', 'file'),
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
		'threshold' => 'debug',
		'rootLogger' => array(
			'level' => 'DEBUG',
			'appenders' => array('default'),
		),
	);
	
	public function setUp() {
		Logger::resetConfiguration();
	}
	
	public function tearDown() {
		Logger::resetConfiguration();
	}
	
	public function testConversion() {
		$url =  PHPUNIT_CONFIG_DIR . '/adapters/xml/config_valid.xml';
		$adapter = new LoggerConfigurationAdapterXML();
		$actual = $adapter->convert($url);
		$this->assertEquals($this->expected1, $actual);
	}
	
	public function testConversion2() {
		$url =  PHPUNIT_CONFIG_DIR . '/adapters/xml/config_valid_underscore.xml';
		$adapter = new LoggerConfigurationAdapterXML();
		$actual = $adapter->convert($url);
		
		$this->assertEquals($this->expected1, $actual);
	}
	
	/**
	 * Test exception is thrown when file cannot be found.
 	 * @expectedException LoggerException
 	 * @expectedExceptionMessage File [you/will/never/find/me.conf] does not exist.
	 */
	public function testNonExistantFile() {
		$adapter = new LoggerConfigurationAdapterXML();
		$adapter->convert('you/will/never/find/me.conf');
	}
	
	/**
	 * Test exception is thrown when file contains invalid XML.
	 * @expectedException LoggerException
	 * @expectedExceptionMessage Error loading configuration file: Premature end of data in tag configuration line
	 */
	public function testInvalidXMLFile() {
		$url =  PHPUNIT_CONFIG_DIR . '/adapters/xml/config_invalid_syntax.xml';
		$adapter = new LoggerConfigurationAdapterXML();
		$adapter->convert($url);
	}
	
	/**
	 * Test that a warning is triggered when two loggers with the same name 
	 * are defined.
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage log4php: Duplicate logger definition [foo]. Overwriting
	 */
	public function testDuplicateLoggerWarning() {
		$url =  PHPUNIT_CONFIG_DIR . '/adapters/xml/config_duplicate_logger.xml';
		$adapter = new LoggerConfigurationAdapterXML();
		$adapter->convert($url);
	}
	
	
	/**
	 * Test that when two loggers with the same name are defined, the second 
	 * one will overwrite the first.
	 */
	public function testDuplicateLoggerConfig() {
		$url =  PHPUNIT_CONFIG_DIR . '/adapters/xml/config_duplicate_logger.xml';
		$adapter = new LoggerConfigurationAdapterXML();
		
		// Supress the warning so that test can continue 
		$config = @$adapter->convert($url);

		// Second definition of foo has level set to warn (the first to info)
		$this->assertEquals('warn', $config['loggers']['foo']['level']);		
	}
}

?>