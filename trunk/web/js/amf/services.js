var amfphp;
if(!amfphp){
	amfphp = {};
}
var App = window.App || {config: {}};

amfphp.services = {};

/**
 * set by default to server url with which the code was generated. The contentType parameter is to make sure the server interprets the request as JSON
 * */
amfphp.entryPointUrl = (App.config.host || "http://hackazon.com") + "/amf?contentType=application/json";

/** 
*   Coupon service. Provides method for operating vouchers
*   @package AmfphpModule\Services
*   */
amfphp.services.CouponService = {};


/**  */
amfphp.services.CouponService.registerVoucher = function(onSuccess, onError, date, dayOfWeek){
	var callData = JSON.stringify({"serviceName":"CouponService", "methodName":"registerVoucher","parameters":[date, dayOfWeek]});
	    $.post(amfphp.entryPointUrl, callData, onSuccess)
	    	.error(onError);
	
}
