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
 * This class encapsulates the information obtained when parsing
 * formatting modifiers in conversion modifiers.
 * 
 * @package log4php
 * @subpackage helpers
 * @since 0.3
 */
class LoggerFormattingInfo {
	
	/** 
	 * Minimal output length. If output is shorter than this value, it will be
	 * padded with spaces. 
	 */
	public $min = 0;
	
	/** 
	 * Maximum output length. If output is longer than this value, it will be 
	 * trimmed.
	 */
	public $max = PHP_INT_MAX;
	
	/**
	 * Whether to pad the string from the left. If set to false, the string 
	 * will be padded from the right. 
	 */
	public $padLeft = true;
	
	/**
	 * Whether to trim the string from the left. If set to false, the string
	 * will be trimmed from the right.
	 */
	public $trimLeft = false;
}
