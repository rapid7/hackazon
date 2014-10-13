/**
 * VERSION: 12.0
 * DATE: 2012-01-14
 * AS3
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
	import flash.media.SoundTransform;
/**
 * [AS3 only] Tweens properties of an object's soundTransform property (like the volume, pan, leftToRight, etc. of a MovieClip/SoundChannel/NetStream). <br /><br />
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.TweenLite; 
import com.greensock.plugins.TweenPlugin; 
import com.greensock.plugins.SoundTransformPlugin; 
TweenPlugin.activate([SoundTransformPlugin]); //activation is permanent in the SWF, so this line only needs to be run once.

TweenLite.to(mc, 1, {soundTransform:{volume:0.2, pan:0.5}}); 
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class SoundTransformPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		
		/** @private **/
		protected var _target:Object;
		/** @private **/
		protected var _st:SoundTransform;
		
		/** @private **/
		public function SoundTransformPlugin() {
			super("soundTransform,volume");
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			if (!target.hasOwnProperty("soundTransform")) {
				return false;
			}
			_target = target;
			_st = _target.soundTransform;
			for (var p:String in value) {
				_addTween(_st, p, _st[p], value[p], p);
			}
			return true;
		}
		
		/** @private **/
		override public function setRatio(v:Number):void {
			super.setRatio(v);
			_target.soundTransform = _st;
		}
		
	}
}