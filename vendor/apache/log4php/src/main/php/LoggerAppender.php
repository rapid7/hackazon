<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *		http://www.apache.org/licenses/LICENSE-2.0
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
 * Abstract class that defines output logs strategies.
 *
 * @version $Revision$
 * @package log4php
 */
abstract class LoggerAppender extends LoggerConfigurable {
	
	/**
	 * Set to true when the appender is closed. A closed appender will not 
	 * accept any logging requests. 
	 * @var boolean 
	 */
	protected $closed = false;
	
	/**
	 * The first filter in the filter chain.
	 * @var LoggerFilter
	 */
	protected $filter;
			
	/**
	 * The appender's layout. Can be null if the appender does not use 
	 * a layout.
	 * @var LoggerLayout
	 */
	protected $layout; 
	
	/**
	 * Appender name. Used by other components to identify this appender.
	 * @var string
	 */
	protected $name;
	
	/**
	 * Appender threshold level. Events whose level is below the threshold 
	 * will not be logged.
	 * @var LoggerLevel
	 */
	protected $threshold;
	
	/**
	 * Set to true if the appender requires a layout.
	 * 
	 * True by default, appenders which do not use a layout should override 
	 * this property to false.
	 * 
	 * @var boolean
	 */
	protected $requiresLayout = true;
	
	/**
	 * Default constructor.
	 * @param string $name Appender name
	 */
	public function __construct($name = '') {
		$this->name = $name;

		if ($this->requiresLayout) {
			$this->layout = $this->getDefaultLayout();
		}
	}
	
	public function __destruct() {
		$this->close();
	}
	
	/**
	 * Returns the default layout for this appender. Can be overriden by 
	 * derived appenders.
	 * 
	 * @return LoggerLayout
	 */
	public function getDefaultLayout() {
		return new LoggerLayoutSimple();
	}
	
	/**
	 * Adds a filter to the end of the filter chain.
	 * @param LoggerFilter $filter add a new LoggerFilter
	 */
	public function addFilter($filter) {
		if($this->filter === null) {
			$this->filter = $filter;
		} else {
			$this->filter->addNext($filter);
		}
	}
	
	/**
	 * Clears the filter chain by removing all the filters in it.
	 */
	public function clearFilters() {
		$this->filter = null;
	}

	/**
	 * Returns the first filter in the filter chain. 
	 * The return value may be <i>null</i> if no is filter is set.
	 * @return LoggerFilter
	 */
	public function getFilter() {
		return $this->filter;
	} 
	
	/** 
	 * Returns the first filter in the filter chain. 
	 * The return value may be <i>null</i> if no is filter is set.
	 * @return LoggerFilter
	 */
	public function getFirstFilter() {
		return $this->filter;
	}
	
	/**
	 * Performs threshold checks and invokes filters before delegating logging 
	 * to the subclass' specific <i>append()</i> method.
	 * @see LoggerAppender::append()
	 * @param LoggerLoggingEvent $event
	 */
	public function doAppend(LoggerLoggingEvent $event) {
		if($this->closed) {
			return;
		}
		
		if(!$this->isAsSevereAsThreshold($event->getLevel())) {
			return;
		}

		$filter = $this->getFirstFilter();
		while($filter !== null) {
			switch ($filter->decide($event)) {
				case LoggerFilter::DENY: return;
				case LoggerFilter::ACCEPT: return $this->append($event);
				case LoggerFilter::NEUTRAL: $filter = $filter->getNext();
			}
		}
		$this->append($event);
	}	 

	/**
	 * Sets the appender layout.
	 * @param LoggerLayout $layout
	 */
	public function setLayout($layout) {
		if($this->requiresLayout()) {
			$this->layout = $layout;
		}
	} 
	
	/**
	 * Returns the appender layout.
	 * @return LoggerLayout
	 */
	public function getLayout() {
		return $this->layout;
	}
	
	/**
	 * Configurators call this method to determine if the appender
	 * requires a layout. 
	 *
	 * <p>If this method returns <i>true</i>, meaning that layout is required, 
	 * then the configurator will configure a layout using the configuration 
	 * information at its disposal.	 If this method returns <i>false</i>, 
	 * meaning that a layout is not required, then layout configuration will be
	 * skipped even if there is available layout configuration
	 * information at the disposal of the configurator.</p>
	 *
	 * <p>In the rather exceptional case, where the appender
	 * implementation admits a layout but can also work without it, then
	 * the appender should return <i>true</i>.</p>
	 * 
	 * @return boolean
	 */
	public function requiresLayout() {
		return $this->requiresLayout;
	}
	
	/**
	 * Retruns the appender name.
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Sets the appender name.
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;	
	}
	
	/**
	 * Returns the appender's threshold level. 
	 * @return LoggerLevel
	 */
	public function getThreshold() { 
		return $this->threshold;
	}
	
	/**
	 * Sets the appender threshold.
	 * 
	 * @param LoggerLevel|string $threshold Either a {@link LoggerLevel} 
	 *   object or a string equivalent.
	 * @see LoggerOptionConverter::toLevel()
	 */
	public function setThreshold($threshold) {
		$this->setLevel('threshold', $threshold);
	}
	
	/**
	 * Checks whether the message level is below the appender's threshold. 
	 *
	 * If there is no threshold set, then the return value is always <i>true</i>.
	 * 
	 * @param LoggerLevel $level
	 * @return boolean Returns true if level is greater or equal than 
	 *   threshold, or if the threshold is not set. Otherwise returns false.
	 */
	public function isAsSevereAsThreshold($level) {
		if($this->threshold === null) {
			return true;
		}
		return $level->isGreaterOrEqual($this->getThreshold());
	}

	/**
	 * Prepares the appender for logging.
	 * 
	 * Derived appenders should override this method if option structure
	 * requires it.
	 */
	public function activateOptions() {
		$this->closed = false;
	}
	
	/**
	 * Forwards the logging event to the destination.
	 * 
	 * Derived appenders should implement this method to perform actual logging.
	 * 
	 * @param LoggerLoggingEvent $event
	 */
	abstract protected function append(LoggerLoggingEvent $event); 

	/**
	 * Releases any resources allocated by the appender.
	 * 
	 * Derived appenders should override this method to perform proper closing
	 * procedures.
	 */
	public function close() {
		$this->closed = true;
	}
	
	/** Triggers a warning for this logger with the given message. */
	protected function warn($message) {
		$id = get_class($this) . (empty($this->name) ? '' : ":{$this->name}");
		trigger_error("log4php: [$id]: $message", E_USER_WARNING);
	}
	
}
