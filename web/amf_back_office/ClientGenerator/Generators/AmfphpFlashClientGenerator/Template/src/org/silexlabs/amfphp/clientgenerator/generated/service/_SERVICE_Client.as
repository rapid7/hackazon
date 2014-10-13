package org.silexlabs.amfphp.clientgenerator.generated.service{
    import flash.net.NetConnection;
    import flash.net.Responder;
    import org.silexlabs.amfphp.clientgenerator.AMFPHPServiceClient;
    import org.silexlabs.amfphp.clientgenerator.IResponderSignal;

    /*ACG_SERVICE_COMMENT*/
    public class _SERVICE_Client extends AMFPHPServiceClient {
        /**
        * constructor.
        * @param NetConnection nc. instantiate the nc, call the connect() method on it with the amfPHP server url, .
        * maybe add some event listeners for error events, then pass it here. 
        **/
        public function _SERVICE_Client(nc:NetConnection){
			super(nc, "_SERVICE_");
        }
		
/*ACG_METHOD*/
/*ACG_METHOD_COMMENT*/
        public function _METHOD_(/*ACG_PARAMETER_COMMA*/_PARAMETER_:Object/*ACG_PARAMETER_COMMA*/):IResponderSignal{ 
			return callService("_METHOD_" /*ACG_PARAMETER*/, _PARAMETER_/*ACG_PARAMETER*/);
        }
/*ACG_METHOD*/
                
        
    }
}
