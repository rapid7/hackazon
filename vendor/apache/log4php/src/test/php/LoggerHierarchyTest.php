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
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    $Revision$
 * @link       http://logging.apache.org/log4php
 */

/**
 * @group main
 */
class LoggerHierarchyTest extends PHPUnit_Framework_TestCase {
        
	private $hierarchy;
        
	protected function setUp() {
		$this->hierarchy = new LoggerHierarchy(new LoggerRoot());
	}
	
	public function testResetConfiguration() {
		$root = $this->hierarchy->getRootLogger();
		$appender = new LoggerAppenderConsole('A1');
		$root->addAppender($appender);

		$logger = $this->hierarchy->getLogger('test');
		self::assertEquals(1, count($this->hierarchy->getCurrentLoggers()));
		
		$this->hierarchy->resetConfiguration();
		self::assertEquals(LoggerLevel::getLevelDebug(), $root->getLevel());
		self::assertEquals(LoggerLevel::getLevelAll(), $this->hierarchy->getThreshold());
		self::assertEquals(1, count($this->hierarchy->getCurrentLoggers()));
		
		foreach($this->hierarchy->getCurrentLoggers() as $logger) {
			self::assertNull($logger->getLevel());
			self::assertTrue($logger->getAdditivity());
			self::assertEquals(0, count($logger->getAllAppenders()), 0);
		}
	}
	
	public function testSettingParents() {
		$hierarchy = $this->hierarchy;
		$loggerDE = $hierarchy->getLogger("de");
		$root = $loggerDE->getParent();
		self::assertEquals('root', $root->getName());
		
		$loggerDEBLUB = $hierarchy->getLogger("de.blub");
		self::assertEquals('de.blub', $loggerDEBLUB->getName());
		$p = $loggerDEBLUB->getParent();
		self::assertEquals('de', $p->getName());
		
		$loggerDEBLA = $hierarchy->getLogger("de.bla");
		$p = $loggerDEBLA->getParent();
		self::assertEquals('de', $p->getName());
		
		$logger3 = $hierarchy->getLogger("de.bla.third");
		$p = $logger3->getParent();
		self::assertEquals('de.bla', $p->getName());
		
		$p = $p->getParent();
		self::assertEquals('de', $p->getName());
	}
	
	public function testExists() {
		$hierarchy = $this->hierarchy;
		$logger = $hierarchy->getLogger("de");
		
		self::assertTrue($hierarchy->exists("de"));
		
		$logger = $hierarchy->getLogger("de.blub");
		self::assertTrue($hierarchy->exists("de.blub"));
		self::assertTrue($hierarchy->exists("de"));
		
		$logger = $hierarchy->getLogger("de.de");
		self::assertTrue($hierarchy->exists("de.de"));
	}
	
	public function testClear() {
		$hierarchy = $this->hierarchy;
		$logger = $hierarchy->getLogger("de");
		self::assertTrue($hierarchy->exists("de"));
		$hierarchy->clear();
		self::assertFalse($hierarchy->exists("de"));
	}
}
