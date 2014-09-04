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
 * @package log4php
 */

/**
 * This class is specialized in retrieving loggers by name and also maintaining 
 * the logger hierarchy. The logger hierarchy is dealing with the several Log-Levels
 * Logger can have. From log4j website:
 * 
 * "A logger is said to be an ancestor of another logger if its name followed 
 * by a dot is a prefix of the descendant logger name. A logger is said to be
 * a parent of a child logger if there are no ancestors between itself and the 
 * descendant logger."
 * 
 * Child Loggers do inherit their Log-Levels from their Ancestors. They can
 * increase their Log-Level compared to their Ancestors, but they cannot decrease it.
 * 
 * <p>The casual user does not have to deal with this class directly.</p>
 *
 * <p>The structure of the logger hierarchy is maintained by the
 * getLogger method. The hierarchy is such that children link
 * to their parent but parents do not have any pointers to their
 * children. Moreover, loggers can be instantiated in any order, in
 * particular descendant before ancestor.</p>
 *
 * <p>In case a descendant is created before a particular ancestor,
 * then it creates a provision node for the ancestor and adds itself
 * to the provision node. Other descendants of the same ancestor add
 * themselves to the previously created provision node.</p>
 *
 * @version $Revision$
 * @package log4php
 */
class LoggerHierarchy {
	
	/** Array holding all Logger instances. */
	protected $loggers = array();
	
	/** 
	 * The root logger.
	 * @var RootLogger 
	 */
	protected $root;
	
	/** 
	 * The logger renderer map.
	 * @var LoggerRendererMap 
	 */
	protected $rendererMap;

	/** 
	 * Main level threshold. Events with lower level will not be logged by any 
	 * logger, regardless of it's configuration.
	 * @var LoggerLevel 
	 */
	protected $threshold;
	
	/**
	 * Creates a new logger hierarchy.
	 * @param LoggerRoot $root The root logger.
	 */
	public function __construct(LoggerRoot $root) {
		$this->root = $root;
		$this->setThreshold(LoggerLevel::getLevelAll());
		$this->rendererMap = new LoggerRendererMap();
	}
	 
	/**
	 * Clears all loggers.
	 */
	public function clear() {
		$this->loggers = array();
	}
	
	/**
	 * Check if the named logger exists in the hierarchy.
	 * @param string $name
	 * @return boolean
	 */
	public function exists($name) {
		return isset($this->loggers[$name]);
	}

	/**
	 * Returns all the currently defined loggers in this hierarchy as an array.
	 * @return array
	 */	 
	public function getCurrentLoggers() {
		return array_values($this->loggers);
	}
	
	/**
	 * Returns a named logger instance logger. If it doesn't exist, one is created.
	 * 
	 * @param string $name Logger name
	 * @return Logger Logger instance.
	 */
	public function getLogger($name) {
		if(!isset($this->loggers[$name])) {
			$logger = new Logger($name);

			$nodes = explode('.', $name);
			$firstNode = array_shift($nodes);
			
			// if name is not a first node but another first node is their
			if($firstNode != $name and isset($this->loggers[$firstNode])) {
				$logger->setParent($this->loggers[$firstNode]);
			} else {
				// if there is no father, set root logger as father
				$logger->setParent($this->root);
			} 
		
			// if there are more nodes than one
			if(count($nodes) > 0) {
				// find parent node
				foreach($nodes as $node) {
					$parentNode = "$firstNode.$node";
					if(isset($this->loggers[$parentNode]) and $parentNode != $name) {
						$logger->setParent($this->loggers[$parentNode]);
					}
					$firstNode .= ".$node";
				}
			}
			
			$this->loggers[$name] = $logger;
		}		
		
		return $this->loggers[$name];
	} 
	
	/**
	 * Returns the logger renderer map.
	 * @return LoggerRendererMap 
	 */
	public function getRendererMap() {
		return $this->rendererMap;
	}
	
	/**
	 * Returns the root logger.
	 * @return LoggerRoot
	 */ 
	public function getRootLogger() {
		return $this->root;
	}
	 
	/**
	 * Returns the main threshold level.
	 * @return LoggerLevel 
	 */
	public function getThreshold() {
		return $this->threshold;
	} 

	/**
	 * Returns true if the hierarchy is disabled for given log level and false
	 * otherwise.
	 * @return boolean
	 */
	public function isDisabled(LoggerLevel $level) {
		return ($this->threshold->toInt() > $level->toInt());
	}
	
	/**
	 * Reset all values contained in this hierarchy instance to their
	 * default. 
	 *
	 * This removes all appenders from all loggers, sets
	 * the level of all non-root loggers to <i>null</i>,
	 * sets their additivity flag to <i>true</i> and sets the level
	 * of the root logger to {@link LOGGER_LEVEL_DEBUG}.
	 * 
	 * <p>Existing loggers are not removed. They are just reset.
	 *
	 * <p>This method should be used sparingly and with care as it will
	 * block all logging until it is completed.</p>
	 */
	public function resetConfiguration() {
		$root = $this->getRootLogger();
		
		$root->setLevel(LoggerLevel::getLevelDebug());
		$this->setThreshold(LoggerLevel::getLevelAll());
		$this->shutDown();
		
		foreach($this->loggers as $logger) {
			$logger->setLevel(null);
			$logger->setAdditivity(true);
			$logger->removeAllAppenders();
		}
		
		$this->rendererMap->reset();
		LoggerAppenderPool::clear();
	}
	
	/**
	 * Sets the main threshold level.
	 * @param LoggerLevel $l
	 */
	public function setThreshold(LoggerLevel $threshold) {
		$this->threshold = $threshold;
	}
	
	/**
	 * Shutting down a hierarchy will <i>safely</i> close and remove
	 * all appenders in all loggers including the root logger.
	 * 
	 * The shutdown method is careful to close nested
	 * appenders before closing regular appenders. This is allows
	 * configurations where a regular appender is attached to a logger
	 * and again to a nested appender.
	 * 
	 * @todo Check if the last paragraph is correct.
	 */
	public function shutdown() {
		$this->root->removeAllAppenders();
		
		foreach($this->loggers as $logger) {
			$logger->removeAllAppenders();
		}
	}
	
	/**
	 * Prints the current Logger hierarchy tree. Useful for debugging.
	 */
	public function printHierarchy() {
		$this->printHierarchyInner($this->getRootLogger(), 0);
	}
	
	private function printHierarchyInner(Logger $current, $level) {
		for ($i = 0; $i < $level; $i++) {
			echo ($i == $level - 1) ? "|--" : "|  ";
		}
		echo $current->getName() . "\n";
		
		foreach($this->loggers as $logger) {
			if ($logger->getParent() == $current) {
				$this->printHierarchyInner($logger, $level + 1);
			}
		}
	}
} 
