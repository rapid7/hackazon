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
 * LoggerPatternConverter is an abstract class that provides the formatting 
 * functionality that derived classes need.
 * 
 * <p>Conversion specifiers in a conversion patterns are parsed to
 * individual PatternConverters. Each of which is responsible for
 * converting a logging event in a converter specific manner.</p>
 * 
 * @version $Revision$
 * @package log4php
 * @subpackage helpers
 * @since 0.3
 */
abstract class LoggerPatternConverter {
	
	/**
	 * Next converter in the converter chain.
	 * @var LoggerPatternConverter 
	 */
	public $next = null;
	
	/**
	 * Formatting information, parsed from pattern modifiers. 
	 * @var LoggerFormattingInfo
	 */
	protected $formattingInfo;
	
	/**
	 * Converter-specific formatting options.
	 * @var array
	 */
	protected $option;

	/**
	 * Constructor 
	 * @param LoggerFormattingInfo $formattingInfo
	 * @param array $option
	 */
	public function __construct(LoggerFormattingInfo $formattingInfo = null, $option = null) {  
		$this->formattingInfo = $formattingInfo;
		$this->option = $option;
		$this->activateOptions();
	}
	
	/**
	 * Called in constructor. Converters which need to process the options 
	 * can override this method. 
	 */
	public function activateOptions() { }
  
	/**
	 * Converts the logging event to the desired format. Derived pattern 
	 * converters must implement this method.
	 *
	 * @param LoggerLoggingEvent $event
	 */
	abstract public function convert(LoggerLoggingEvent $event);

	/**
	 * Converts the event and formats it according to setting in the 
	 * Formatting information object.
	 *
	 * @param string &$sbuf string buffer to write to
	 * @param LoggerLoggingEvent $event Event to be formatted.
	 */
	public function format(&$sbuf, $event) {
		$string = $this->convert($event);
		
		if (!isset($this->formattingInfo)) {
			$sbuf .= $string;
			return;	
		}
		
		$fi = $this->formattingInfo;
		
		// Empty string
		if($string === '' || is_null($string)) {
			if($fi->min > 0) {
				$sbuf .= str_repeat(' ', $fi->min);
			}
			return;
		}
		
		$len = strlen($string);
	
		// Trim the string if needed
		if($len > $fi->max) {
			if ($fi->trimLeft) {
				$sbuf .= substr($string, $len - $fi->max, $fi->max);
			} else {
				$sbuf .= substr($string , 0, $fi->max);
			}
		}
		
		// Add padding if needed
		else if($len < $fi->min) {
			if($fi->padLeft) {
				$sbuf .= str_repeat(' ', $fi->min - $len);
				$sbuf .= $string;
			} else {
				$sbuf .= $string;
				$sbuf .= str_repeat(' ', $fi->min - $len);
			}
		}
		
		// No action needed
		else {
			$sbuf .= $string;
		}
	}
}
