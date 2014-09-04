<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
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
 * The output of the LoggerXmlLayout consists of a series of log4php:event elements. 
 * 
 * Configurable parameters: 
 * - {@link $locationInfo} - If set to true then the file name and line number 
 *   of the origin of the log statement will be included in output.
 * - {@link $log4jNamespace} - If set to true then log4j namespace will be used
 *   instead of log4php namespace. This can be usefull when using log viewers 
 *   which can only parse the log4j namespace such as Apache Chainsaw. 
 * 
 * <p>It does not output a complete well-formed XML file. 
 * The output is designed to be included as an external entity in a separate file to form
 * a correct XML file.</p>
 * 
 * Example:
 * 
 * {@example ../../examples/php/layout_xml.php 19}<br>
 * 
 * {@example ../../examples/resources/layout_xml.properties 18}<br>
 *
 * The above would print:
 * 
 * <pre>
 * <log4php:eventSet xmlns:log4php="http://logging.apache.org/log4php/" version="0.3" includesLocationInfo="true">
 * 	<log4php:event logger="root" level="INFO" thread="13802" timestamp="1252456226491">
 * 		<log4php:message><![CDATA[Hello World!]]></log4php:message>
 * 		<log4php:locationInfo class="main" file="examples/php/layout_xml.php" line="6" method="main" />
 * 	</log4php:event>
 * </log4php:eventSet>
 * </pre>
 *
 * @version $Revision$
 * @package log4php
 * @subpackage layouts
 */
class LoggerLayoutXml extends LoggerLayout {
	const LOG4J_NS_PREFIX ='log4j';
	const LOG4J_NS = 'http://jakarta.apache.org/log4j/';
	
	const LOG4PHP_NS_PREFIX = 'log4php';
	const LOG4PHP_NS = 'http://logging.apache.org/log4php/';
	
	const CDATA_START = '<![CDATA[';
	const CDATA_END = ']]>';
	const CDATA_PSEUDO_END = ']]&gt;';
	const CDATA_EMBEDDED_END = ']]>]]&gt;<![CDATA[';

	/**
	 * If set to true then the file name and line number of the origin of the
	 * log statement will be output.
	 * @var boolean
	 */
	protected $locationInfo = true;
  
	/**
	 * If set to true, log4j namespace will be used instead of the log4php 
	 * namespace.
	 * @var boolean 
	 */
	protected $log4jNamespace = false;
	
	/** The namespace in use. */
	protected $namespace = self::LOG4PHP_NS;
	
	/** The namespace prefix in use */
	protected $namespacePrefix = self::LOG4PHP_NS_PREFIX;
	 
	public function activateOptions() {
		if ($this->getLog4jNamespace()) {
			$this->namespace        = self::LOG4J_NS;
			$this->namespacePrefix  = self::LOG4J_NS_PREFIX;
		} else {
			$this->namespace        = self::LOG4PHP_NS;
			$this->namespacePrefix  = self::LOG4PHP_NS_PREFIX;
		}
	}
	
	/**
	 * @return string
	 */
	public function getHeader() {
		return "<{$this->namespacePrefix}:eventSet ".
			"xmlns:{$this->namespacePrefix}=\"{$this->namespace}\" ".
			"version=\"0.3\" ".
			"includesLocationInfo=\"".($this->getLocationInfo() ? "true" : "false")."\"".
			">" . PHP_EOL;
	}

	/**
	 * Formats a {@link LoggerLoggingEvent} in conformance with the log4php.dtd.
	 *
	 * @param LoggerLoggingEvent $event
	 * @return string
	 */
	public function format(LoggerLoggingEvent $event) {
		$ns = $this->namespacePrefix;
		
		$loggerName = $event->getLoggerName();
		$timeStamp = number_format((float)($event->getTimeStamp() * 1000), 0, '', '');
		$thread = $event->getThreadName();
		$level = $event->getLevel()->toString();

		$buf  = "<$ns:event logger=\"{$loggerName}\" level=\"{$level}\" thread=\"{$thread}\" timestamp=\"{$timeStamp}\">".PHP_EOL;
		$buf .= "<$ns:message>"; 
		$buf .= $this->encodeCDATA($event->getRenderedMessage()); 
		$buf .= "</$ns:message>".PHP_EOL;

		$ndc = $event->getNDC();
		if(!empty($ndc)) {
			$buf .= "<$ns:NDC><![CDATA[";
			$buf .= $this->encodeCDATA($ndc);
			$buf .= "]]></$ns:NDC>".PHP_EOL;
		}
		
		$mdcMap = $event->getMDCMap();
		if (!empty($mdcMap)) {
			$buf .= "<$ns:properties>".PHP_EOL;
			foreach ($mdcMap as $name=>$value) {
				$buf .= "<$ns:data name=\"$name\" value=\"$value\" />".PHP_EOL;
			}
			$buf .= "</$ns:properties>".PHP_EOL;
		}

		if ($this->getLocationInfo()) {
			$locationInfo = $event->getLocationInformation();
			$buf .= "<$ns:locationInfo ". 
					"class=\"" . $locationInfo->getClassName() . "\" ".
					"file=\"" .  htmlentities($locationInfo->getFileName(), ENT_QUOTES) . "\" ".
					"line=\"" .  $locationInfo->getLineNumber() . "\" ".
					"method=\"" . $locationInfo->getMethodName() . "\" ";
			$buf .= "/>".PHP_EOL;
		}

		$buf .= "</$ns:event>".PHP_EOL;
		
		return $buf;
	}
	
	/**
	 * @return string
	 */
	public function getFooter() {
		return "</{$this->namespacePrefix}:eventSet>" . PHP_EOL;
	}
	
	
	/** 
	 * Whether or not file name and line number will be included in the output.
	 * @return boolean
	 */
	public function getLocationInfo() {
		return $this->locationInfo;
	}
  
	/**
	 * The {@link $locationInfo} option takes a boolean value. By default,
	 * it is set to false which means there will be no location
	 * information output by this layout. If the the option is set to
	 * true, then the file name and line number of the statement at the
	 * origin of the log statement will be output.
	 */
	public function setLocationInfo($flag) {
		$this->setBoolean('locationInfo', $flag);
	}
  
	/**
	 * @return boolean
	 */
	 public function getLog4jNamespace() {
	 	return $this->log4jNamespace;
	 }

	/**
	 * @param boolean
	 */
	public function setLog4jNamespace($flag) {
		$this->setBoolean('log4jNamespace', $flag);
	}
	
	/** 
	 * Encases a string in CDATA tags, and escapes any existing CDATA end 
	 * tags already present in the string.
	 * @param string $string 
	 */
	private function encodeCDATA($string) {
		$string = str_replace(self::CDATA_END, self::CDATA_EMBEDDED_END, $string);
		return self::CDATA_START . $string . self::CDATA_END;
	}
}

