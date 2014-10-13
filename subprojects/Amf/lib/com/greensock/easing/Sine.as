/**
 * VERSION: 1.0
 * DATE: 2012-03-22
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * Eases with a relatively low power either at the beginning (easeIn), the end (easeOut), or both (easeInOut). 
 * <code>Sine</code> is a convenience class that congregates the 3 types of Sine eases (SineIn, SineOut, 
 * and SineInOut) as static properties so that they can be referenced using the standard synatax, like 
 * <code>Sine.easeIn</code>, <code>Sine.easeOut</code>, and <code>Sine.easeInOut</code>. 
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	final public class Sine {
		
		/** Eases out with slight deceleration. **/
		public static var easeOut:SineOut = new SineOut();
		
		/** Eases in with slight acceleration. **/
		public static var easeIn:SineIn = new SineIn();
		
		/** Eases in and then out with slight acceleration/deceleration. **/
		public static var easeInOut:SineInOut = new SineInOut();
	}
}