package org.silexlabs.amfphp.clientgenerator.ui.elements
{
	import flash.display.DisplayObject;
	import flash.events.IEventDispatcher;
	
	public interface ILabel extends IEventDispatcher
	{
		function get displayObject():DisplayObject;
		function get label():String;
		function set label(value:String):void;		
	}
}