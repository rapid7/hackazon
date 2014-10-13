/**
 * VERSION: 1.0
 * DATE: 2012-03-22
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * Eases with an overshoot either at the beginning (easeIn), the end (easeOut), or both (easeInOut). 
 * <code>Back</code> is a convenience class that congregates the 3 types of Back eases (BackIn, BackOut, 
 * and BackInOut) as static properties so that they can be referenced using the standard synatax, like 
 * <code>Back.easeIn</code>, <code>Back.easeOut</code>, and <code>Back.easeInOut</code>. 
 * 
 * <p>You can configure the amount of overshoot using the <code>config()</code> method, like
 * <code>TweenLite.to(obj, 1, {x:100, ease:Back.easeOut.config(3)});</code></p>
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	final public class Back {
		
		/** Eases out with an overshoot. **/
		public static var easeOut:BackOut = new BackOut();
		
		/** Eases in with an overshoot, initially dipping below the starting value before accelerating towards the end. **/
		public static var easeIn:BackIn = new BackIn();
		
		/** Eases in and out with an overshoot, initially dipping below the starting value before accelerating towards the end, overshooting it and easing out. **/
		public static var easeInOut:BackInOut = new BackInOut();
	}
}
