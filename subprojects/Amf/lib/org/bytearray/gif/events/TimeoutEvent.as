package org.bytearray.gif.events
{
	import flash.events.Event;;
	
	public class TimeoutEvent extends Event
	{
		public static const TIME_OUT:String = "timeout";
		
		public function TimeoutEvent ( pType:String )	
		{
			super ( pType, false, false );			
		}
	}
}