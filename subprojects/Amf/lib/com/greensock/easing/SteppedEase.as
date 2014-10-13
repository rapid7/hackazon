/**
 * VERSION: 0.5
 * DATE: 2010-11-30
 * AS3
 * UPDATES AND DOCS AT: http://www.greensock.com/
 **/
package com.greensock.easing {
/**
 * Most easing equations give a smooth, gradual transition between the start and end values, but SteppedEase provides
 * an easy way to define a specific number of steps that the transition should take. For example, if mc.x is 0 and you 
 * want to tween it to 100 with 5 steps (20, 40, 60, 80, and 100) over the course of 2 seconds, you'd do:
 * 
 * <listing version="3.0">
TweenLite.to(mc, 2, {x:100, ease:SteppedEase.config(5)});

//or create an instance directly
var steppedEase = new SteppedEase(5);
TweenLite.to(mc, 3, {y:300, ease:steppedEase});
</listing>
 * 
 * <p>Note: SteppedEase is optimized for use with the GreenSock tweenining platform, so it isn't intended to be used with other engines. 
 * Specifically, its easing equation always returns values between 0 and 1.</p>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */	 
	public class SteppedEase extends Ease {
		/** @private **/
		private var _steps:int;
		
		/**
		 * Constructor
		 * 
		 * @param steps Number of steps between the start and the end values. 
		 */
		public function SteppedEase(steps:int) {
			_p1 = 1 / steps;
			_steps = steps + 1;
		}
		
		/**
		 * @private
		 * Deprecated
		 * This static function provides a quick way to create a SteppedEase and immediately reference its ease function 
		 * in a tween, like:<br /><br /><code>
		 * 
		 * TweenLite.to(mc, 2, {x:100, ease:SteppedEase.create(5)});<br />
		 * </code>
		 * 
		 * @param steps Number of steps between the start and the end values. 
		 * @return The easing function that can be plugged into a tween
		 */
		public static function create(steps:int):SteppedEase {
			return new SteppedEase(steps);
		}
		
		/**
		 * Translates the tween's progress ratio into the corresponding ease ratio. This is the heart of the Ease, where it does all its work.
		 * 
		 * @param p progress ratio (a value between 0 and 1 indicating the progress of the tween/ease)
		 * @return translated number
		 */
		override public function getRatio(p:Number):Number {
			if (p < 0) {
				p = 0;
			} else if (p >= 1) {
				p = 0.999999999;
			}
			return ((_steps * p) >> 0) * _p1;
		}
		
		/**
		 * Permits customization of the ease (defining a number of steps). 
		 * 
		 * @param steps Number of steps between the start and the end values. 
		 * @return new SteppedEase instance that is configured according to the parameters provided
		 */
		public static function config(steps:int):SteppedEase {
			return new SteppedEase(steps);
		}
		
		/** @private Deprecated - Number of steps between the start and the end values. **/
		public function get steps():int {
			return _steps - 1;
		}

	}
}
