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
 */

/**
 * LoggerAppenderPDO appender logs to a database using the PHP's PDO extension.
 *
 * ## Configurable parameters: ##
 *
 * - dsn             - The Data Source Name (DSN) used to connect to the database.
 * - user            - Username used to connect to the database.
 * - password        - Password used to connect to the database.
 * - table           - Name of the table to which log entries are be inserted.
 * - insertSQL       - Sets the insert statement for a logging event. Defaults
 *                     to the correct one - change only if you are sure what you are doing.
 * - insertPattern   - The conversion pattern to use in conjuction with insert 
 *                     SQL. Must contain the same number of comma separated 
 *                     conversion patterns as there are question marks in the 
 *                     insertSQL.
 *
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @since 2.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/pdo.html Appender documentation
 */
class LoggerAppenderPDO extends LoggerAppender {

	// ******************************************
	// *** Configurable parameters            ***
	// ******************************************
	
	/** 
	 * DSN string used to connect to the database.
	 * @see http://www.php.net/manual/en/pdo.construct.php
	 */
	protected $dsn;

	/** Database user name. */
	protected $user;
	
	/** Database password. */
	protected $password;
	
	/** 
	 * The insert query.
	 * 
	 * The __TABLE__ placeholder will be replaced by the table name from 
	 * {@link $table}.
	 *  
	 * The questionmarks are part of the prepared statement, and they must 
	 * match the number of conversion specifiers in {@link insertPattern}.
	 */
	protected $insertSQL = "INSERT INTO __TABLE__ (timestamp, logger, level, message, thread, file, line) VALUES (?, ?, ?, ?, ?, ?, ?)";

	/** 
	 * A comma separated list of {@link LoggerPatternLayout} format strings 
	 * which replace the "?" in {@link $insertSQL}.
	 * 
	 * Must contain the same number of comma separated conversion patterns as 
	 * there are question marks in {@link insertSQL}.
 	 * 
 	 * @see LoggerPatternLayout For conversion patterns.
	 */
	protected $insertPattern = "%date{Y-m-d H:i:s},%logger,%level,%message,%pid,%file,%line";

	/** Name of the table to which to append log events. */
	protected $table = 'log4php_log';
	
	/** The number of recconect attempts to make on failed append. */
	protected $reconnectAttempts = 3;
	
	
	// ******************************************
	// *** Private memebers                   ***
	// ******************************************
	
	/** 
	 * The PDO instance.
	 * @var PDO 
	 */
	protected $db;
	
	/** 
	 * Prepared statement for the insert query.
	 * @var PDOStatement 
	 */
	protected $preparedInsert;
	
	/** This appender does not require a layout. */
	protected $requiresLayout = false;


	// ******************************************
	// *** Appender methods                   ***
	// ******************************************
	
	/**
	 * Acquires a database connection based on parameters.
	 * Parses the insert pattern to create a chain of converters which will be
	 * used in forming query parameters from logging events.
	 */
	public function activateOptions() {
		try {
			$this->establishConnection();
		} catch (PDOException $e) {
			$this->warn("Failed connecting to database. Closing appender. Error: " . $e->getMessage());
			$this->close();
			return;
		}

		// Parse the insert patterns; pattern parts are comma delimited
		$pieces = explode(',', $this->insertPattern);
		$converterMap = LoggerLayoutPattern::getDefaultConverterMap();
		foreach($pieces as $pattern) {
			$parser = new LoggerPatternParser($pattern, $converterMap);
			$this->converters[] = $parser->parse(); 
		}
		
		$this->closed = false;
	}
	
	/** 
	 * Connects to the database, and prepares the insert query.
	 * @throws PDOException If connect or prepare fails.  
	 */
	protected function establishConnection() {
		// Acquire database connection
		$this->db = new PDO($this->dsn, $this->user, $this->password);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		// Prepare the insert statement
		$insertSQL = str_replace('__TABLE__', $this->table, $this->insertSQL);
		$this->preparedInsert = $this->db->prepare($insertSQL);
	}
	
	/**
	 * Appends a new event to the database.
	 * 
	 * If writing to database fails, it will retry by re-establishing the 
	 * connection up to $reconnectAttempts times. If writing still fails, 
	 * the appender will close.
	 */
	public function append(LoggerLoggingEvent $event) {

		for ($attempt = 1; $attempt <= $this->reconnectAttempts + 1; $attempt++) {
			try {
				// Attempt to write to database
				$this->preparedInsert->execute($this->format($event));
				$this->preparedInsert->closeCursor();
				break;
			} catch (PDOException $e) {
				$this->warn("Failed writing to database: ". $e->getMessage());
				
				// Close the appender if it's the last attempt
				if ($attempt > $this->reconnectAttempts) {
					$this->warn("Failed writing to database after {$this->reconnectAttempts} reconnect attempts. Closing appender.");
					$this->close();
				// Otherwise reconnect and try to write again
				} else {
					$this->warn("Attempting a reconnect (attempt $attempt of {$this->reconnectAttempts}).");
					$this->establishConnection();
				}
			}
		}
	}
	
	/**
	 * Converts the logging event to a series of database parameters by using 
	 * the converter chain which was set up on activation. 
	 */
	protected function format(LoggerLoggingEvent $event) {
		$params = array();
		foreach($this->converters as $converter) {
			$buffer = '';
			while ($converter !== null) {
				$converter->format($buffer, $event);
				$converter = $converter->next;
			}
			$params[] = $buffer;
		}
		return $params;
	}
	
	/**
	 * Closes the connection to the logging database
	 */
	public function close() {
		// Close the connection (if any)
		$this->db = null;
		
		// Close the appender
		$this->closed = true;
	}
	
	// ******************************************
	// *** Accessor methods                   ***
	// ******************************************
	
	/**
	 * Returns the active database handle or null if not established.
	 * @return PDO
	 */
	public function getDatabaseHandle() {
		return $this->db;
	}
	
	/** Sets the username. */
	public function setUser($user) {
		$this->setString('user', $user);
	}
	
	/** Returns the username. */
	public function getUser($user) {
		return $this->user;
	}
	
	/** Sets the password. */
	public function setPassword($password) {
		$this->setString('password', $password);
	}
	
	/** Returns the password. */
	public function getPassword($password) {
		return $this->password;
	}
	
	/** Sets the insert SQL. */
	public function setInsertSQL($sql) {
		$this->setString('insertSQL', $sql);
	}
	
	/** Returns the insert SQL. */
	public function getInsertSQL($sql) {
		return $this->insertSQL;
	}

	/** Sets the insert pattern. */
	public function setInsertPattern($pattern) {
		$this->setString('insertPattern', $pattern);
	}
	
	/** Returns the insert pattern. */
	public function getInsertPattern($pattern) {
		return $this->insertPattern;
	}

	/** Sets the table name. */
	public function setTable($table) {
		$this->setString('table', $table);
	}
	
	/** Returns the table name. */
	public function getTable($table) {
		return $this->table;
	}
	
	/** Sets the DSN string. */
	public function setDSN($dsn) {
		$this->setString('dsn', $dsn);
	}
	
	/** Returns the DSN string. */
	public function getDSN($dsn) {
		return $this->setString('dsn', $dsn);
	}	
}
