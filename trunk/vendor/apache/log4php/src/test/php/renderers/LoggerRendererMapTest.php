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
 * @subpackage renderers
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    $Revision$
 * @link       http://logging.apache.org/log4php
 */

/** Renders everything as 'foo'. */
class FooRenderer implements LoggerRenderer {
	public function render($input) {
		return 'foo';
	}
}

class InvalidCostumObjectRenderer { }

class Fruit3 {
    public $test1 = 'test1';
    public $test2 = 'test2';
    public $test3 = 'test3';
}

class Fruit3Descendant extends Fruit3 {
}

class FruitRenderer3 implements LoggerRenderer {
    public function render($fruit) {
		return $fruit->test1 . ',' . $fruit->test2 . ',' . $fruit->test3;
	}
}

class SampleObject {
}

/**
 * @group renderers
 */
class LoggerRendererMapTest extends PHPUnit_Framework_TestCase {

	public function testDefaults() {
		
		$map = new LoggerRendererMap();
		$actual = $map->getByClassName('Exception');
		self::assertInstanceOf('LoggerRendererException', $actual);
		
		// Check non-configured objects return null
		self::assertNull($map->getByObject(new stdClass()));
		self::assertNull($map->getByClassName('stdClass'));
	}
	
	public function testClear() 
	{
		$map = new LoggerRendererMap();
		$map->clear(); // This should clear the map and remove default renderers
		self::assertNull($map->getByClassName('Exception'));
	}
	
	public function testFindAndRender() 
	{
		$map = new LoggerRendererMap();
		$map->addRenderer('Fruit3', 'FruitRenderer3');

		$fruit = new Fruit3();
		$descendant = new Fruit3Descendant();
		
		// Check rendering of fruit
		$actual = $map->findAndRender($fruit);
		$expected = 'test1,test2,test3';
		self::assertSame($expected, $actual);
		
		$actual = $map->getByObject($fruit);
		self::assertInstanceOf('FruitRenderer3', $actual);
		
		// Check rendering of fruit's descendant
		$actual = $map->findAndRender($descendant);
		$expected = 'test1,test2,test3';
		self::assertSame($expected, $actual);
		
		$actual = $map->getByObject($descendant);
		self::assertInstanceOf('FruitRenderer3', $actual);
		
		// Test rendering null returns null
		self::assertNull($map->findAndRender(null));
	}
	
	/**
	 * Try adding a non-existant class as renderer.
	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Failed adding renderer. Rendering class [DoesNotExist] not found.
	 */
	public function testAddRendererError1() 
	{
		$map = new LoggerRendererMap();
		$map->addRenderer('Fruit3', 'DoesNotExist');
	}
	
	/**
	 * Try adding a class which does not implement LoggerRenderer as renderer.
	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage Failed adding renderer. Rendering class [stdClass] does not implement the LoggerRenderer interface.
	 */
	public function testAddRendererError2() 
	{
		$map = new LoggerRendererMap();
		$map->addRenderer('Fruit3', 'stdClass');
	}
	
	public function testAddRendererError3() 
	{
		$map = new LoggerRendererMap();
		@$map->addRenderer('Fruit3', 'stdClass');
		self::assertNull($map->getByClassName('Fruit3'));
		
		@$map->addRenderer('Fruit3', 'DoesNotExist');
		self::assertNull($map->getByClassName('Fruit3'));
	}
	
	/**
	 * Try setting a non-existant class as default renderer.
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage Failed setting default renderer. Rendering class [DoesNotExist] not found.
	 */
	public function testSetDefaultRendererError1() 
	{
		$map = new LoggerRendererMap();
		$map->setDefaultRenderer('DoesNotExist');
	}
	
	/**
	 * Try setting a class which does not implement LoggerRenderer as default renderer.
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage Failed setting default renderer. Rendering class [stdClass] does not implement the LoggerRenderer interface.
	 */
	public function testSetDefaultRendererError2()
	{
		$map = new LoggerRendererMap();
		$map->setDefaultRenderer('stdClass');
	}
	
	public function testSetDefaultRendererError3()
	{
		$map = new LoggerRendererMap();
		$expected =  $map->getDefaultRenderer();
		
		@$map->setDefaultRenderer('stdClass');
		$actual = $map->getDefaultRenderer();
		self::assertSame($expected, $actual);
	
		@$map->setDefaultRenderer('DoesNotExist');
		$actual = $map->getDefaultRenderer();
		self::assertSame($expected, $actual);
	}
	
	public function testFetchingRenderer() 
	{
		$map = new LoggerRendererMap();
		$map->addRenderer('Fruit3', 'FruitRenderer3');
	}
	
	public function testDefaultRenderer() 
	{
		$fruit = new Fruit3();
		
		$map = new LoggerRendererMap();
		$actual = $map->findAndRender($fruit);
		
		$defaultRenderer = new LoggerRendererDefault();
		$expected = $defaultRenderer->render($fruit);
		self::assertSame($expected, $actual);
	}
	
	public function testOverrideDefaultRenderer() 
	{
		$map = new LoggerRendererMap();
		$default = $map->getDefaultRenderer();
		
		$array = array(1, 2, 3);
		
		$actual = $map->findAndRender($array);
		$expected = print_r($array, true);
		self::assertSame($actual, $expected);
		
		// Now switch the default renderer
		$map->setDefaultRenderer('FooRenderer');
		$actual = $map->findAndRender($array);
		$expected = 'foo';
		self::assertSame($actual, $expected);
	}

	public function testGetByObjectCrap()
	{
		$map = new LoggerRendererMap();
		
		// Non object input should always return null
		self::assertNull($map->getByObject(null));
		self::assertNull($map->getByObject(array()));
		self::assertNull($map->getByObject('sdasda'));
	}
	
	public function testXMLConfig() 
	{
		$map = Logger::getHierarchy()->getRendererMap();
		Logger::resetConfiguration();
		self::assertInstanceOf('LoggerRendererDefault', $map->getDefaultRenderer());
		
		Logger::configure(PHPUNIT_CONFIG_DIR . '/renderers/config_default_renderer.xml');
		self::assertInstanceOf('FruitRenderer3', $map->getDefaultRenderer());
		
		Logger::resetConfiguration();
		self::assertInstanceOf('LoggerRendererDefault', $map->getDefaultRenderer());
	}
	
	public function testExceptionRenderer() 
	{
		$ex = new LoggerException("This is a test");
		
		$map = new LoggerRendererMap();
		$actual = $map->findAndRender($ex);
		$expected = (string) $ex;

		self::assertSame($expected, $actual);
	}
	
	
}
