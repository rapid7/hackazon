package org.silexlabs.amfphp.clientgenerator.ui.elements
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	
	public class Link extends MovieClip
	{
		public var textField:TextField;
		private var _address:String;
		
		public function Link()
		{
			super();
			useHandCursor = buttonMode = true;
			mouseChildren = false;
			addEventListener(MouseEvent.CLICK, clickHandler);
			textField.autoSize = TextFieldAutoSize.LEFT;
		}
		
		private function clickHandler(event:MouseEvent):void{
			navigateToURL(new URLRequest(_address), "_blank");	
		}
		
		[Inspectable]
		public function get label():String{
			return textField.text;
		}
		
		public function set label(value:String):void{
			textField.text = value as String;
		}
		
		[Inspectable]
		public function get address():String{
			return _address;
		}
		
		public function set address(value:String):void{
			_address = value;
		}
		
		
	}
}