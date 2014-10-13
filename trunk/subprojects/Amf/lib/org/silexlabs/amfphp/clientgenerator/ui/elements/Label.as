package org.silexlabs.amfphp.clientgenerator.ui.elements
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	
	/**
	 * a quick base class for a label based on a movie clip
	 * */
	public class Label extends MovieClip implements ILabel
	{
		
		public var textField:TextField;
		
		private var _label:String;
		public function Label()
		{
			super();
			mouseChildren = false;
			textField.autoSize = TextFieldAutoSize.LEFT;
		}
		
		
		public function get label():String
		{
			return _label;
		}
		
		public function set label(value:String):void
		{
			_label = value;
			textField.text = _label;
		}
		
		public function get displayObject():DisplayObject{
			return this;
		}
	}
}