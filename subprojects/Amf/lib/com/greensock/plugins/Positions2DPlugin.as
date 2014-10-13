/**
 * VERSION: 12.0
 * DATE: 2012-01-14
 * AS3 
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
/**
 * [AS3/AS2 only] This plugin allows you to define an array of Points at which the target should be positioned during the course of
 * the tween (in order). So if 4 Points are in the array, the target will be rendered at the first Point's x/y at the 
 * beginning of the tween, then at around 25% through the tween, it will jump to the 2nd Point's position, etc. until
 * it arrives at the last Point's position. The array can be populated with any object that has x and y properties 
 * (they don't need to be Points - they could be generic Objects).
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.TweenLite; 
import com.greensock.plugins.TweenPlugin; 
import com.greensock.plugins.Positions2DPlugin;
TweenPlugin.activate([Positions2DPlugin]); //activation is permanent in the SWF, so this line only needs to be run once

TweenLite.to(mc, 3, {positions2D:[{x:250, y:50}, {x:500, y:0}]}); 
</listing>
 * 
 * <p><strong>Copyright 2008-2013, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class Positions2DPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		
		/** @private **/
		protected var _target:Object;
		/** @private **/
		protected var _positions:Array;
		
		/** @private **/
		public function Positions2DPlugin() {
			super("positions2D,x,y");
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			if (!(value is Array)) {
				return false;
			}
			_target = target;
			_positions = value as Array;
			return true;
		}	
		
		/** @private **/
		override public function setRatio(v:Number):void {
			if (v < 0) {
				v = 0;
			} else if (v >= 1) {
				v = 0.999999999;
			}
			var position:Object = _positions[ int(_positions.length * v) ];
			_target.x = position.x;
			_target.y = position.y;
		}
		
	}
}