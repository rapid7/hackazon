/**
 * VERSION: 12.0
 * DATE: 2012-01-12
 * AS3 
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
/**
 * [AS3/AS2 only, although autoAlpha is recognized inside the CSSPlugin for JavaScript] Tweening "autoAlpha" is 
 * exactly the same as tweening an object's "alpha" except that it ensures that the object's "visible" property 
 * is true until autoAlpha reaches zero at which point it will toggle the "visible" property to false. That not 
 * only improves rendering performance in the Flash Player, but also hides DisplayObjects so that they don't 
 * interact with the mouse. 
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.TweenLite; 
import com.greensock.plugins.TweenPlugin; 
import com.greensock.plugins.AutoAlphaPlugin; 
TweenPlugin.activate([AutoAlphaPlugin]); //activation is permanent in the SWF, so this line only needs to be run once.

TweenLite.to(mc, 2, {autoAlpha:0});
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class AutoAlphaPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		
		/** @private **/
		protected var _target:Object;
		/** @private **/
		protected var _ignoreVisible:Boolean;
		
		/** @private **/
		public function AutoAlphaPlugin() {
			super("autoAlpha,alpha,visible");
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			_target = target;
			_addTween(target, "alpha", target.alpha, value, "alpha");
			return true;
		}
		
		/** @private **/
		override public function _kill(lookup:Object):Boolean {
			_ignoreVisible = ("visible" in lookup);
			return super._kill(lookup);
		}
		
		/** @private **/
		override public function setRatio(v:Number):void {
			super.setRatio(v);
			if (!_ignoreVisible) {
				_target.visible = (_target.alpha != 0);
			}
		}
		
	}
}