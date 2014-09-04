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
 * Manages defined renderers and determines which renderer to use for a given 
 * input. 
 *
 * @version $Revision$
 * @package log4php
 * @subpackage renderers
 * @since 0.3
 */
class LoggerRendererMap {

	/**
	 * Maps class names to appropriate renderers.
	 * @var array
	 */
	private $map = array();

	/**
	 * The default renderer to use if no specific renderer is found. 
	 * @var LoggerRenderer
	 */
	private $defaultRenderer;
	
	public function __construct() {
		
		// Set default config
		$this->reset();
	}

	/**
	 * Adds a renderer to the map.
	 * 
	 * If a renderer already exists for the given <var>$renderedClass</var> it 
	 * will be overwritten without warning.
	 *
	 * @param string $renderedClass The name of the class which will be 
	 * 		rendered by the renderer.
	 * @param string $renderingClass The name of the class which will 
	 * 		perform the rendering.
	 */
	public function addRenderer($renderedClass, $renderingClass) {
		// Check the rendering class exists
		if (!class_exists($renderingClass)) {
			trigger_error("log4php: Failed adding renderer. Rendering class [$renderingClass] not found.");
			return;
		}
		
		// Create the instance
		$renderer = new $renderingClass();
		
		// Check the class implements the right interface
		if (!($renderer instanceof LoggerRenderer)) {
			trigger_error("log4php: Failed adding renderer. Rendering class [$renderingClass] does not implement the LoggerRenderer interface.");
			return;
		}
		
		// Convert to lowercase since class names in PHP are not case sensitive
		$renderedClass = strtolower($renderedClass);
		
		$this->map[$renderedClass] = $renderer;
	}
	
	/**
	 * Sets a custom default renderer class.
	 * 
	 * TODO: there's code duplication here. This method is almost identical to 
	 * addRenderer(). However, it has custom error messages so let it sit for 
	 * now.
	 *
	 * @param string $renderingClass The name of the class which will 
	 * 		perform the rendering.
	 */
	public function setDefaultRenderer($renderingClass) {
		// Check the class exists
		if (!class_exists($renderingClass)) {
			trigger_error("log4php: Failed setting default renderer. Rendering class [$renderingClass] not found.");
			return;
		}
		
		// Create the instance
		$renderer = new $renderingClass();
		
		// Check the class implements the right interface
		if (!($renderer instanceof LoggerRenderer)) {
			trigger_error("log4php: Failed setting default renderer. Rendering class [$renderingClass] does not implement the LoggerRenderer interface.");
			return;
		}
		
		$this->defaultRenderer = $renderer;
	}
	
	/**
	 * Returns the default renderer.
	 * @var LoggerRenderer
	 */
	public function getDefaultRenderer() {
		return $this->defaultRenderer;
	}
	
	/**
	 * Finds the appropriate renderer for the given <var>input</var>, and 
	 * renders it (i.e. converts it to a string). 
	 *
	 * @param mixed $input Input to render.
	 * @return string The rendered contents.
	 */
	public function findAndRender($input) {
		if ($input === null) {
			return null;
		}
		
		// For objects, try to find a renderer in the map
		if(is_object($input)) {
			$renderer = $this->getByClassName(get_class($input));
			if (isset($renderer)) {
				return $renderer->render($input);
			}
		}
		
		// Fall back to the default renderer
		return $this->defaultRenderer->render($input);
	}

	/**
	 * Returns the appropriate renderer for a given object.
	 * 
	 * @param mixed $object
	 * @return LoggerRenderer Or null if none found.
	 */
	public function getByObject($object) {
		if (!is_object($object)) {
			return null;
		}
		return $this->getByClassName(get_class($object));
	}
	
	/**
	 * Returns the appropriate renderer for a given class name.
	 * 
	 * If no renderer could be found, returns NULL.
	 *
	 * @param string $class
	 * @return LoggerRendererObject Or null if not found.
	 */
	public function getByClassName($class) {
		for(; !empty($class); $class = get_parent_class($class)) {
			$class = strtolower($class);
			if(isset($this->map[$class])) {
				return $this->map[$class];
			}
		}
		return null;
	}

	/** Empties the renderer map. */
	public function clear() {
		$this->map = array();
	}
	
	/** Resets the renderer map to it's default configuration. */
	public function reset() {
		$this->defaultRenderer = new LoggerRendererDefault();
		$this->clear();
		$this->addRenderer('Exception', 'LoggerRendererException');
	}
}
