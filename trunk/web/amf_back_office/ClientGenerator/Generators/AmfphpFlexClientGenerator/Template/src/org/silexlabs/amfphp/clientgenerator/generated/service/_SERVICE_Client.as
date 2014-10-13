package org.silexlabs.amfphp.clientgenerator.generated.service {
    
    import mx.rpc.AsyncToken;
    import mx.rpc.remoting.RemoteObject;
    

/*ACG_SERVICE_COMMENT*/
    public class _SERVICE_Client {
        
        private var _ro:RemoteObject;
        
        public function _SERVICE_Client(){
            
            _ro = new RemoteObject;    
            _ro.destination = "amfphp"; 
            _ro.source = "_SERVICE_";                                
        }
        
        
        public function get ro():RemoteObject
        {
            return _ro;
        }
        
/*ACG_METHOD*/
/*ACG_METHOD_COMMENT*/
        public function _METHOD_(/*ACG_PARAMETER_COMMA*/_PARAMETER_:Object/*ACG_PARAMETER_COMMA*/):AsyncToken { 
            return _ro._METHOD_(/*ACG_PARAMETER_COMMA*/_PARAMETER_/*ACG_PARAMETER_COMMA*/);
        }
/*ACG_METHOD*/
        
    }
}
