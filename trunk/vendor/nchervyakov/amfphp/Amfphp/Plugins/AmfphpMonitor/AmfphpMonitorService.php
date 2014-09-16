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
 * monitoring service. controls logging, and provides method to fetch data.
 *
 * @amfphpHide
 * @package Amfphp_Plugins_Monitor
 * @author Ariel Sommeria-klein
 */
class AmfphpMonitorService {
    /**
     * path to log. 
     * @see AmfphpMonitor maxLogFileSize
     * @var type string
     */
    public static $logPath;


    /**
     * restrict access to amfphp_admin. 
     * @var boolean
     */
    public static $restrictAccess;
    
       
    /**
     * max log file size
     * @see AmfphpMonitor maxLogFileSize
     * @var int 
     */
    public static $maxLogFileSize;
    
    /**
     * get method roles
     * @param string $methodName
     * @return array
     */
    public function _getMethodRoles($methodName) {
        if (self::$restrictAccess) {
            return array('amfphp_admin');
        }
    }
    
    /**
     * parse logged data and return it. 
     * the format is 
     * obj -> sortedDatas ( array ( uri => times (array (name => array of times)) 
     * obj -> timeNames 
     * 
     * note: timeNames are needed for rendering on the client side, to make sure that each series has the same times.
     * This could be done on the client side, but is faster to do here.
     * 
     * @todo calculate averages per service instead of just returning the data raw.
     * @param boolean $flush get rid of the logged data 
     * @return array 
     */
    public function getData($flush){
        if(!is_writable(self::$logPath) || !is_readable(self::$logPath)){
            throw new Amfphp_Core_Exception('AmfphpMonitor does not have permission to read and write to log file: ' . self::$logPath);
        }
                
        if(!file_exists(self::$logPath)){
            return null;
        }
        $fileContent = file_get_contents(self::$logPath);
        //ignore "php exit " line
        $loggedData = substr($fileContent, 16);
        if($flush){
            $this->flush();
        }
        $exploded = explode("\n", $loggedData);
        
        //data is sorted by target uri
        $sortedData = array();
        //use associative array to avoid duplicating time  names, then return keys.
        $timeNamesAssoc = array();
        foreach($exploded as $serializedRecord){
            $record = unserialize($serializedRecord); 
            if(!$record){
                continue;
            }
            $uri = $record->uri;
            if(!isset($sortedData[$uri])){
                $sortedData[$uri] = array();
            }
            $uriData = &$sortedData[$uri];
            //sort times
            foreach($record->times as $timeName => $timeValue){
                if(!isset ($uriData[$timeName])){
                    $uriData[$timeName] = array();
                }
                $uriData[$timeName][] = $timeValue;
                $timeNamesAssoc[$timeName] = '';
            }
            
        }
        $ret = new stdClass();
        $ret->sortedData = $sortedData;
        $ret->timeNames = array_keys($timeNamesAssoc);
        
        if(filesize(self::$logPath) > self::$maxLogFileSize){
            $ret->serverComment = 'The log file is full, so it is possible that your latest service calls are not represented. Either flush the log using the "flush" button or increase the log size(maxLogFileSize)';
        }
        
        
        return $ret;
    }
    /**
     * flush monitor log
     */
    public function flush(){
        if(!is_writable(self::$logPath) || !is_readable(self::$logPath)){
            throw new Amfphp_Core_Exception('AmfphpMonitor does not have permission to read and write to log file: ' . self::$logPath);
        }
                
        if(file_put_contents(self::$logPath, "<?php exit();?>\n") === false){
            throw new Amfphp_Core_Exception('AmfphpMonitor write file failed ' . self::$logPath);
        }
    }
}

?>
