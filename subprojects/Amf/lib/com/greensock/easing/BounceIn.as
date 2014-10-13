/**
 * VERSION: 1.0
 * DATE: 2012-03-22
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * @private
 * Bounces slightly at first, then to a greater degree over time, accelerating as the ease progresses.
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	final public class BounceIn extends Ease {
		
		/** The default ease instance which can be reused many times in various tweens in order to conserve memory and improve performance slightly compared to creating a new instance each time. **/
		public static var ease:BounceIn = new BounceIn();
	
		/** @inheritDoc **/
		override public function getRatio(p:Number):Number {
			if ((p = 1 - p) < 1 / 2.75) {
				return 1 - (7.5625 * p * p);
			} else if (p < 2 / 2.75) {
				return 1 - (7.5625 * (p -= 1.5 / 2.75) * p + .75);
			} else if (p < 2.5 / 2.75) {
				return 1 - (7.5625 * (p -= 2.25 / 2.75) * p + .9375);
			} else {
				return 1 - (7.5625 * (p -= 2.625 / 2.75) * p + .984375);
			}
		}
	
	}
	
}
