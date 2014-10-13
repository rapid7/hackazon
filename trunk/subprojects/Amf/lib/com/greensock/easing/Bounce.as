/**
 * VERSION: 1.0
 * DATE: 2012-03-22
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * Eases, bouncing either at the beginning (easeIn), the end (easeOut), or both (easeInOut). 
 * <code>Bounce</code> is a convenience class that congregates the 3 types of Bounce eases (BounceIn, BounceOut, 
 * and BounceInOut) as static properties so that they can be referenced using the standard synatax, like 
 * <code>Bounce.easeIn</code>, <code>Bounce.easeOut</code>, and <code>Bounce.easeInOut</code>. 
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	final public class Bounce {
		
		/** Eases out, bouncing at the end. **/
		public static var easeOut:BounceOut = new BounceOut();
		
		/** Bounces slightly at first, then to a greater degree over time, accelerating as the ease progresses. **/
		public static var easeIn:BounceIn = new BounceIn();

		/** Bounces in increasing degree towards the center of the ease, then eases out, bouncing to the end (decreasing in degree at the end). **/
		public static var easeInOut:BounceInOut = new BounceInOut();
	}
}