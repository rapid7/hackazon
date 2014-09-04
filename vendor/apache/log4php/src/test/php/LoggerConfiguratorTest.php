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


class CostumDefaultRenderer implements LoggerRenderer {
	public function render($o) { }
}


/**
 * 
 * @group configurators
 *
 */
 class LoggerConfiguratorTest extends PHPUnit_Framework_TestCase
 {
 	/** Reset configuration after each test. */
 	public function setUp() {
 		Logger::resetConfiguration();
 	}
 	/** Reset configuration after each test. */
 	public function tearDown() {
 		Logger::resetConfiguration();
 	}
 	
 	/** Check default setup. */
 	public function testDefaultConfig() {
 		Logger::configure();
 		
 		$actual = Logger::getCurrentLoggers();
 		$expected = array();
		$this->assertSame($expected, $actual);

 		$appenders = Logger::getRootLogger()->getAllAppenders();
 		$this->assertInternalType('array', $appenders);
 		$this->assertEquals(count($appenders), 1);
 		
 		$names = array_keys($appenders);
 		$this->assertSame('default', $names[0]);
 		
 		$appender = array_shift($appenders);
 		$this->assertInstanceOf('LoggerAppenderEcho', $appender);
 		$this->assertSame('default', $appender->getName());
 		
 		$layout = $appender->getLayout();
 		$this->assertInstanceOf('LoggerLayoutSimple', $layout);
 		
 		$root = Logger::getRootLogger();
 		$appenders = $root->getAllAppenders();
 		$this->assertInternalType('array', $appenders);
 		$this->assertEquals(count($appenders), 1);
		
 		$actual = $root->getLevel();
 		$expected = LoggerLevel::getLevelDebug();
 		$this->assertEquals($expected, $actual);
 	}
 	
 	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Invalid configuration param given. Reverting to default configuration.
 	 */
 	public function testInputIsInteger() {
 		Logger::configure(12345);
 	}
 	
 	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage log4php: Configuration failed. Unsupported configuration file extension: yml
 	 */ 	
 	public function testYAMLFile() {
		Logger::configure(PHPUNIT_CONFIG_DIR . '/config.yml');
 	}

 	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Invalid configuration provided for appender
 	 */
 	public function testAppenderConfigNotArray() {
 		$hierachyMock = $this->getMock('LoggerHierarchy', array(), array(), '', false);
 		
 		$config = array(
	 		'appenders' => array(
	            'default',
	        ),
        );

        $configurator = new LoggerConfiguratorDefault();
        $configurator->configure($hierachyMock, $config);
 	}
 	
  	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage No class given for appender
 	 */
 	public function testNoAppenderClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/appenders/config_no_class.xml');
 	} 	
 	
  	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Invalid class [unknownClass] given for appender [foo]. Class does not exist. Skipping appender definition.
 	 */
 	public function testNotExistingAppenderClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/appenders/config_not_existing_class.xml');
 	} 

   	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Invalid class [stdClass] given for appender [foo]. Not a valid LoggerAppender class. Skipping appender definition.
 	 */
 	public function testInvalidAppenderClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/appenders/config_invalid_appender_class.xml');
 	} 	
 	
    /**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Nonexistant filter class [Foo] specified on appender [foo]. Skipping filter definition.
 	 */
 	public function testNotExistingAppenderFilterClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/appenders/config_not_existing_filter_class.xml');
 	}

    /**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Nonexistant option [fooParameter] specified on [LoggerFilterStringMatch]. Skipping.
 	 */
 	public function testInvalidAppenderFilterParamter() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/appenders/config_invalid_filter_parameters.xml');
 	} 	
 	
    /**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Invalid filter class [stdClass] sepcified on appender [foo]. Skipping filter definition.
 	 */
 	public function testInvalidAppenderFilterClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/appenders/config_invalid_filter_class.xml');
 	} 	
 	
    /**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Nonexistant layout class [Foo] specified for appender [foo]. Reverting to default layout.
 	 */
 	public function testNotExistingAppenderLayoutClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/appenders/config_not_existing_layout_class.xml');
 	}
 	
    /**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Invalid layout class [stdClass] sepcified for appender [foo]. Reverting to default layout.
 	 */
 	public function testInvalidAppenderLayoutClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/appenders/config_invalid_layout_class.xml');
 	} 

    /**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Layout class not specified for appender [foo]. Reverting to default layout.
 	 */
 	public function testNoAppenderLayoutClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/appenders/config_no_layout_class.xml');
 	}   	

    /**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Failed adding renderer. Rendering class [stdClass] does not implement the LoggerRenderer interface.
 	 */
 	public function testInvalidRenderingClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/renderers/config_invalid_rendering_class.xml');
 	} 	
 	
    /**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Rendering class not specified. Skipping renderer definition.
 	 */
 	public function testNoRenderingClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/renderers/config_no_rendering_class.xml');
 	} 	

    /**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Rendered class not specified. Skipping renderer definition.
 	 */
 	public function testNoRenderedClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/renderers/config_no_rendered_class.xml');
 	} 	
 	
 	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Failed adding renderer. Rendering class [DoesNotExistRenderer] not found.
 	 */
 	public function testNotExistingRenderingClassSet() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/renderers/config_not_existing_rendering_class.xml');
 	} 	
 	
 	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Invalid additivity value [4711] specified for logger [myLogger].
 	 */
 	public function testInvalidLoggerAddivity() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/loggers/config_invalid_additivity.xml');
 	} 

 	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Nonexistnant appender [unknownAppender] linked to logger [myLogger].
 	 */
 	public function testNotExistingLoggerAppendersClass() {
 		Logger::configure(PHPUNIT_CONFIG_DIR . '/loggers/config_not_existing_appenders.xml');
 	}  	
 	
 	/**
 	 * Test that an error is reported when config file is not found. 
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage log4php: Configuration failed. File not found
 	 */
 	public function testNonexistantFile() {
 		Logger::configure('hopefully/this/path/doesnt/exist/config.xml');
 		
 	}
 	
 	/** Test correct fallback to the default configuration. */
 	public function testNonexistantFileFallback() {
 		@Logger::configure('hopefully/this/path/doesnt/exist/config.xml');
 		$this->testDefaultConfig();
 	}
 	
 	public function testAppendersWithLayout() {
 		$config = Logger::configure(array(
 			'rootLogger' => array(
 				'appenders' => array('app1', 'app2')
 			),
 			'loggers' => array(
 				'myLogger' => array(
 					'appenders' => array('app1'),
 					'additivity'=> true
 				)
 			),
 			'renderers' => array(
 				array('renderedClass' => 'stdClass', 'renderingClass' => 'LoggerRendererDefault')
 			),
 			'appenders' => array(
 				'app1' => array(
 					'class' => 'LoggerAppenderEcho',
 					'layout' => array(
 						'class' => 'LoggerLayoutSimple'
 					),
 					'params' => array(
 						'htmlLineBreaks' => false
 					)
 				),
		 		'app2' => array(
		 		 	'class' => 'LoggerAppenderEcho',
		 		 	'layout' => array(
		 		 		'class' => 'LoggerLayoutPattern',
		 		 		'params' => array(
		 		 			'conversionPattern' => 'message: %m%n'
		 		 		)
		 			),
		 			'filters' => array(
		 				array(
		 					'class'	=> 'LoggerFilterStringMatch',
		 					'params'=> array(
		 						'stringToMatch'	=> 'foo',
		 						'acceptOnMatch'	=> false
		 					)
		 				)
		 			)
		 		),
 			) 
 		));
 		
 		ob_start();
 		Logger::getRootLogger()->info('info');
 		$actual = ob_get_contents();
 		ob_end_clean();
 		
 		$expected = "INFO - info" . PHP_EOL . "message: info" . PHP_EOL;
  		$this->assertSame($expected, $actual);
 	}
 	
  	public function testThreshold()
 	{
 		Logger::configure(array(
 			'threshold' => 'WARN',
 			'rootLogger' => array(
 				'appenders' => array('default')
 			),
 			'appenders' => array(
 				'default' => array(
 					'class' => 'LoggerAppenderEcho',
 				),
 			) 
 		));
 		
 		$actual = Logger::getHierarchy()->getThreshold();
 		$expected = LoggerLevel::getLevelWarn();
 		
 		self::assertSame($expected, $actual);
 	}
 	
 	/**
 	* @expectedException PHPUnit_Framework_Error
 	* @expectedExceptionMessage Invalid threshold value [FOO] specified. Ignoring threshold definition.
 	*/
  	public function testInvalidThreshold()
 	{
 		Logger::configure(array(
 			'threshold' => 'FOO',
 			'rootLogger' => array(
 				'appenders' => array('default')
 			),
 			'appenders' => array(
 				'default' => array(
 					'class' => 'LoggerAppenderEcho',
 				),
 			) 
 		));
 	}
 	
 	public function testAppenderThreshold()
 	{
 		Logger::configure(array(
 			'rootLogger' => array(
 				'appenders' => array('default')
 			),
 			'appenders' => array(
 				'default' => array(
 					'class' => 'LoggerAppenderEcho',
 					'threshold' => 'INFO'
 				),
 			) 
 		));
 		
 		$actual = Logger::getRootLogger()->getAppender('default')->getThreshold();
 		$expected = LoggerLevel::getLevelInfo();

 		self::assertSame($expected, $actual);
 	}
 	
 	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Invalid threshold value [FOO] specified for appender [default]. Ignoring threshold definition.
 	 */
 	public function testAppenderInvalidThreshold()
 	{
 		Logger::configure(array(
 			'rootLogger' => array(
 				'appenders' => array('default')
 			),
 			'appenders' => array(
 				'default' => array(
 					'class' => 'LoggerAppenderEcho',
 					'threshold' => 'FOO'
 				),
 			) 
 		));
 	}
 	
 	public function testLoggerThreshold()
 	{
 		Logger::configure(array(
 			'rootLogger' => array(
 				'appenders' => array('default'),
 				'level' => 'ERROR'
 			),
 			'loggers' => array(
 				'default' => array(
 					'appenders' => array('default'),
 		 			'level' => 'WARN'
 				)
 			),
 			'appenders' => array(
 				'default' => array(
 					'class' => 'LoggerAppenderEcho',
 				),
 			) 
 		));
 		
 		// Check root logger
 		$actual = Logger::getRootLogger()->getLevel();
 		$expected = LoggerLevel::getLevelError();
 		self::assertSame($expected, $actual);
 		
 		// Check default logger
 		$actual = Logger::getLogger('default')->getLevel();
 		$expected = LoggerLevel::getLevelWarn();
 		self::assertSame($expected, $actual);
 	}
 	
 	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Invalid level value [FOO] specified for logger [default]. Ignoring level definition.
 	 */
 	public function testInvalidLoggerThreshold()
 	{
 		Logger::configure(array(
 			'loggers' => array(
 				'default' => array(
 					'appenders' => array('default'),
 		 			'level' => 'FOO'
 				)
 			),
 			'appenders' => array(
 				'default' => array(
 					'class' => 'LoggerAppenderEcho',
 				),
 			) 
 		));
 	}
 	
 	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Invalid level value [FOO] specified for logger [root]. Ignoring level definition.
 	 */
  	public function testInvalidRootLoggerThreshold()
 	{
 		Logger::configure(array(
 			'rootLogger' => array(
 				'appenders' => array('default'),
 				'level' => 'FOO'
 			),
 			'appenders' => array(
 				'default' => array(
 					'class' => 'LoggerAppenderEcho',
 				),
 			) 
 		));
 	}
 }