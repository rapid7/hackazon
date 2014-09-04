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
class LoggerLayoutSerializedTest extends PHPUnit_Framework_TestCase {

	public function testLocationInfo() {
		$layout = new LoggerLayoutSerialized();
		self::assertFalse($layout->getLocationInfo());
		$layout->setLocationInfo(true);
		self::assertTrue($layout->getLocationInfo());
		$layout->setLocationInfo(false);
		self::assertFalse($layout->getLocationInfo());
	}
	
	/**
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Invalid value given for 'locationInfo' property: ['foo']. Expected a boolean value. Property not changed.
	 */
	public function testLocationInfoFail() {
		$layout = new LoggerLayoutSerialized();
		$layout->setLocationInfo('foo');
	}
	
	public function testLayout() {
		Logger::configure(array(
			'appenders' => array(
				'default' => array(
					'class' => 'LoggerAppenderEcho',
					'layout' => array(
						'class' => 'LoggerLayoutSerialized'
					)
				)
			),
			'rootLogger' => array(
				'appenders' => array('default')
			)
		));

		ob_start();
		$foo = Logger::getLogger('foo');
		$foo->info("Interesting message.");
		$actual = ob_get_contents();
		ob_end_clean();
		
		$event = unserialize($actual);
		
		self::assertInstanceOf('LoggerLoggingEvent', $event);
		self::assertEquals('Interesting message.', $event->getMessage());
		self::assertEquals(LoggerLevel::getLevelInfo(), $event->getLevel());
	}
	
	public function testLayoutWithLocationInfo() {
		Logger::configure(array(
			'appenders' => array(
				'default' => array(
					'class' => 'LoggerAppenderEcho',
					'layout' => array(
						'class' => 'LoggerLayoutSerialized',
						'params' => array(
							'locationInfo' => true
						)
					)
				)
			),
			'rootLogger' => array(
				'appenders' => array('default')
			)
		));
	
		ob_start();
		$foo = Logger::getLogger('foo');
		$foo->info("Interesting message.");
		$actual = ob_get_contents();
		ob_end_clean();
	
		$event = unserialize($actual);
	
		self::assertInstanceOf('LoggerLoggingEvent', $event);
		self::assertEquals('Interesting message.', $event->getMessage());
		self::assertEquals(LoggerLevel::getLevelInfo(), $event->getLevel());
	}
}
