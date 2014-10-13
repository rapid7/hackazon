var amfphp;
if(!amfphp){
	amfphp = {};
}

amfphp.services = {};

/**
 * set by default to server url with which the code was generated. The contentType parameter is to make sure the server interprets the request as JSON
 * */
amfphp.entryPointUrl = "/*ACG_AMFPHPURL*/?contentType=application/json";

/*ACG_SERVICE*//*ACG_SERVICE_COMMENT*/
amfphp.services._SERVICE_ = {};

/*ACG_METHOD*/
/*ACG_METHOD_COMMENT*/
amfphp.services._SERVICE_._METHOD_ = function(onSuccess, onError/*ACG_PARAMETER*/, _PARAMETER_/*ACG_PARAMETER*/){
	var callData = JSON.stringify({"serviceName":"_SERVICE_", "methodName":"_METHOD_","parameters":[/*ACG_PARAMETER_COMMA*/_PARAMETER_/*ACG_PARAMETER_COMMA*/]});
	    $.post(amfphp.entryPointUrl, callData, onSuccess)
	    	.error(onError);
	
}/*ACG_METHOD*/
/*ACG_SERVICE*/