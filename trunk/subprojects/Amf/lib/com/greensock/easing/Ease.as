/**
 * VERSION: 0.5
 * DATE: 2012-01-31
 * AS3 (AS2 version is also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * Base class for all GreenSock easing equations. In its simplest form, an Ease
 * is responsible for translating linear time (typically represented as a number
 * between 0 and 1 where 0 is the beginning, 0.5 is halfway complete, and 1 is 
 * the end) into a value that has a different rate of change but still starts
 * at 0 and ends at 1. In the GreenSock platform, eases are used to give 
 * tweens/animations the look and feel that the animator desires. For example, 
 * a ball rolling to a stop would decelerate over time (easeOut) rather than using 
 * a linear velocity. An Elastic ease could be used to make an object appear as 
 * though it is loosely attached somewhere and is snapping into place with loose 
 * (or tight) tension. 
 * 
 * <p>All Ease instances have a <code>getRatio()</code> method that is responsible
 * for the translation of the progress ratio which the tween typically feeds in. 
 * End users almost never need to directly feed any values to or get any values from
 * an Ease instance - the tweens will do that internally.</p>
 * 
 * <p>The base Ease class handles most of the common power-based easeIn/easeOut/eaesInOut 
 * calculations (like Linear, Quad, Cubic, Quart, Quint, and Strong) internally. 
 * You can define a separate function that uses what was considered the 4 standard 
 * easing parameters by Adobe and many others (time, start, change, duration) and 
 * Ease will serve as a proxy in order to maximize backwards compatibility and usability. 
 * For example, if you have a custom method that you created like this:</p>
 * <listing version="3.0">
function myEase(t:Number, s:Number, c:Number, d:Number):Number {
    return s+(t=t/d)*t*t*t*c;
}
</listing>
 * You could still use that by wrapping Ease around it like this:
 * <listing version="3.0">
import com.greensock.~~;
import com.greensock.easing.~~;
 
TweenLite.to(mc, 5, {x:600, ease:new Ease(myEase)});
</listing>
 * <p>In the above example, the anytime the Ease's <code>getRatio()</code> method is called, it
 * would feed the first parameter as a ratio between 0 and 1 and the rest of the 3 parameters 
 * would always be 0, 1, 1. This is all done transparently by TweenLite/TweenMax, so you 
 * really shouldn't need to worry about this.</p>
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	public class Ease {
		/** @private **/
		protected static var _baseParams:Array = [0, 0, 1, 1];
		/** @private **/
		protected var _func:Function;
		/** @private **/
		protected var _params:Array;
		/** @private **/
		protected var _p1:Number;
		/** @private **/
		protected var _p2:Number;
		/** @private **/
		protected var _p3:Number;
		/** @private integer indicating the type of ease where 1 is easeOut, 2 is easeIn, 3 is easeInOut, and 0 is none of these. **/
		public var _type:int;
		/** @private power of the ease where Linear is 0, Quad is 1, Cubic is 2, Quart is 3, Quint (and Strong) is 4, etc. **/
		public var _power:int;
		/** @private if true, TweenLite/Max will call setRatio() at the end and beginning of the tween instead of assuming it's 1/0. This is only useful in very rare situations like in a SlowMo ease that uses endcapMode=true which will have a 0 ratio at the end of the tween. **/
		public var _calcEnd:Boolean;
		
		/**
		 * Constructor
		 * 
		 * @param func Function (if any) that should be proxied. This is completely optional and is in fact rarely used except when you have your own custom ease function that follows the standard ease parameter pattern like time, start, change, duration.
		 * @param extraParams If any extra parameters beyond the standard 4 (time, start, change, duration) need to be fed to the <code>func</code> function, define them as an array here. For example, the old Elastic.easeOut accepts 2 extra parameters in its standard equation (although the newer GreenSock version uses the more modern <code>config()</code> method for configuring the ease and doesn't require any extraPrams here)
		 * @param type Integer indicating the type of ease where 1 is easeOut, 2 is easeIn, 3 is easeInOut, and 0 is none of these. 
		 * @param power Power of the ease where Linear is 0, Quad is 1, Cubic is 2, Quart is 3, Quint (and Strong) is 4, etc.
		 */
		public function Ease(func:Function=null, extraParams:Array=null, type:Number=0, power:Number=0) {
			_func = func;
			_params = (extraParams) ? _baseParams.concat(extraParams) : _baseParams;
			_type = type;
			_power = power;
		}
		
		/**
		 * Translates the tween's progress ratio into the corresponding ease ratio. This is the heart of the Ease, where it does all its work.
		 * 
		 * @param p progress ratio (a value between 0 and 1 indicating the progress of the tween/ease)
		 * @return translated number
		 */
		public function getRatio(p:Number):Number {
			if (_func != null) {
				_params[0] = p;
				return _func.apply(null, _params);
			} else {
				var r:Number = (_type == 1) ? 1 - p : (_type == 2) ? p : (p < 0.5) ? p * 2 : (1 - p) * 2;
				if (_power == 1) {
					r *= r;
				} else if (_power == 2) {
					r *= r * r;
				} else if (_power == 3) {
					r *= r * r * r;
				} else if (_power == 4) {
					r *= r * r * r * r;
				}
				return (_type == 1) ? 1 - r : (_type == 2) ? r : (p < 0.5) ? r / 2 : 1 - (r / 2);
			}
		}
		
	}
}
