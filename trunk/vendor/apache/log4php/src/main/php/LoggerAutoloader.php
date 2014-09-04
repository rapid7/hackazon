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

if (function_exists('__autoload')) {
	trigger_error("log4php: It looks like your code is using an __autoload() function. log4php uses spl_autoload_register() which will bypass your __autoload() function and may break autoloading.", E_USER_WARNING);
}

spl_autoload_register(array('LoggerAutoloader', 'autoload'));

/**
 * Class autoloader.
 * 
 * @package log4php
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version $Revision$
 */
class LoggerAutoloader {
	
	/** Maps classnames to files containing the class. */
	private static $classes = array(
	
		// Base
		'LoggerAppender' => '/LoggerAppender.php',
		'LoggerAppenderPool' => '/LoggerAppenderPool.php',
		'LoggerConfigurable' => '/LoggerConfigurable.php',
		'LoggerConfigurator' => '/LoggerConfigurator.php',
		'LoggerException' => '/LoggerException.php',
		'LoggerFilter' => '/LoggerFilter.php',
		'LoggerHierarchy' => '/LoggerHierarchy.php',
		'LoggerLevel' => '/LoggerLevel.php',
		'LoggerLocationInfo' => '/LoggerLocationInfo.php',
		'LoggerLoggingEvent' => '/LoggerLoggingEvent.php',
		'LoggerMDC' => '/LoggerMDC.php',
		'LoggerNDC' => '/LoggerNDC.php',
		'LoggerLayout' => '/LoggerLayout.php',
		'LoggerReflectionUtils' => '/LoggerReflectionUtils.php',
		'LoggerRoot' => '/LoggerRoot.php',
		'LoggerThrowableInformation' => '/LoggerThrowableInformation.php',
		
		// Appenders
		'LoggerAppenderConsole' => '/appenders/LoggerAppenderConsole.php',
		'LoggerAppenderDailyFile' => '/appenders/LoggerAppenderDailyFile.php',
		'LoggerAppenderEcho' => '/appenders/LoggerAppenderEcho.php',
		'LoggerAppenderFile' => '/appenders/LoggerAppenderFile.php',
		'LoggerAppenderMail' => '/appenders/LoggerAppenderMail.php',
		'LoggerAppenderMailEvent' => '/appenders/LoggerAppenderMailEvent.php',
		'LoggerAppenderMongoDB' => '/appenders/LoggerAppenderMongoDB.php',
		'LoggerAppenderNull' => '/appenders/LoggerAppenderNull.php',
		'LoggerAppenderFirePHP' => '/appenders/LoggerAppenderFirePHP.php',
		'LoggerAppenderPDO' => '/appenders/LoggerAppenderPDO.php',
		'LoggerAppenderPhp' => '/appenders/LoggerAppenderPhp.php',
		'LoggerAppenderRollingFile' => '/appenders/LoggerAppenderRollingFile.php',
		'LoggerAppenderSocket' => '/appenders/LoggerAppenderSocket.php',
		'LoggerAppenderSyslog' => '/appenders/LoggerAppenderSyslog.php',
		
		// Configurators
		'LoggerConfigurationAdapter' => '/configurators/LoggerConfigurationAdapter.php',
		'LoggerConfigurationAdapterINI' => '/configurators/LoggerConfigurationAdapterINI.php',
		'LoggerConfigurationAdapterPHP' => '/configurators/LoggerConfigurationAdapterPHP.php',
		'LoggerConfigurationAdapterXML' => '/configurators/LoggerConfigurationAdapterXML.php',
		'LoggerConfiguratorDefault' => '/configurators/LoggerConfiguratorDefault.php',

		// Filters
		'LoggerFilterDenyAll' => '/filters/LoggerFilterDenyAll.php',
		'LoggerFilterLevelMatch' => '/filters/LoggerFilterLevelMatch.php',
		'LoggerFilterLevelRange' => '/filters/LoggerFilterLevelRange.php',
		'LoggerFilterStringMatch' => '/filters/LoggerFilterStringMatch.php',

		// Helpers
		'LoggerFormattingInfo' => '/helpers/LoggerFormattingInfo.php',
		'LoggerOptionConverter' => '/helpers/LoggerOptionConverter.php',
		'LoggerPatternParser' => '/helpers/LoggerPatternParser.php',
		'LoggerUtils' => '/helpers/LoggerUtils.php',
	
		// Pattern converters
		'LoggerPatternConverter' => '/pattern/LoggerPatternConverter.php',
		'LoggerPatternConverterClass' => '/pattern/LoggerPatternConverterClass.php',
		'LoggerPatternConverterCookie' => '/pattern/LoggerPatternConverterCookie.php',
		'LoggerPatternConverterDate' => '/pattern/LoggerPatternConverterDate.php',
		'LoggerPatternConverterEnvironment' => '/pattern/LoggerPatternConverterEnvironment.php',
		'LoggerPatternConverterFile' => '/pattern/LoggerPatternConverterFile.php',
		'LoggerPatternConverterLevel' => '/pattern/LoggerPatternConverterLevel.php',
		'LoggerPatternConverterLine' => '/pattern/LoggerPatternConverterLine.php',
		'LoggerPatternConverterLiteral' => '/pattern/LoggerPatternConverterLiteral.php',
		'LoggerPatternConverterLocation' => '/pattern/LoggerPatternConverterLocation.php',
		'LoggerPatternConverterLogger' => '/pattern/LoggerPatternConverterLogger.php',
		'LoggerPatternConverterMDC' => '/pattern/LoggerPatternConverterMDC.php',
		'LoggerPatternConverterMessage' => '/pattern/LoggerPatternConverterMessage.php',
		'LoggerPatternConverterMethod' => '/pattern/LoggerPatternConverterMethod.php',
		'LoggerPatternConverterNDC' => '/pattern/LoggerPatternConverterNDC.php',
		'LoggerPatternConverterNewLine' => '/pattern/LoggerPatternConverterNewLine.php',
		'LoggerPatternConverterProcess' => '/pattern/LoggerPatternConverterProcess.php',
		'LoggerPatternConverterRelative' => '/pattern/LoggerPatternConverterRelative.php',
		'LoggerPatternConverterRequest' => '/pattern/LoggerPatternConverterRequest.php',
		'LoggerPatternConverterServer' => '/pattern/LoggerPatternConverterServer.php',
		'LoggerPatternConverterSession' => '/pattern/LoggerPatternConverterSession.php',
		'LoggerPatternConverterSessionID' => '/pattern/LoggerPatternConverterSessionID.php',
		'LoggerPatternConverterSuperglobal' => '/pattern/LoggerPatternConverterSuperglobal.php',
		'LoggerPatternConverterThrowable' => '/pattern/LoggerPatternConverterThrowable.php',
		
		// Layouts
		'LoggerLayoutHtml' => '/layouts/LoggerLayoutHtml.php',
		'LoggerLayoutPattern' => '/layouts/LoggerLayoutPattern.php',
		'LoggerLayoutSerialized' => '/layouts/LoggerLayoutSerialized.php',
		'LoggerLayoutSimple' => '/layouts/LoggerLayoutSimple.php',
		'LoggerLayoutTTCC' => '/layouts/LoggerLayoutTTCC.php',
		'LoggerLayoutXml' => '/layouts/LoggerLayoutXml.php',
		
		// Renderers
		'LoggerRendererDefault' => '/renderers/LoggerRendererDefault.php',
		'LoggerRendererException' => '/renderers/LoggerRendererException.php',
		'LoggerRendererMap' => '/renderers/LoggerRendererMap.php',
		'LoggerRenderer' => '/renderers/LoggerRenderer.php',
	);
	
	/**
	 * Loads a class.
	 * @param string $className The name of the class to load.
	 */
	public static function autoload($className) {
		if(isset(self::$classes[$className])) {
			include dirname(__FILE__) . self::$classes[$className];
		}
	}
}
