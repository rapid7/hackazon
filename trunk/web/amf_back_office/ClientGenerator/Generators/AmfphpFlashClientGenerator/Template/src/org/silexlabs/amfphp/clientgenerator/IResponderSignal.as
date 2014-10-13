package org.silexlabs.amfphp.clientgenerator{
	public interface IResponderSignal {
		function setResultHandler(callback:Function):IResponderSignal;
	
	    function setErrorHandler(callback:Function):IResponderSignal;
	}
}
