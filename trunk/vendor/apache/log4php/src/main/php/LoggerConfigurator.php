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
 * Interface for logger configurators.
 * 
 * @package log4php
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version $Revision$
 * @since 2.2
 */
interface LoggerConfigurator
{
	/**
	 * Configures log4php based on the given configuration. 
	 * 
	 * All configurators implementations must implement this interface.
	 * 
	 * @param LoggerHierarchy $hierarchy The hierarchy on which to perform 
	 * 		the configuration. 
	 * @param mixed $input Either path to the config file or the 
	 * 		configuration as an array.
	 */
	public function configure(LoggerHierarchy $hierarchy, $input = null);
}