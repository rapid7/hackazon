<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// parse pom.xml to get version in sync
$xml = simplexml_load_file('../../pom.xml');
$pom_version = (string) $xml->version;

preg_match("/^([^-]+)(-SNAPSHOT)?$/", $pom_version, $matches);
$version = $matches[1];

// Maven release plugin always requires the version to have -SNAPSHOT in 
// the version node. So this is not possible:  
// $stability = empty($matches[2]) ? 'stable' : 'devel';

// Hard coded to stable. Maybe find better solution.
$stability = 'stable';

$name = 'Apache_log4php';
$summary = 'A versatile logging framework for PHP';

$description = <<<EOT
Apache log4php is a versatile logging framework for PHP at the Apache Software Foundation (ASF).
EOT;

$notes = 'Please see CHANGELOG and changes.xml!';

$options = array(
	'license' => 'Apache License 2.0',
	//'filelistgenerator' => 'svn',
	'ignore' => array('package.php', 'package-config.php'),
	'simpleoutput' => true,
	'baseinstalldir' => '/',
	'packagedirectory' => '.',
	'dir_roles' => array(
		'examples' => 'doc',
	),
	'exceptions' => array(
	    'changes.xml' =>  'doc',
		'CHANGELOG' => 'doc',
		'LICENSE' => 'doc',
		'README' => 'doc',
		'NOTICE' => 'doc',
	),
);

$license = array(
	'name' => 'Apache License 2.0',
	'url' => 'http://www.apache.org/licenses/LICENSE-2.0'
);

$maintainer = array();
$maintainer[]   =   array(
	'role' => 'lead',
	'handle' => 'grobmeier',
	'name' => 'Christian Grobmeier',
	'email' => 'grobmeier@apache.org',
	'active' => 'yes'
);
$maintainer[]   =   array(
    'role' => 'developer',
    'handle' => 'ihabunek',
    'name' => 'Ivan Habunek',
    'email' => 'ihabunek@apache.org',
    'active' => 'yes'
);
$maintainer[]  =   array(
	'role' => 'lead',
	'handle' => 'kurdalen',
	'name' => 'Knut Urdalen',
	'email' => 'kurdalen@apache.org',
	'active' => 'no'
);
$maintainer[]   =   array(
    'role' => 'developer',
    'handle' => 'chammers',
    'name' => 'Christian Hammers',
    'email' => 'chammers@apache.org',
    'active' => 'no'
);

$dependency = array();

$channel = 'pear.apache.org/log4php';
$require = array(
	'php' => '5.2.0',
	'pear_installer' => '1.7.0',
);
