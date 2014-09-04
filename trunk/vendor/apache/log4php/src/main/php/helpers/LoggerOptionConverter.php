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
 * A convenience class to convert property values to specific types.
 *
 * @version $Revision$ 
 * @package log4php
 * @subpackage helpers
 * @since 0.5
 */
class LoggerOptionConverter {
	
	/** String values which are converted to boolean TRUE. */
	private static $trueValues = array('1', 'true', 'yes', 'on');
	
	/** 
	 * String values which are converted to boolean FALSE.
	 * 
	 * Note that an empty string must convert to false, because 
	 * parse_ini_file() which is used for parsing configuration 
	 * converts the value _false_ to an empty string.
	 */
	private static $falseValues = array('0', 'false', 'no', 'off', '');
	
	/**
	 * Read a predefined var.
	 *
	 * It returns a value referenced by <var>$key</var> using this search criteria:
	 * - if <var>$key</var> is a constant then return it. Else
	 * - if <var>$key</var> is set in <var>$_ENV</var> then return it. Else
	 * - return <var>$def</var>. 
	 *
	 * @param string $key The key to search for.
	 * @param string $def The default value to return.
	 * @return string	the string value of the system property, or the default
	 *					value if there is no property with that key.
	 */
	public static function getSystemProperty($key, $def) {
		if(defined($key)) {
			return (string)constant($key);
		} else if(isset($_SERVER[$key])) {
			return (string)$_SERVER[$key];
		} else if(isset($_ENV[$key])) {
			return (string)$_ENV[$key];
		} else {
			return $def;
		}
	}

	/** Converts $value to boolean, or throws an exception if not possible. */
	public static function toBooleanEx($value) {
		if (isset($value)) {
			if (is_bool($value)) {
				return $value;
			}
			$value = strtolower(trim($value));
			if (in_array($value, self::$trueValues)) {
				return true;
			}
			if (in_array($value, self::$falseValues)) {
				return false;
			}
		}
		
		throw new LoggerException("Given value [" . var_export($value, true) . "] cannot be converted to boolean.");
	}
	
	/** 
	 * Converts $value to integer, or throws an exception if not possible. 
	 * Floats cannot be converted to integer.
	 */
	public static function toIntegerEx($value) {
		if (is_integer($value)) {
			return $value;
		}
		if (is_numeric($value) && ($value == (integer) $value)) {
			return (integer) $value;
		}
	
		throw new LoggerException("Given value [" . var_export($value, true) . "] cannot be converted to integer.");
	}
	
	/**
	 * Converts $value to integer, or throws an exception if not possible.
	 * Floats cannot be converted to integer.
	 */
	public static function toPositiveIntegerEx($value) {
		if (is_integer($value) && $value > 0) {
			return $value;
		}
		if (is_numeric($value) && ($value == (integer) $value) && $value > 0) {
			return (integer) $value;
		}
	
		throw new LoggerException("Given value [" . var_export($value, true) . "] cannot be converted to a positive integer.");
	}

	/** Converts the value to a level. Throws an exception if not possible. */
	public static function toLevelEx($value) {
		if ($value instanceof LoggerLevel) {
			return $value;
		}
		$level = LoggerLevel::toLevel($value);
		if ($level === null) {
			throw new LoggerException("Given value [" . var_export($value, true) . "] cannot be converted to a logger level.");
		}
		return $level;
	}

	/**
	 * Converts a value to a valid file size (integer).
	 * 
	 * Supports 'KB', 'MB' and 'GB' suffixes, where KB = 1024 B etc. 
	 *
	 * The final value will be rounded to the nearest integer.
	 *
	 * Examples:
	 * - '100' => 100
	 * - '100.12' => 100
	 * - '100KB' => 102400
	 * - '1.5MB' => 1572864
	 * 
	 * @param mixed $value File size (optionally with suffix).
	 * @return integer Parsed file size.
	 */
	public static function toFileSizeEx($value) {
		
		if (empty($value)) {
			throw new LoggerException("Empty value cannot be converted to a file size.");
		}
		
		if (is_numeric($value)) {
			return (integer) $value;
		}
		
		if (!is_string($value)) {
			throw new LoggerException("Given value [" . var_export($value, true) . "] cannot be converted to a file size.");
		}
		
		$str = strtoupper(trim($value));
		$count = preg_match('/^([0-9.]+)(KB|MB|GB)?$/', $str, $matches);
		
		if ($count > 0) {
			$size = $matches[1];
			$unit = $matches[2];
			
			switch($unit) {
				case 'KB': $size *= pow(1024, 1); break;
				case 'MB': $size *= pow(1024, 2); break;
				case 'GB': $size *= pow(1024, 3); break;
			}
			
			return (integer) $size;
		}
		
		throw new LoggerException("Given value [$value] cannot be converted to a file size.");
	}

	/** 
	 * Converts a value to string, or throws an exception if not possible. 
	 * 
	 * Objects can be converted to string if they implement the magic 
	 * __toString() method.
	 * 
	 */
	public static function toStringEx($value) {
		if (is_string($value)) {
			return $value;
		}
		if (is_numeric($value)) {
			return (string) $value;
		}
		if (is_object($value) && method_exists($value, '__toString')) {
			return (string) $value;
		}
	
		throw new LoggerException("Given value [" . var_export($value, true) . "] cannot be converted to string.");
	}
	
	/**
	 * Performs value substitution for string options.
	 * 
	 * An option can contain PHP constants delimited by '${' and '}'.
	 * 
	 * E.g. for input string "some ${FOO} value", the method will attempt 
	 * to substitute ${FOO} with the value of constant FOO if it exists.
	 * 
	 * Therefore, if FOO is a constant, and it has value "bar", the resulting 
	 * string will be "some bar value". 
	 * 
	 * If the constant is not defined, it will be replaced by an empty string, 
	 * and the resulting string will be "some  value". 
	 * 
	 * @param string $string String on which to perform substitution.
	 * @return string
	 */
	public static function substConstants($string) {
		preg_match_all('/\${([^}]+)}/', $string, $matches);
		
		foreach($matches[1] as $key => $match) {
			$match = trim($match);
			$search = $matches[0][$key];
			$replacement = defined($match) ? constant($match) : '';
			$string = str_replace($search, $replacement, $string);
		}
		return $string;
	}
}
