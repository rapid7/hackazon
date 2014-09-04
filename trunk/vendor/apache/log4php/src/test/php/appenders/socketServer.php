<?php

/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   tests
 * @package    log4php
 * @subpackage appenders
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    $Revision$
 * @link       http://logging.apache.org/log4php
 * 
 * A simple socket server used in LoggerAppenderSocketTest.
 */

// Port on which to start the server
define('SERVER_PORT', 12345);

// Prevent hangs
set_time_limit(0);

// Create a socket
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($sock === false) {
	die("Failed creating socket: " . socket_strerror(socket_last_error()));
}

if (socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1) === false) {
	die("Failed setting socket options: " . socket_strerror(socket_last_error()));
}

if (socket_bind($sock, 'localhost', SERVER_PORT) === false) {
	die("Failed binding socket: " . socket_strerror(socket_last_error()));
}

if (socket_listen($sock, 100) === false) {
	die("Failed binding socket: " . socket_strerror(socket_last_error()));
}

socket_getsockname($sock, $addr, $port);
myLog("Server Listening on $addr:$port");

// Buffer which will store incoming messages
$playback = "";

while(true) {
	myLog("Waiting for incoming connections...");
	
	$msgsock = socket_accept($sock);
	if ($msgsock === false) {
		myLog("Failed accepting a connection: " . socket_strerror(socket_last_error()));
		break;
	}
	
	$buf = socket_read($msgsock, 2048, PHP_NORMAL_READ);

	myLog('Received: "' . trim($buf) . '"');
	
	// Shutdown command
	if (trim($buf) == 'shutdown') {
		myLog("Shutting down.");
		socket_close($msgsock);
		break;
	} 
	// Playback command
	else if (trim($buf) == 'playback') {
		myLog("Returning playback: \"$playback\"");
		socket_write($msgsock, $playback);
	} 
	// Default: add to playback buffer
	else {
		$playback .= trim($buf); 
	}
	
	socket_close($msgsock);
}

myLog("Closing socket.");
socket_close($sock);

function myLog($msg) {
	echo date("Y-m-d H:i:s") . " $msg\n";
}

?>
