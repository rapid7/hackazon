package com.greensock.events {
	import flash.events.Event;
/**
 * Used for dispatching events from the GreenSock Animation Platform. 
 * 	  
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class TweenEvent extends Event {
		/** @private **/
		public static const VERSION:Number = 12.0;
		public static const START:String = "start";
		public static const UPDATE:String = "change";
		public static const COMPLETE:String = "complete";
		public static const REVERSE_COMPLETE:String = "reverseComplete";
		public static const REPEAT:String = "repeat";
		
		public function TweenEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) {
			super(type, bubbles, cancelable);
		}
		
		public override function clone():Event {
			return new TweenEvent(this.type, this.bubbles, this.cancelable);
		}
	
	}
	
}