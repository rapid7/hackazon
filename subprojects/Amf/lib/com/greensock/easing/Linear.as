/**
 * VERSION: 1.0
 * DATE: 2012-03-22
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * Linear ease with no acceleration or deceleration. Linear is identical to <code>Power0</code>. 
 * 
 * <p>This is one of the eases that is natively accelerated in TweenLite and TweenMax. All of the 
 * "Power" eases and their counterparts (Linear (0), Quad (1), Cubic (2), Quart (3), Quint (4), and Strong (4)) are 
 * accelerated.</p>
 * 
 * <p><strong>Example usage:</strong></p>
 * <p><code>
 * TweenLite.to(obj, 1, {x:100, ease:Linear.easeNone});
 * </code></p>
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	final public class Linear extends Ease {
		
		/** Linear ease with no acceleration or deceleration (for backwards compatibility) **/
		public static var easeNone:Linear = new Linear();
		
		/** The default ease instance which can be reused many times in various tweens in order to conserve memory and improve performance slightly compared to creating a new instance each time. **/
		public static var ease:Linear = easeNone;
		
		/** Linear ease with no acceleration or deceleration **/
		public static var easeIn:Linear = easeNone;
		
		/** Linear ease with no acceleration or deceleration **/
		public static var easeOut:Linear = easeNone;
		
		/** Linear ease with no acceleration or deceleration **/
		public static var easeInOut:Linear = easeNone;
		
		/** Constructor **/
		public function Linear() {
			super(null, null, 1, 0);
		}
		
	}
}