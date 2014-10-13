/**
 * VERSION: 12.0.2
 * DATE: 2013-04-09
 * AS3
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
	
	import flash.display.MovieClip;
/**
 * [AS3/AS2 only] Tweens a MovieClip forward to a particular frame number, wrapping it if/when it reaches the end
 * of the timeline. For example, if your MovieClip has 20 frames total and it is currently at frame 10
 * and you want tween to frame 5, a normal frame tween would go backwards from 10 to 5, but a frameForward
 * would go from 10 to 20 (the end) and wrap to the beginning and continue tweening from 1 to 5. 
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.TweenLite; 
import com.greensock.plugins.~~; 
TweenPlugin.activate([FrameForwardPlugin]); //activation is permanent in the SWF, so this line only needs to be run once.

TweenLite.to(mc, 1, {frameForward:5}); 
</listing>
 * 
 * <p>Note: When tweening the frames of a MovieClip, any audio that is embedded on the MovieClip's timeline (as "stream") will not be played. 
 * Doing so would be impossible because the tween might speed up or slow down the MovieClip to any degree.</p>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class FrameForwardPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		
		/** @private **/
		protected var _start:int;
		/** @private **/
		protected var _change:int;
		/** @private **/
		protected var _max:uint;
		/** @private **/
		protected var _target:Object;
		/** @private Allows FrameBackwardPlugin to extend this class and only use an extremely small amount of kb (because the functionality is combined here) **/
		protected var _backward:Boolean;
		
		/** @private **/
		public function FrameForwardPlugin() {
			super("frameForward,frame,frameLabel,frameBackward");
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			_target = target;
			_start = _target.currentFrame;
			_max = _target.totalFrames;
			_change = (typeof(value) === "number") ? Number(value) - _start : (typeof(value) === "string" && value.charAt(1) === "=") ? int(value.charAt(0) + "1") * Number(value.substr(2)) : Number(value) || 0;
			if (!_backward && _change < 0) {
				_change = ((_change + (_max * 99999)) % _max) + ((_change / _max) | 0) * _max;
			} else if (_backward && _change > 0) {
				_change = ((_change - (_max * 99999)) % _max) - ((_change / _max) | 0) * _max;
			}
			return true;
		}
		
		/** @private **/
		override public function setRatio(v:Number):void {
			var frame:Number = (_change * v + _start) % _max;
			if (frame < 0.5 && frame >= -0.5) {
				frame = _max;
			} else if (frame < 0) {
				frame += _max;
			}
			frame = (frame + 0.5) | 0;
			if (frame != _target.currentFrame) {
				_target.gotoAndStop( frame );
			}
		}

	}
}