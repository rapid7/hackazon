var amfphp;
if(!amfphp){
	amfphp = {};
}

amfphp.services = {};

/**
 * set by default to server url with which the code was generated. The contentType parameter is to make sure the server interprets the request as JSON
 * */
amfphp.entryPointUrl = "../Amfphp/index.php?contentType=application/json";

/** 
*   monitoring service. controls logging, qnd provides method to fetch data.
*  
*   @amfphpHide
*   @package Amfphp_Plugins_Monitor
*   @author Ariel Sommeria-klein
*   */
amfphp.services.AmfphpMonitorService = {};


/** 
*   parse logged data and return it. 
*   the format is 
*   obj -> sortedDatas ( array ( uri => times (array (name => array of times)) 
*   obj -> timeNames 
*   
*   note: timeNames are needed for rendering on the client side, to make sure that each series has the same times.
*   This could be done on the client side, but is faster to do here.
*   
*   @todo calculate averages per service instead of just returning the data raw.
*   @param boolean $flush get rid of the logged data 
*   @return array 
*   */
amfphp.services.AmfphpMonitorService.getData = function(onSuccess, onError, flush){
	var callData = JSON.stringify({"serviceName":"AmfphpMonitorService", "methodName":"getData","parameters":[flush]});
	    $.post(amfphp.entryPointUrl, callData, onSuccess, "json")
	    	.fail(onError);
	
}
/** 
*   flush monitor log
*   */
amfphp.services.AmfphpMonitorService.flush = function(onSuccess, onError){
	var callData = JSON.stringify({"serviceName":"AmfphpMonitorService", "methodName":"flush","parameters":[]});
	    $.post(amfphp.entryPointUrl, callData, onSuccess, "json")
	    	.fail(onError);
	
}
/** 
*   analyses existing services. Warning: if 2 or more services have the same name, only one will appear in the returned data, 
*   as it is an associative array using the service name as key. 
*   @amfphpHide
*   @package Amfphp_Plugins_Discovery
*   @author Ariel Sommeria-Klein
*   */
amfphp.services.AmfphpDiscoveryService = {};


/** 
*   does the actual collection of data about available services
*   @return array of AmfphpDiscovery_ServiceInfo
*   */
amfphp.services.AmfphpDiscoveryService.discover = function(onSuccess, onError){
	var callData = JSON.stringify({"serviceName":"AmfphpDiscoveryService", "methodName":"discover","parameters":[]});
	    $.post(amfphp.entryPointUrl, callData, onSuccess, "json")
	    	.fail(onError);
	
}
