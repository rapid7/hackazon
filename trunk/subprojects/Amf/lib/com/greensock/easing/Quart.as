/**
 * VERSION: 1.0
 * DATE: 2012-03-22
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * Provides an <code>easeIn</code>, <code>easeOut</code>, and <code>easeInOut</code> with a power (or strength) 
 * of 2 which is identical to the <code>Power2</code> ease. The more power, the more 
 * exaggerated the easing effect. Using a numeric approach like Power2 instead of Quart makes experimenting 
 * easier and the code reads more intuitively. 
 * 
 * <p>This is one of the eases that is natively accelerated in TweenLite and TweenMax. All of the 
 * "Power" eases and their counterparts (Linear (0), Quad (1), Cubic (2), Quart (3), Quint (4), and Strong (4)) are 
 * accelerated.</p>
 * 
 * <p><strong>Example usage:</strong></p>
 * <p><code>
 * TweenLite.to(obj, 1, {x:100, ease:Quart.easeOut});
 * </code></p>
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	final public class Quart {
		
		/** Eases out with a power of 3. **/
		public static var easeOut:Ease = new Ease(null,null,1,3);
		
		/** Eases in with a power of 3. **/
		public static var easeIn:Ease = new Ease(null,null,2,3);
		
		/** Eases in and then out with a power of 3. **/
		public static var easeInOut:Ease = new Ease(null,null,3,3);
	}
}