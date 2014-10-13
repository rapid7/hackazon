package org.bytearray.gif.frames
{
	import flash.display.BitmapData;
	
	public class GIFFrame 
	{
		public var bitmapData:BitmapData;
		public var delay:int;
			
		public function GIFFrame( pImage:BitmapData, pDelay:int )	
		{
			bitmapData = pImage;
			delay = pDelay;	
		}
	}
}