package org.silexlabs.amfphp.clientgenerator.ui.elements
{
	import flash.display.DisplayObject;

	/**
	 * interface for all objects that are used for data input / output with the user.
	 * */
	public interface IDataIoGui
	{
		function get displayObject():DisplayObject;
		function get data():Object;
		function set data(value:Object):void;
		
	}
}