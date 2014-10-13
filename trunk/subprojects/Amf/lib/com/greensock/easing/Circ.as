/**
 * VERSION: 1.0
 * DATE: 2012-03-22
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * Eases with an abrupt change in velocity either at the beginning (easeIn), the end (easeOut), or both (easeInOut). 
 * <code>Circ</code> is a convenience class that congregates the 3 types of Circ eases (CircIn, CircOut, 
 * and CircInOut) as static properties so that they can be referenced using the standard synatax, like 
 * <code>Circ.easeIn</code>, <code>Circ.easeOut</code>, and <code>Circ.easeInOut</code>. 
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	final public class Circ {
		
		/** Eases out with an abrupt change in velocity. **/
		public static var easeOut:CircOut = new CircOut();
		
		/** Eases in with an abrupt change in velocity. **/
		public static var easeIn:CircIn = new CircIn();
		
		/** Eases in and out with an abrupt change in velocity. **/
		public static var easeInOut:CircInOut = new CircInOut();
	}
}