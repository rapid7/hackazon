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
 * Most of the work of the {@link LoggerPatternLayout} class 
 * is delegated to the {@link LoggerPatternParser} class.
 * 
 * <p>It is this class that parses conversion patterns and creates
 * a chained list of {@link LoggerPatternConverter} converters.</p>
 * 
 * @version $Revision$ 
 * @package log4php
 * @subpackage helpers
 *
 * @since 0.3
 */
class LoggerPatternParser {

	/** Escape character for conversion words in the conversion pattern. */
	const ESCAPE_CHAR = '%';
	
	/** Maps conversion words to relevant converters. */
	private $converterMap;
	
	/** Conversion pattern used in layout. */
	private $pattern;
	
	/** Regex pattern used for parsing the conversion pattern. */
	private $regex;
	
	/** 
	 * First converter in the chain. 
	 * @var LoggerPatternConverter
	 */
	private $head;
	
	/** Last converter in the chain. */
	private $tail;
	
	public function __construct($pattern, $converterMap) {
		$this->pattern = $pattern;
		$this->converterMap = $converterMap;
		
		// Construct the regex pattern
		$this->regex = 
			'/' .                       // Starting regex pattern delimiter
			self::ESCAPE_CHAR .         // Character which marks the start of the conversion pattern
			'(?P<modifiers>[0-9.-]*)' . // Format modifiers (optional)
			'(?P<word>[a-zA-Z]+)' .     // The conversion word
			'(?P<option>{[^}]*})?' .    // Conversion option in braces (optional)
			'/';                        // Ending regex pattern delimiter
	}
	
	/** 
	 * Parses the conversion pattern string, converts it to a chain of pattern
	 * converters and returns the first converter in the chain.
	 * 
	 * @return LoggerPatternConverter
	 */
	public function parse() {
		
		// Skip parsing if the pattern is empty
		if (empty($this->pattern)) {
			$this->addLiteral('');
			return $this->head;
		}
		
		// Find all conversion words in the conversion pattern
		$count = preg_match_all($this->regex, $this->pattern, $matches, PREG_OFFSET_CAPTURE);
		if ($count === false) {
			$error = error_get_last();
			throw new LoggerException("Failed parsing layotut pattern: {$error['message']}");
		}
		
		$prevEnd = 0;
		
		foreach($matches[0] as $key => $item) {
			
			// Locate where the conversion command starts and ends
			$length = strlen($item[0]);
			$start = $item[1];
			$end = $item[1] + $length;
		
			// Find any literal expressions between matched commands
			if ($start > $prevEnd) {
				$literal = substr($this->pattern, $prevEnd, $start - $prevEnd);
				$this->addLiteral($literal);
			}
			
			// Extract the data from the matched command
			$word = !empty($matches['word'][$key]) ? $matches['word'][$key][0] : null;
			$modifiers = !empty($matches['modifiers'][$key]) ? $matches['modifiers'][$key][0] : null;
			$option = !empty($matches['option'][$key]) ? $matches['option'][$key][0] : null;
			
			// Create a converter and add it to the chain
			$this->addConverter($word, $modifiers, $option);
			
			$prevEnd = $end;
		}

		// Add any trailing literals
		if ($end < strlen($this->pattern)) {
			$literal = substr($this->pattern, $end);
			$this->addLiteral($literal);
		}
		
		return $this->head;
	}
	
	/** 
	 * Adds a literal converter to the converter chain. 
	 * @param string $string The string for the literal converter.
	 */
	private function addLiteral($string) {
		$converter = new LoggerPatternConverterLiteral($string);
		$this->addToChain($converter);
	}
	
	/**
	 * Adds a non-literal converter to the converter chain.
	 * 
	 * @param string $word The conversion word, used to determine which 
	 *  converter will be used.
	 * @param string $modifiers Formatting modifiers.
	 * @param string $option Option to pass to the converter.
	 */
	private function addConverter($word, $modifiers, $option) {
 		$formattingInfo = $this->parseModifiers($modifiers);
		$option = trim($option, "{} ");
		
		if (isset($this->converterMap[$word])) {
			$converter = $this->getConverter($word, $formattingInfo, $option);
			$this->addToChain($converter);	
		} else {
			trigger_error("log4php: Invalid keyword '%$word' in converison pattern. Ignoring keyword.", E_USER_WARNING);
		}
	}
	
	/**
	 * Determines which converter to use based on the conversion word. Creates 
	 * an instance of the converter using the provided formatting info and 
	 * option and returns it.
	 * 
	 * @param string $word The conversion word.
	 * @param LoggerFormattingInfo $info Formatting info.
	 * @param string $option Converter option.
	 * 
	 * @throws LoggerException 
	 * 
	 * @return LoggerPatternConverter
	 */
	private function getConverter($word, $info, $option) {
		if (!isset($this->converterMap[$word])) {
			throw new LoggerException("Invalid keyword '%$word' in converison pattern. Ignoring keyword.");
		}
		
		$converterClass = $this->converterMap[$word];
		if(!class_exists($converterClass)) {
			throw new LoggerException("Class '$converterClass' does not exist.");
		}
		
		$converter = new $converterClass($info, $option);
		if(!($converter instanceof LoggerPatternConverter)) {
			throw new LoggerException("Class '$converterClass' is not an instance of LoggerPatternConverter.");
		}
		
		return $converter;
	}
	
	/** Adds a converter to the chain and updates $head and $tail pointers. */
	private function addToChain(LoggerPatternConverter $converter) {
		if (!isset($this->head)) {
			$this->head = $converter;
			$this->tail = $this->head;
		} else {
			$this->tail->next = $converter;
			$this->tail = $this->tail->next;
		}
	}
	
	/**
	 * Parses the formatting modifiers and produces the corresponding 
	 * LoggerFormattingInfo object.
	 * 
	 * @param string $modifier
	 * @return LoggerFormattingInfo
	 * @throws LoggerException
	 */
	private function parseModifiers($modifiers) {
		$info = new LoggerFormattingInfo();
	
		// If no modifiers are given, return default values
		if (empty($modifiers)) {
			return $info;
		}
	
		// Validate
		$pattern = '/^(-?[0-9]+)?\.?-?[0-9]+$/';
		if (!preg_match($pattern, $modifiers)) {
			trigger_error("log4php: Invalid modifier in conversion pattern: [$modifiers]. Ignoring modifier.", E_USER_WARNING);
			return $info;
		}
	
		$parts = explode('.', $modifiers);
	
		if (!empty($parts[0])) {
			$minPart = (integer) $parts[0];
			$info->min = abs($minPart);
			$info->padLeft = ($minPart > 0);
		}
	
		if (!empty($parts[1])) {
			$maxPart = (integer) $parts[1];
			$info->max = abs($maxPart);
			$info->trimLeft = ($maxPart < 0);
		}
	
		return $info;
	}
}
