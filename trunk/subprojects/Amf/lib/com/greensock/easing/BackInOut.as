/**
 * VERSION: 1.0
 * DATE: 2012-03-22
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * @private
 * Eases in and out with an overshoot, initially dipping below the starting value before accelerating towards the end, overshooting it and easing out.
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	final public class BackInOut extends Ease {
		
		/** The default ease instance which can be reused many times in various tweens in order to conserve memory and improve performance slightly compared to creating a new instance each time. **/
		public static var ease:BackInOut = new BackInOut();

		/**
		 * Constructor
		 * 
		 * @param overshoot affects the degree or strength of the overshoot (default: 1.70158)
		 */
		public function BackInOut(overshoot:Number=1.70158) {
			_p1 = overshoot;
			_p2 = _p1 * 1.525;
		}
		
		/** @inheritDoc **/
		override public function getRatio(p:Number):Number {
			return ((p*=2) < 1) ? 0.5 * p * p * ((_p2 + 1) * p - _p2) : 0.5 * ((p -= 2) * p * ((_p2 + 1) * p + _p2) + 2);
		}
		
		/**
		 * Permits customization of the ease with various parameters.
		 * 
		 * @param overshoot affects the degree or strength of the overshoot	(default: 1.70158)
		 * @return new BackInOut instance that is configured according to the parameters provided
		 */
		public function config(overshoot:Number=1.70158):BackInOut {
			return new BackInOut(overshoot);
		}
	
	}
	
}
