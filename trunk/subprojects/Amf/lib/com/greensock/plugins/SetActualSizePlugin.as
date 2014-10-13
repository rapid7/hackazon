/**
 * VERSION: 12.0
 * DATE: 2012-01-14
 * A3S
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.*;
/**
 * [AS3 only] Some components require resizing with setActualSize() instead of standard tweens of width/height in
 * order to scale properly. The SetActualSizePlugin accommodates this easily. You can define the width, 
 * height, or both. <br /><br />
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.TweenLite; 
import com.greensock.plugins.TweenPlugin; 
import com.greensock.plugins.SetActualSizePlugin; 
TweenPlugin.activate([SetActualSizePlugin]); //activation is permanent in the SWF, so this line only needs to be run once.

TweenLite.to(myComponent, 1, {setActualSize:{width:200, height:30}});
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class SetActualSizePlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		
		/** @private **/
		public var width:Number;
		/** @private **/
		public var height:Number;
		
		/** @private **/
		protected var _target:Object;
		/** @private **/
		protected var _setWidth:Boolean;
		/** @private **/
		protected var _setHeight:Boolean;
		/** @private **/
		protected var _hasSetSize:Boolean;
		
		/** @private **/
		public function SetActualSizePlugin() {
			super("setActualSize,setSize,width,height,scaleX,scaleY");
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			_target = target;
			_hasSetSize = Boolean("setActualSize" in _target);
			if ("width" in value && _target.width != value.width) {
				_addTween((_hasSetSize) ? this : _target, "width", _target.width, value.width, "width", true);
				_setWidth = _hasSetSize;
			}
			if ("height" in value && _target.height != value.height) {
				_addTween((_hasSetSize) ? this : _target, "height", _target.height, value.height, "height", true);
				_setHeight = _hasSetSize;
			}
			if (_firstPT == null) { //protects against occassions when the tween's start and end values are the same.
				_hasSetSize = false;
			}	
			return true;
		}
		
		/** @private **/
		override public function _kill(lookup:Object):Boolean {
			if ("width" in lookup || "scaleX" in lookup) {
				_setWidth = false;
			}
			if ("height" in lookup || "scaleY" in lookup) {
				_setHeight = false;
			}
			return super._kill(lookup);
		}
				
		/** @private **/
		override public function setRatio(v:Number):void {
			super.setRatio(v);
			if (_hasSetSize) {
				_target.setActualSize((_setWidth) ? this.width : _target.width, (_setHeight) ? this.height : _target.height);
			}
		}
		

	}
}