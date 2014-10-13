/**
 * VERSION: 12.0.0
 * DATE: 2012-02-23
 * AS3
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.core {
/**
 * @private
 * Stores information about an individual property tween. There is no reason to use this class directly - TweenLite, TweenMax, and some plugins use it internally.
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */	
	final public class PropTween {
		/** Target object **/
		public var t:Object;
		/** Name of the property that is being tweened on the target (for plugins, this is always "setRatio", but the actual property name of the orignal target is stored in the "n" property of the PropTween instance) **/
		public var p:String;
		/** Starting value  **/
		public var s:Number;
		/** Amount to change (basically, the difference between the starting value and ending value) **/
		public var c:Number;
		/** Indicates whether or not the target's property that is being tweened is a function (true) or not (false). If it's a function, it must be set with t.p(value) rather than t.p = value. **/
		public var f:Boolean;
		/** Priority in the rendering queue. The lower the value the later it will be tweened. The default value is 0, but some plugins must be rendered later (or earlier). **/
		public var pr:int;
		/** Indicates whether or not the target is a TweenPlugin. **/
		public var pg:Boolean;
		/** The name associated with the original target property. Typically this is the same as PropTween.p but for TweenPlugin tweens it is often different. For example an autoAlpha tween would create a PropTween of the AutoAlphaPlugin instance and p would be "setRatio", but n would be "autoAlpha". **/
		public var n:String;
		/** If <code>true</code>, the property should be rounded. **/
		public var r:Boolean;
		/** Next PropTween in the linked list **/
		public var _next:PropTween;
		/** Previous PropTween in the linked list **/
		public var _prev:PropTween;
		
		/**
		 * Constructor
		 * 
		 * @param target Target object
		 * @param property Name of the property that is being tweened on the target (for plugins, this is always "setRatio", but the actual property name of the orignal target is stored in the "n" property of the PropTween instance)
		 * @param start Starting value
		 * @param change Amount to change (basically, the difference between the starting value and ending value)
		 * @param name The name associated with the original target property. Typically this is the same as PropTween.p but for TweenPlugin tweens it is often different. For example an autoAlpha tween would create a PropTween of the AutoAlphaPlugin instance and p would be "setRatio", but n would be "autoAlpha".
		 * @param isPlugin Indicates whether or not the target is a TweenPlugin.
		 * @param nextNode Next PropTween in the linked list
		 * @param priority Priority in the rendering queue. The lower the value the later it will be tweened. The default value is 0, but some plugins must be rendered later (or earlier).
		 */
		public function PropTween(target:Object, property:String, start:Number, change:Number, name:String, isPlugin:Boolean, next:PropTween=null, priority:int=0) {
			this.t = target;
			this.p = property;
			this.s = start;
			this.c = change;
			this.n = name;
			this.f = (target[property] is Function);
			this.pg = isPlugin;
			if (next) {
				next._prev = this;
				this._next = next;
			}
			this.pr = priority;
		}
	}
}