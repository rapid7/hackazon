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
 * @subpackage filters
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    $Revision$
 * @link       http://logging.apache.org/log4php
 */

/**
 * @group filters
 */
class LoggerFilterDenyAllTest extends PHPUnit_Framework_TestCase {
        
	public function testDecide() {
		$filter = new LoggerFilterDenyAll();
		
		$events = array(
			LoggerTestHelper::getTraceEvent(),
			LoggerTestHelper::getDebugEvent(),
			LoggerTestHelper::getInfoEvent(),
			LoggerTestHelper::getWarnEvent(),
			LoggerTestHelper::getErrorEvent(),
			LoggerTestHelper::getFatalEvent(),
		);
		
		foreach($events as $event) {
			$actual = $filter->decide($event);
			self::assertEquals(LoggerFilter::DENY, $actual);
		}
    }
    
    public function testConfiguration() {
    	$config = LoggerConfiguratorDefault::getDefaultConfiguration();
    	$config['appenders']['default']['filters'] = array(
    		array(
    			'class' => 'LoggerFilterDenyAll'
    		)
    	);
    	
    	Logger::configure($config);
    	$logger = Logger::getRootLogger();
    	
    	ob_start();
    	$logger->trace('Test');
    	$logger->debug('Test');
    	$logger->info('Test');
    	$logger->warn('Test');
    	$logger->error('Test');
    	$logger->fatal('Test');
    	$actual = ob_get_clean();
    	
    	$this->assertEmpty($actual);
    }
}
