package org.silexlabs.amfphp.clientgenerator
{
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.NetStatusEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.NetConnection;

	public class NetConnectionSingleton
	{   
        public static var _host:String;
		private static var _netConnectionInstance:NetConnection;
		
		public function NetConnectionSingleton()
		{
			
		}
		
		public static function getNetConnection():NetConnection{
			if(!_netConnectionInstance){
				_netConnectionInstance = new NetConnection();
				var amfphpEntryPointUrl:String = _host ? _host + '/amf' : "/*ACG_AMFPHPURL*/";
				
				//amfphpEntryPointUrl = "http://localhost:8888/workspaceNetbeans/amfphp-2.0/Tests/TestData/";
				_netConnectionInstance.connect(amfphpEntryPointUrl);
				_netConnectionInstance.addEventListener(IOErrorEvent.IO_ERROR, errorHandler);
				_netConnectionInstance.addEventListener(NetStatusEvent.NET_STATUS, errorHandler);
				_netConnectionInstance.addEventListener(SecurityErrorEvent.SECURITY_ERROR, errorHandler);
			}
			return _netConnectionInstance;
		}
		
		private static function errorHandler(event:Event):void{
			trace(event.toString());
		}
	}
}