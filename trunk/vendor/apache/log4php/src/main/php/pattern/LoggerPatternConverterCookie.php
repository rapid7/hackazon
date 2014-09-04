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
 * Returns a value from the $_COOKIE superglobal array corresponding to the 
 * given key. If no key is given, return all values.
 * 
 * Options:
 *  [0] $_COOKIE key value
 * 
 * @package log4php
 * @subpackage pattern
 * @version $Revision$
 * @since 2.3
 */
class LoggerPatternConverterCookie extends LoggerPatternConverterSuperglobal {
	protected $name = '_COOKIE';
}