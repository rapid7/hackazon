/**
 * VERSION: 12.01
 * DATE: 2012-07-28
 * AS3 
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.*;
	import com.greensock.core.*;
	
	import flash.display.*;
	import flash.geom.ColorTransform;
	import flash.geom.Transform;
/**
 * [AS3/AS2 only] To change a DisplayObject's tint/color, set this to the hex value of the tint you'd like
 * to end up at (or begin at if you're using <code>TweenMax.from()</code>). An example hex value would be <code>0xFF0000</code>.
 * 
 * <p>To remove a tint completely, set the tint to <code>null</code></p>
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.TweenLite;
import com.greensock.plugins.TweenPlugin; 
import com.greensock.plugins.TintPlugin; 
TweenPlugin.activate([TintPlugin]); //activation is permanent in the SWF, so this line only needs to be run once.

TweenLite.to(mc, 1, {tint:0xFF0000}); 
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class TintPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		/** @private **/
		protected static var _props:Array = ["redMultiplier","greenMultiplier","blueMultiplier","alphaMultiplier","redOffset","greenOffset","blueOffset","alphaOffset"];
		
		/** @private **/
		protected var _transform:Transform;
		
		/** @private **/
		public function TintPlugin() {
			super("tint,colorTransform,removeTint");
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			if (!(target is DisplayObject)) {
				return false;
			}
			var end:ColorTransform = new ColorTransform();
			if (value != null && tween.vars.removeTint != true) {
				end.color = uint(value);
			}
			_transform = DisplayObject(target).transform;
			var ct:ColorTransform = _transform.colorTransform;
			end.alphaMultiplier = ct.alphaMultiplier;
			end.alphaOffset = ct.alphaOffset;
			_init(ct, end);
			return true;
		}
		
		/** @private **/
		public function _init(start:ColorTransform, end:ColorTransform):void {
			var i:int = _props.length, 
				p:String;
			while (--i > -1) {
				p = _props[i];
				if (start[p] != end[p]) {
					_addTween(start, p, start[p], end[p], "tint");
				}
			}
		}
		
		/** @private **/
		override public function setRatio(v:Number):void {
			var ct:ColorTransform = _transform.colorTransform, //don't just use _ct because if alpha changes are made separately, they won't get applied properly.
				pt:PropTween = _firstPT;
			while (pt) {
				ct[pt.p] = pt.c * v + pt.s;
				pt = pt._next;
			}
			_transform.colorTransform = ct;
		}
		
	}
}