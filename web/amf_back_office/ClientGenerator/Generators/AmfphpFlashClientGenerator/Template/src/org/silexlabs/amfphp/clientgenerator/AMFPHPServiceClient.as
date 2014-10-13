package org.silexlabs.amfphp.clientgenerator {
	import flash.net.NetConnection;
	import flash.net.Responder;
	
	/**
	 * generated with AmfphpCodeGen. Don't edit directly.
	 */
	public class AMFPHPServiceClient{
		protected var _nc:NetConnection;
		protected var _serviceName:String;
		/**
		 * constructor.
		 * @param NetConnection nc. instantiate the nc, call the connect() method on it with the amfPHP server url, .
		 * maybe add some event listeners for error events, then pass it here. 
		 */
		public function AMFPHPServiceClient(nc:NetConnection, serviceName:String){
			_nc = nc;
			_serviceName = serviceName;
		}
		
		protected function callService(name:String, ...args):IResponderSignal {
			
			var signal:ResponderSignal = new ResponderSignal();
			var method:String = [_serviceName,name].join("/");			
			var responder:Responder = new Responder(signal.handleResult, signal.handleError);
			_nc.call.apply(_nc,[method, responder].concat(args));
			return signal;
			
		}		
	}
}
