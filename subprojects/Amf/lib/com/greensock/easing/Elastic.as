/**
 * VERSION: 1.0
 * DATE: 2012-03-22
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * Eases with an elastic effect either at the beginning (easeIn), the end (easeOut), or both (easeInOut). 
 * <code>Elastic</code> is a convenience class that congregates the 3 types of Elastic eases (ElasticIn, ElasticOut, 
 * and ElasticInOut) as static properties so that they can be referenced using the standard synatax, like 
 * <code>Elastic.easeIn</code>, <code>Elastic.easeOut</code>, and <code>Elastic.easeInOut</code>. 
 * 
 * <p>You can configure the amplitude and period of the sine wave using the <code>config()</code> method, like
 * <code>TweenLite.to(obj, 1, {x:100, ease:Elastic.easeOut.config(0.5, 2)});</code></p>
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	final public class Elastic {
		
		/** Eases using a sine wave that starts fast and then decelerates over time. **/
		public static var easeOut:ElasticOut = new ElasticOut();
		
		/** Eases using a sine wave that starts slowly and then accelerates over time **/
		public static var easeIn:ElasticIn = new ElasticIn();
		
		/** Eases using a sine wave that starts slowly, then accelerates and then decelerates over time. **/
		public static var easeInOut:ElasticInOut = new ElasticInOut();
	}
}