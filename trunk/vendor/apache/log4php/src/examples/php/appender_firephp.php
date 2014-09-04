<?php
/**
 * Appender_Firephp example. Copy this file into your DOCUMENT_ROOT
 *
 * Licensed to the Apache Software Foundation (ASF) under one or more contributor
 * license agreements. See the NOTICE file distributed with this work for
 * additional information regarding copyright ownership. The ASF licenses this
 * file to you under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *      http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software distributed
 * under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
 * CONDITIONS OF ANY KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations under the License.
 *
 * PHP version 5
 *
 * @category  Example
 * @package   LoggerAppenderFirephp
 * @author    Bruce Ingalls <Bruce.Ingalls-at-gmail-dot-com>
 * @copyright 2012 Apache Software Foundation
 * @license   Apache License, Version 2.0
 * @version   SVN: $Id:$
 * @link      http://sourcemint.com/github.com/firephp/firephp/1:1.0.0b1rc6/-docs/Configuration/Constants
 * @link      https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/FirePHPHandler.php
 * @since     Feb 22, 2012
 * @internal  CodeSniffs as PEAR, adapted to Apache style. Phpmd clean.
 */

//Change next line to URL path following domain. I.e. chop off 'http://localhost'
define('INSIGHT_SERVER_PATH', $_SERVER['REQUEST_URI']);
//define('INSIGHT_SERVER_PATH', $_SERVER['SCRIPT_NAME']);
define('INSIGHT_DEBUG', true);  //Comment, after config is debugged, to remove 'Flushing headers'
define('INSIGHT_IPS', '*');		//Your IP here for extra security
//Works, but replace next line with free key from Developer Companion, for security on live sites
define('INSIGHT_AUTHKEYS', '*');
define('INSIGHT_PATHS', dirname(__FILE__));

//EDIT YOUR FirePHP LOCATION HERE
// If using ZIP Archive
//TODO: Add 'lib/' of extracted archive to include path
require_once 'FirePHP/Init.php';	//Must be declared before log4php

// If using PHAR Archive (php 5.3+)
//require_once('phar://.../firephp.phar/FirePHP/Init.php');
// TODO: Replace ----^^^



require_once dirname(__FILE__).'/../../main/php/Logger.php';

Logger::configure(dirname(__FILE__).'/../resources/appender_firephp.xml');

?>
<!-- RUN THIS FROM WEB DOCUMENT_ROOT (~/public_html/ or /var/www/) -->

<h1>FirePHP appender test &amp; configuration</h1>
<h2>Requirements</h2>
<ul>
	<li>
		<a href="http://logging.apache.org/log4php/">Apache log4php</a>
		&gt;= v2.2.2
	</li>
	<li>
		<a href="http://getfirebug.com/">Mozilla Firebug</a> with console &amp; net enabled.
	</li>
	<li>
		<a href="http://sourcemint.com/github.com/firephp/firephp/1:1.0.0b1rc6/-docs/Welcome"
		   >FirePHP >= 1.0</a> (beta, as of March 2012) server lib &amp; Firefox plugin.
		   This one is also referred to as <i>Insight</i> or <i>Developer's Companion</i>
	</li>
</ul>

<h2>Untested (or not supported)</h2>
<ul>
	<li>Old versions of Mozilla Firefox</li>
	<li>
		Versions of FirePHP prior to <b>v1.0beta</b>!
		Currently, this is the default at addons.mozilla.org !
	</li>
	<li>
		<a href="https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/FirePHPHandler.php"
		   >Monolog</a>
	</li>
	<li>*FirePHP* for Google Chrome</li>
	<li>No other browser currently has a similar plugin</li>
</ul>

<h2>Instructions</h2>
<ul>
	<li>Install the requirements above, following their instructions</li>
	<li>If you installed the phar package, edit its location at the top of this file</li>
	<li>Ensure this file has web server read permissions in <code>DOCUMENT_ROOT</code></li>
	<li>
		Copy <code>appender_firephp.xml</code> to log4php.xml into your DOCUMENT_ROOT
		<small>(Note that log4php.xml runs LogggerAppenderFirephp at debug level)</small>
	</li>
	<li>Optional: launch Developer Companion. Follow its instructions to generate a key</li>
	<li>Open the Firebug console (window), and enable <i>Console</i> &amp; <i>Net</i></li>
	<li>Reload Firefox</li>
	<li>
		If the greeting in Firebug console displays with problems, click on it,
		to see a stack trace
	</li>
	<li>
		Comment out <b><code>define('INSIGHT_DEBUG', true);</code></b> at the top of
		this file, to disable the notice:
		<small style="border: 2px solid black; background-color: red;">
			<span style="font-weight: bold;">[INSIGHT]</span> Flushing headers
		</small>
	</li>
</ul>

<h2>If you see a greeting in Firebug, you can now return to work!</h2>

<?php
$log = Logger::getLogger('FirePhp_Example_Logger_Name');
$log->debug('Congrats! Enjoy log4php with FirePHP!');

