/**
 * VERSION: 12.0
 * DATE: 2012-01-12
 * AS3
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
	import flash.filters.BevelFilter;
/**
 * [AS3/AS2 only] Tweens a BevelFilter. The following properties are available (you only need to define the ones you want to tween): 
 * <code>
 * <ul>
 * 		<li> distance : Number [0]</li>
 * 		<li> angle : Number [0]</li>
 * 		<li> highlightColor : uint [0xFFFFFF]</li>
 * 		<li> highlightAlpha : Number [0.5]</li>
 * 		<li> shadowColor : uint [0x000000]</li>
 * 		<li> shadowAlpha :Number [0.5]</li>
 * 		<li> blurX : Number [2]</li>
 * 		<li> blurY : Number [2]</li>
 * 		<li> strength : Number [0]</li>
 * 		<li> quality : uint [2]</li>
 * 		<li> index : uint</li>
 * 		<li> addFilter : Boolean [false]</li>
 * 		<li> remove : Boolean [false]</li>
 * </ul>
 * </code>
 * 
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.TweenLite;
import com.greensock.plugins.TweenPlugin; 
import com.greensock.plugins.BevelFilterPlugin; 
TweenPlugin.activate([BevelFilterPlugin]); //activation is permanent in the SWF, so this line only needs to be run once.

TweenLite.to(mc, 1, {bevelFilter:{blurX:10, blurY:10, distance:6, angle:45, strength:1}});
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class BevelFilterPlugin extends FilterPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		/** @private **/
		private static var _propNames:Array = ["distance","angle","highlightColor","highlightAlpha","shadowColor","shadowAlpha","blurX","blurY","strength","quality"];
		
		/** @private **/
		public function BevelFilterPlugin() {
			super("bevelFilter");
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			return _initFilter(target, value, tween, BevelFilter, new BevelFilter(0, 0, 0xFFFFFF, 0.5, 0x000000, 0.5, 2, 2, 0, value.quality || 2), _propNames);
		}
		
	}
}