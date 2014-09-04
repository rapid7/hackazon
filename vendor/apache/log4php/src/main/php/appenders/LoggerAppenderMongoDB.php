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
 * Appender for writing to MongoDB.
 * 
 * This class was originally contributed by Vladimir Gorej.
 * 
 * ## Configurable parameters: ##
 * 
 * - **host** - Server on which mongodb instance is located. 
 * - **port** - Port on which the instance is bound.
 * - **databaseName** - Name of the database to which to log.
 * - **collectionName** - Name of the target collection within the given database.
 * - **username** - Username used to connect to the database.
 * - **password** - Password used to connect to the database.
 * - **timeout** - For how long the driver should try to connect to the database (in milliseconds).
 * 
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @since 2.1
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/mongodb.html Appender documentation
 * @link http://github.com/log4mongo/log4mongo-php Vladimir Gorej's original submission.
 * @link http://www.mongodb.org/ MongoDB website.
 */
class LoggerAppenderMongoDB extends LoggerAppender {
	
	// ******************************************
	// ** Constants                            **
	// ******************************************
	
	/** Default prefix for the {@link $host}. */	
	const DEFAULT_MONGO_URL_PREFIX = 'mongodb://';
	
	/** Default value for {@link $host}, without a prefix. */
	const DEFAULT_MONGO_HOST = 'localhost';
	
	/** Default value for {@link $port} */
	const DEFAULT_MONGO_PORT = 27017;
	
	/** Default value for {@link $databaseName} */
	const DEFAULT_DB_NAME = 'log4php_mongodb';
	
	/** Default value for {@link $collectionName} */
	const DEFAULT_COLLECTION_NAME = 'logs';
	
	/** Default value for {@link $timeout} */
	const DEFAULT_TIMEOUT_VALUE = 3000;
	
	// ******************************************
	// ** Configurable parameters              **
	// ******************************************
	
	/** Server on which mongodb instance is located. */
	protected $host;
	
	/** Port on which the instance is bound. */
	protected $port;
	
	/** Name of the database to which to log. */
	protected $databaseName;
	
	/** Name of the collection within the given database. */
	protected $collectionName;
			
	/** Username used to connect to the database. */
	protected $userName;
	
	/** Password used to connect to the database. */
	protected $password;
	
	/** Timeout value used when connecting to the database (in milliseconds). */
	protected $timeout;
	
	// ******************************************
	// ** Member variables                     **
	// ******************************************

	/** 
	 * Connection to the MongoDB instance.
	 * @var Mongo
	 */
	protected $connection;
	
	/** 
	 * The collection to which log is written. 
	 * @var MongoCollection
	 */
	protected $collection;

	public function __construct($name = '') {
		parent::__construct($name);
		$this->host = self::DEFAULT_MONGO_URL_PREFIX . self::DEFAULT_MONGO_HOST;
		$this->port = self::DEFAULT_MONGO_PORT;
		$this->databaseName = self::DEFAULT_DB_NAME;
		$this->collectionName = self::DEFAULT_COLLECTION_NAME;
		$this->timeout = self::DEFAULT_TIMEOUT_VALUE;
		$this->requiresLayout = false;
	}
	
	/**
	 * Setup db connection.
	 * Based on defined options, this method connects to the database and 
	 * creates a {@link $collection}. 
	 */
	public function activateOptions() {
		try {
			$this->connection = new Mongo(sprintf('%s:%d', $this->host, $this->port), array('timeout' => $this->timeout));
			$db	= $this->connection->selectDB($this->databaseName);
			if ($this->userName !== null && $this->password !== null) {
				$authResult = $db->authenticate($this->userName, $this->password);
				if ($authResult['ok'] == floatval(0)) {
					throw new Exception($authResult['errmsg'], $authResult['ok']);
				}
			}
			$this->collection = $db->selectCollection($this->collectionName);
		} catch (MongoConnectionException $ex) {
			$this->closed = true;
			$this->warn(sprintf('Failed to connect to mongo deamon: %s', $ex->getMessage()));
		} catch (InvalidArgumentException $ex) {
			$this->closed = true;
			$this->warn(sprintf('Error while selecting mongo database: %s', $ex->getMessage()));
		} catch (Exception $ex) {
			$this->closed = true;
			$this->warn('Invalid credentials for mongo database authentication');
		}
	}

	/**
	 * Appends a new event to the mongo database.
	 *
	 * @param LoggerLoggingEvent $event
	 */
	public function append(LoggerLoggingEvent $event) {
		try {
			if ($this->collection != null) {
				$this->collection->insert($this->format($event));
			}
		} catch (MongoCursorException $ex) {
			$this->warn(sprintf('Error while writing to mongo collection: %s', $ex->getMessage()));
		}
	}
	
	/**
	 * Converts the logging event into an array which can be logged to mongodb.
	 * 
	 * @param LoggerLoggingEvent $event
	 * @return array The array representation of the logging event.
	 */
	protected function format(LoggerLoggingEvent $event) {
		$timestampSec = (int) $event->getTimestamp();
		$timestampUsec = (int) (($event->getTimestamp() - $timestampSec) * 1000000);

		$document = array(
			'timestamp' => new MongoDate($timestampSec, $timestampUsec),
			'level' => $event->getLevel()->toString(),
			'thread' => (int) $event->getThreadName(),
			'message' => $event->getMessage(),
			'loggerName' => $event->getLoggerName() 
		);	

		$locationInfo = $event->getLocationInformation();
		if ($locationInfo != null) {
			$document['fileName'] = $locationInfo->getFileName();
			$document['method'] = $locationInfo->getMethodName();
			$document['lineNumber'] = ($locationInfo->getLineNumber() == 'NA') ? 'NA' : (int) $locationInfo->getLineNumber();
			$document['className'] = $locationInfo->getClassName();
		}	

		$throwableInfo = $event->getThrowableInformation();
		if ($throwableInfo != null) {
			$document['exception'] = $this->formatThrowable($throwableInfo->getThrowable());
		}
		
		return $document;
	}
	
	/**
	 * Converts an Exception into an array which can be logged to mongodb.
	 * 
	 * Supports innner exceptions (PHP >= 5.3)
	 * 
	 * @param Exception $ex
	 * @return array
	 */
	protected function formatThrowable(Exception $ex) {
		$array = array(				
			'message' => $ex->getMessage(),
			'code' => $ex->getCode(),
			'stackTrace' => $ex->getTraceAsString(),
		);
        
		if (method_exists($ex, 'getPrevious') && $ex->getPrevious() !== null) {
			$array['innerException'] = $this->formatThrowable($ex->getPrevious());
		}
		
		return $array;
	}
		
	/**
	 * Closes the connection to the logging database
	 */
	public function close() {
		if($this->closed != true) {
			$this->collection = null;
			if ($this->connection !== null) {
				$this->connection->close();
				$this->connection = null;
			}
			$this->closed = true;
		}
	}
	
	/** 
	 * Sets the value of {@link $host} parameter.
	 * @param string $host
	 */
	public function setHost($host) {
		if (!preg_match('/^mongodb\:\/\//', $host)) {
			$host = self::DEFAULT_MONGO_URL_PREFIX . $host;
		}
		$this->host = $host;
	}
		
	/** 
	 * Returns the value of {@link $host} parameter.
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}

	/** 
	 * Sets the value of {@link $port} parameter.
	 * @param int $port
	 */
	public function setPort($port) {
		$this->setPositiveInteger('port', $port);
	}
		
	/** 
	 * Returns the value of {@link $port} parameter.
	 * @return int
	 */
	public function getPort() {
		return $this->port;
	}

	/** 
	 * Sets the value of {@link $databaseName} parameter.
	 * @param string $databaseName
	 */
	public function setDatabaseName($databaseName) {
		$this->setString('databaseName', $databaseName);
	}
		
	/** 
	 * Returns the value of {@link $databaseName} parameter.
	 * @return string
	 */
	public function getDatabaseName() {
		return $this->databaseName;
	}

	/** 
	 * Sets the value of {@link $collectionName} parameter.
	 * @param string $collectionName
	 */
	public function setCollectionName($collectionName) {
		$this->setString('collectionName', $collectionName);
	}
		
	/** 
	 * Returns the value of {@link $collectionName} parameter.
	 * @return string
	 */
	public function getCollectionName() {
		return $this->collectionName;
	}

	/** 
	 * Sets the value of {@link $userName} parameter.
	 * @param string $userName
	 */
	public function setUserName($userName) {
		$this->setString('userName', $userName, true);
	}
	
	/** 
	 * Returns the value of {@link $userName} parameter.
	 * @return string
	 */
	public function getUserName() {
		return $this->userName;
	}

	/** 
	 * Sets the value of {@link $password} parameter.
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->setString('password', $password, true);
	}
		
	/** 
	 * Returns the value of {@link $password} parameter.
	 * @return string 
	 */
	public function getPassword() {
		return $this->password;
	}

	/** 
	 * Sets the value of {@link $timeout} parameter.
	 * @param int $timeout
	 */
	public function setTimeout($timeout) {
		$this->setPositiveInteger('timeout', $timeout);
	}

	/** 
	 * Returns the value of {@link $timeout} parameter.
	 * @return int
	 */
	public function getTimeout() {
		return $this->timeout;
	}
	/** 
	 * Returns the mongodb connection.
	 * @return Mongo
	 */
	public function getConnection() {
		return $this->connection;
	}
	
	/** 
	 * Returns the active mongodb collection.
	 * @return MongoCollection
	 */
	public function getCollection() {
		return $this->collection;
	}
}
