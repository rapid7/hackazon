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
 * The LoggerMDC class provides _mapped diagnostic contexts_.
 * 
 * A Mapped Diagnostic Context, or MDC in short, is an instrument for 
 * distinguishing interleaved log output from different sources. Log output 
 * is typically interleaved when a server handles multiple clients 
 * near-simultaneously.
 * 
 * This class is similar to the {@link LoggerNDC} class except that 
 * it is based on a map instead of a stack.
 * 
 * @version $Revision$
 * @since 0.3
 * @package log4php
 */
class LoggerMDC {
	
	/** Holds the context map. */
	private static $map = array();
		
	/**
	 * Stores a context value as identified with the key parameter into the 
	 * context map.
	 *
	 * @param string $key the key
	 * @param string $value the value
	 */
	public static function put($key, $value) {
		self::$map[$key] = $value;
	}
  
	/**
	 * Returns the context value identified by the key parameter.
	 *
	 * @param string $key The key.
	 * @return string The context or an empty string if no context found
	 * 	for given key.
	 */
	public static function get($key) {
		return isset(self::$map[$key]) ? self::$map[$key] : '';
	}

	/**
	 * Returns the contex map as an array.
	 * @return array The MDC context map.
	 */
	public static function getMap() {
		return self::$map;
	}

	/**
	 * Removes the the context identified by the key parameter. 
	 *
	 * Only affects user mappings, not $_ENV or $_SERVER.
	 *
	 * @param string $key The key to be removed.
	 */
	public static function remove($key) {
		unset(self::$map[$key]);
	}
	
	/**
	 * Clears the mapped diagnostic context.
	 */
	public static function clear() {
		self::$map = array();
	}
}
