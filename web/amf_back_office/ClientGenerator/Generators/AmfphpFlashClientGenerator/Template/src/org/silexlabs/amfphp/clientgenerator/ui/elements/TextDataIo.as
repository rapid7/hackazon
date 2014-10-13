package org.silexlabs.amfphp.clientgenerator.ui.elements
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.text.TextField;
	
	/**
	 * link your skin in the library with this class. 
	 * It must contain a text field named 'textField'.
	 * */
	public class TextDataIo extends MovieClip implements IDataIoGui
	{
		public var textField:TextField;
		
		public function TextDataIo()
		{
			super();
		}
		
		
		public function get data():Object{
			return textField.text;
		}
		
		public function set data(value:Object):void{
			textField.text = value as String;
		}
		
		public function get displayObject():DisplayObject{
			return this;
		}		
	}
}