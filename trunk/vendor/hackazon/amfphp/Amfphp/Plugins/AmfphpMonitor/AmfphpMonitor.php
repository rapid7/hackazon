<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */
/**
 *  includes
 */
require_once dirname(__FILE__) . '/AmfphpMonitorService.php';

/**
 * logs monitoring information, and makes it possible to toggle logging and retrieve the data via the AmfphpMonitorService.
 * If the log file is not writable or its size is superior to maxLogFileSize, 
 * logging shall fail silently. This is designed to avoid errors being generated 
 * when a developer forgets to turn off monitoring, and to allow the plugin to be enabled 
 * by default
 * 
 * The log file is by default at [AmfphpMonitor plugin folder]/log.txt.php 
 * To change this set 'logPath' in the config.
 * 
 * 
 * 
 * note: Logging multiple times with the same name is not possible!
 * @todo maybe change storage mechanism to sqlite. This means checking that it is indeed available, checking if performance is ok etc., so it might be a bit heavy handed.
 * 
 * @author Ariel Sommeria-klein
 * @package Amfphp_Plugins_Monitor
 */
class AmfphpMonitor {
    /**
     * path to log file. If it is publicly accessible
     * @var string 
     */
    protected $logPath;
    
    
    /**
     * service and method name. If they are multiple calls in request, they are spearated with a ', '
     * @var string
     */
    protected $uri;

    /**
     * was there an exception during service call.
     * todo. unused.
     * @var boolean 
     */
    protected $isException;

    
    /**
     * last measured time, or start time
     * @var float 
     */
    protected static $lastMeasuredTime;
    
    /**
     * various times.  for example ['startD' => 12 , 'stopD' => 30 ] 
     * means start of deserialization at 12 ms after the request was received, 
     * and end of deserialization 30 ms after start of deserialization.
     * @var array
     */
    protected static $times;

    /**
     * restrict access to amfphp_admin, the role set when using the back office. default is true. 
     * @var boolean
     */
    protected $restrictAccess = true;
    
    /**
     * The maximum size of the log file, in bytes. Once the log is bigger than this, logging stops.
     * Note that this is not strict, it can overflow with the last log.
     * @var int 
     */
    protected $maxLogFileSize = 1000000;
    
    /**
     * constructor.
     * manages log path. If file exists at log path, adds hooks for logging.
     * @param array $config 
     */
    public function __construct(array $config = null) {
        self::$lastMeasuredTime = round(microtime(true) * 1000);
        self::$times = array();
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERVICE_NAMES_2_CLASS_FIND_INFO, $this, 'filterServiceNames2ClassFindInfo');
        
        if (isset($config['logPath'])) {
            $this->logPath = $config['logPath'];
        }else{
            $this->logPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'log.txt.php';
        }
        AmfphpMonitorService::$logPath = $this->logPath;
        if(isset($config['restrictAccess'])){
            $this->restrictAccess = $config['restrictAccess'];    
        }
        AmfphpMonitorService::$restrictAccess = $this->restrictAccess;
        if(isset($config['maxLogFileSize'])){
            $this->maxLogFileSize = $config['maxLogFileSize'];    
        }
        AmfphpMonitorService::$maxLogFileSize = $this->maxLogFileSize;
        
        if(!is_writable($this->logPath) || !is_readable($this->logPath)){
            return;
        }
        
        if(filesize($this->logPath) > $this->maxLogFileSize){
            return;
        }
        
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST, $this, 'filterDeserializedRequest', 0);
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_RESPONSE, $this, 'filterDeserializedResponse', 0);
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZED_RESPONSE, $this, 'filterSerializedResponse');
            
    }
    
    /**
     * measures time since previous call (or start time time if this the first call) , and stores it in the times array
     * public and static so that services can call this too to add custom times.
     * updates lastMeasuredTime
     * @param string $name 
     */
    public static function addTime($name){
        $now = round(microtime(true) * 1000);
        $timeSinceLastMeasure = $now - self::$lastMeasuredTime;
        self::$times[$name] = $timeSinceLastMeasure;
        self::$lastMeasuredTime = $now;
    }
    /**
     * add monitor service
     * @param array $serviceNames2ClassFindInfo associative array of key -> class find info
     */
    public function filterServiceNames2ClassFindInfo(array $serviceNames2ClassFindInfo) {
        $serviceNames2ClassFindInfo['AmfphpMonitorService'] = new Amfphp_Core_Common_ClassFindInfo(dirname(__FILE__) . '/AmfphpMonitorService.php', 'AmfphpMonitorService');
        return $serviceNames2ClassFindInfo;
    }
    
    /**
     * logs the time for end of deserialization, as well as grabs the target uris(service + method)
     * as each request has its own format, the code here must handle all deserialized request structures. 
     * if case not handled just don't set target uris, as data can still be useful even without them.
     * @param mixed $deserializedRequest
     */
    public function filterDeserializedRequest($deserializedRequest) {
        self::addTime('Deserialization');
        //AMF
        if(is_a($deserializedRequest, 'Amfphp_Core_Amf_Packet')){
            //detect Flex by looking at first message. assumes that request doesn't mix simple AMF remoting with Flex Messaging
            $isFlex = ($deserializedRequest->messages[0]->targetUri == 'null'); //target Uri is described in Flex message
            
            for($i = 0; $i < count($deserializedRequest->messages); $i++){
                if($i > 0){
                    //add multiple uris split with a ', '
                    $this->uri .= ', ';
                }
                $message = $deserializedRequest->messages[$i];

                if ($isFlex){
                    $flexMessage = $message->data[0];
                    $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
                    $messageType = $flexMessage->$explicitTypeField;
                    //assumes AmfphpFlexMessaging plugin is installed, which is reasonable given that we're using Flex messaging
                    if ($messageType == AmfphpFlexMessaging::FLEX_TYPE_COMMAND_MESSAGE) {
                        $this->uri .= "Flex Command Message";
                    }else if ($messageType == AmfphpFlexMessaging::FLEX_TYPE_REMOTING_MESSAGE) {                    
                        $this->uri .= $flexMessage->source . '.' . $flexMessage->operation;
                    }else{
                        $this->uri .= 'Flex ' . $messageType;
                    }
                }else{
                    $this->uri .= $message->targetUri;
                }
                
            }
        }else if(isset ($deserializedRequest->serviceName)){
        //JSON
            $this->uri = $deserializedRequest->serviceName . '/' . $deserializedRequest->methodName;
        }else if(is_array($deserializedRequest) && isset ($deserializedRequest['serviceName'])){
            //GET, included request
            $this->uri = $deserializedRequest['serviceName'] . '/' . $deserializedRequest['methodName'];
        }
        
        
    }

    /**
     * logs the time for start of serialization
     * @param packet $deserializedResponse
     */
    public function filterDeserializedResponse($deserializedResponse) {
        self::addTime('Service Call');

    }

    /**
     * logs the time for end of serialization and writes log
     * ignores calls to Amfphp services (checks for 'Amfphp' at beginning of name)
     * tries to get a lock on the file, and if not then just drops the log. 
     * 
     * @param mixed $rawData
     */
    public function filterSerializedResponse($rawData) {
        if(substr($this->uri, 0, 6) == 'Amfphp'){
            return;
        }
        
        if(filesize($this->logPath) > $this->maxLogFileSize){
            return;
        }
        
        self::addTime('Serialization');
        $record = new stdClass();
        $record->uri = $this->uri;
        $record->times = self::$times;
        
        $fp = fopen($this->logPath, "a");

        if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
            fwrite($fp, "\n" . serialize($record));
            fflush($fp);            // flush output before releasing the lock
            flock($fp, LOCK_UN);    // release the lock
        } else {
            echo "Couldn't get the lock!";
        }

        fclose($fp);        
        
    }

    
}

?>
