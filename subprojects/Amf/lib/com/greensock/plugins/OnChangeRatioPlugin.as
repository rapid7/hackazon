/**
 * VERSION: 12.0
 * DATE: 2012-01-14
 * AS3
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
/**
 * [AS3/AS2 only] This plugin allows you to define a callback method that should be called whenever the tween's "ratio" property
 * changes which effectively means that the tweening values changed. This is typically only useful in conjunction with
 * <code>SteppedEase</code>. Also note that the callback should accept one parameter which will refer to the tween itself.
 * This is different than most other callback types, like onComplete and onUpdate which don't pass parameters by default
 * unless you use their onCompleteParams and onUpdateParams counterparts. The reason onChangeRatio works this unique way
 * is to minimize file size (adding an onChangeRatioParams would require either another plugin or adding kb to the 
 * main TweenLite class or changing the syntax altogether to onChangeRatio:{func:myFunction, params:[1,2]} which is
 * even more inconsistent) and because it is such a niche plugin (typically only used with SteppedEase which is quite
 * niche itself). It can be very useful to reuse a single callback method but it must be able to figure out which tween
 * changed its ratio and access its target which is why onChangeRatio passes the tween as the parameter. 
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.TweenLite; 
import com.greensock.plugins.TweenPlugin; 
import com.greensock.plugins.OnChangeRatioPlugin; 
TweenPlugin.activate([OnChangeRatioPlugin]); //activation is permanent in the SWF, so this line only needs to be run once.

TweenLite.to(mc, 3, {x:500, onChangeRatio:changeHandler, ease:SteppedEase.create(5)}); 
function changeHandler(tween:TweenLite):void {
		trace("ratio: " + tween.ratio);
}
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class OnChangeRatioPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		
		/** @private **/
		protected var _func:Function;
		/** @private **/
		protected var _tween:TweenLite;
		/** @private **/
		private var _ratio:Number;
		
		/** @private **/
		public function OnChangeRatioPlugin() {
			super("onChangeRatio");
			_ratio = 0;
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			if (!(value is Function)) {
				return false;
			}
			_func = value as Function;
			_tween = tween;
			return true;
		}	
		
		/** @private **/
		override public function setRatio(v:Number):void {
			if (_ratio != v) {
				_func(_tween);
				_ratio = v;
			}
		}
		
	}
}