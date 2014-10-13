/**
 * VERSION: 1.0
 * DATE: 2012-03-22
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * Eases in a strong fashion either at the beginning (easeIn), the end (easeOut), or both (easeInOut). 
 * <code>Expo</code> is a convenience class that congregates the 3 types of Expo eases (ExpoIn, ExpoOut, 
 * and ExpoInOut) as static properties so that they can be referenced using the standard synatax, like 
 * <code>Expo.easeIn</code>, <code>Expo.easeOut</code>, and <code>Expo.easeInOut</code>. 
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	final public class Expo {
		
		/** 
		 * Eases out in a strong fashion starting out fast and then decelerating. Produces an effect similar to the 
		 * popular "Zeno's paradox" style of scripted easing, where each interval of time decreases the remaining 
		 * distance by a constant proportion. 
		 **/
		public static var easeOut:ExpoOut = new ExpoOut();
		
		/** 
		 * Eases in a strong fashion starting out slowly and then accelerating. Produces an effect similar to the 
		 * popular "Zeno's paradox" style of scripted easing, where each interval of time decreases the remaining 
		 * distance by a constant proportion. 
		 **/
		public static var easeIn:ExpoIn = new ExpoIn();
		
		/** 
		 * Eases in a strong fashion starting out slowly and then accelerating, then decelerating at the end. 
		 * Produces an effect similar to the popular "Zeno's paradox" style of scripted easing, where each 
		 * interval of time decreases the remaining distance by a constant proportion.
		 **/
		public static var easeInOut:ExpoInOut = new ExpoInOut();
	}
}