/**
 * VERSION: 1.0.0
 * DATE: 2013-03-27
 * AS3 
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing.core {
/**
 * @private
 * Used by RoughEase. Couldn't use an internal class due to instantiation order issues caused by referencing an EasePoint inside the RoughEase constructor when we create an "ease" public static var that's a RoughEase. 
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	final public class EasePoint {
		public var time:Number;
		public var gap:Number;
		public var value:Number;
		public var change:Number;
		public var next:EasePoint;
		public var prev:EasePoint;
		
		public function EasePoint(time:Number, value:Number, next:EasePoint) {
			this.time = time;
			this.value = value;
			if (next) {
				this.next = next;
				next.prev = this;
				this.change = next.value - value;
				this.gap = next.time - time;
			}
		}
	}
}