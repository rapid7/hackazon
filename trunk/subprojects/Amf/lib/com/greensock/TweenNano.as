/**
 * VERSION: 12.0.0
 * DATE: 2013-01-21
 * AS3 (AS2 is also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock {
	import flash.display.Shape;
	import flash.events.Event;
	import flash.utils.getTimer;
/**
 * [AS3/AS2 only] TweenNano is a super-lightweight (2k in AS3 and 2.6k in AS2) version of <a href="http://www.greensock.com/tweenlite/">TweenLite</a> 
 * and is only recommended for situations where you absolutely cannot afford the extra 4.7k that the normal 
 * TweenLite engine would cost and your project doesn't require any plugins. Normally, it is much better to 
 * use TweenLite because of the additional flexibility it provides via plugins and its compatibility with 
 * TimelineLite and TimelineMax. TweenNano can do everything TweenLite can do with the following exceptions:
 * <ul>
 * 		<li><strong> No Plugins </strong>- One of the great things about TweenLite is that you can activate
 * 			plugins in order to add features (like autoAlpha, tint, blurFilter, etc.). TweenNano, however, 
 * 			doesn't work with plugins. </li>
 * 		  
 * 		<li><strong> Incompatible with TimelineLite and TimelineMax </strong>- Complex sequencing and management 
 * 			of groups of tweens is much easier with TimelineLite and TimelineMax, but TweenNano instances cannot 
 * 			be inserted into TimelineLite or TimelineMax instances.</li>
 * 		  
 * 		  
 * 		<li><strong> Limited overwrite modes </strong>- By default, TweenNano doesn't overwrite any tweens
 * 			but you can pass <code>overwrite:"all"</code> in the <code>vars</code> parameter to have it kill
 * 			all tweens of the same target immediately. TweenLite, however, offers much more robust overwrite
 * 			management, recognizing advanced modes like <code>"auto"</code> (which only overwrites individual 
 * 			tweening properties that overlap), <code>"concurrent"</code>, <code>"allOnStart"</code>, 
 * 			and <code>"preexisting"</code>. See TweenLite's documentation for details.</li>
 * 
 * 		<li><strong> Fewer methods and properties</strong> TweenNano instances aren't meant to be altered 
 * 			on-the-fly, so they don't have methods like <code>pause(), resume(), reverse(), seek(), restart()</code>, etc. 
 * 			The essentials are covered, though, like <code>to(), from(), delayedCall(), killTweensOf()</code>, 
 * 			and <code>kill()</code>.</li>
 * 	</ul>
 * 
 * 
 * <p><strong>USAGE</strong></p>
 * <p>The most common type of tween is a <a href="TweenNano.html#to()">to()</a> tween which allows you 
 * to define the destination values:</p>
 * 
 * <p><code>
 * TweenNano.to(myObject, 2, {x:100, y:200});
 * </code></p>
 * 
 * <p>The above code will tween <code>myObject.x</code> from whatever it currently is to 100 and 
 * <code>myObject.y</code> property to 200 over the course of 2 seconds. Notice the x and y values are 
 * defined inside a generic object (between curly braces). Put as many properties there as you want.</p>
 * 
 * <p>Tweens begin immediately.</p>
 * 
 * <p>The <code>target</code> can also be an array of objects. For example, the following tween will
 * tween the alpha property to 0.5 and y property to 100 for obj1, obj2, and obj3:</p>
 * 
 * <p><code>
 * TweenNano.to([obj1, obj2, obj3], 1, {alpha:0.5, y:100});
 * </code></p>
 * 
 * <p>You can also use a <a href="TweenNano.html#from()">from()</a> tween if you want to define the 
 * <strong>starting</strong> values instead of the ending values so that the target tweens <em>from</em> 
 * the defined values to wherever they currently are.</p>
 * 
 * <p>Although the <code>to()</code> and <code>from()</code> static methods
 * are popular because they're quick and can avoid some garbage collection hassles, you can also
 * use the more object-oriented syntax like this:</p>
 * 
 * <p><code>
 * var tween = new TweenNano(myObject, 2, {x:100, y:200});
 * </code></p>
 * 
 * <p>or even:</p>
 * 
 * <p><code>
 * var tween = TweenNano.to(myObject, 2, {x:100, y:200});
 * </code></p>
 * 
 * 
 * 
 * <p><strong>EXAMPLES:</strong></p>
 * 
 * <p>Please see <a href="http://www.greensock.com/tweennano/">http://www.greensock.com/tweennano/</a> for examples, tutorials, and interactive demos. </p>
 * 
 * 
 * <p><strong>SPECIAL PROPERTIES:</strong></p>
 * <p>Typically the <code>vars</code> parameter is used to define ending values for tweening 
 * properties of the <code>target</code> (or beginning values for <code>from()</code> tweens) 
 * like <code>{x:100, y:200, alpha:0}</code>, but the following optional special properties 
 * serve other purposes:</p>
 * 
 * <ul>
 * 	<li><strong> delay </strong>:<em> Number</em> -
 * 				 Amount of delay in seconds (or frames for frames-based tweens) before the tween should begin.</li>
 * 	
 * 	<li><strong> ease </strong>:<em> Ease (or Function)</em> -
 * 				 You can choose from various eases to control the rate of change during 
 * 				 the animation, giving it a specific "feel". For example, <code>ElasticOut.ease</code> 
 * 				 or <code>StrongInOut.ease</code>. TweenNano works with not only the easing equations
 * 				 in the com.greensock.easing package, but also standard easing equation that uses the 
 * 				 typical 4 parameters (<code>time, start, change, duration</code>) like Adobe's 
 * 				 <code>fl.motion.easing</code> eases. The default is <code>QuadOut.ease</code>. 
 * 				 For linear animation, use the GreenSock <code>Linear.ease</code> ease.</li>
 * 	
 * 	<li><strong> onComplete </strong>:<em> Function</em> -
 * 				 A function that should be called when the tween has completed</li>
 * 	
 * 	<li><strong> onCompleteParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the <code>onComplete</code> function. For example,
 * 				 <code>TweenNano.to(mc, 1, {x:100, onComplete:myFunction, onCompleteParams:[mc, "param2"]});</code></li>
 * 	
 * 	<li><strong> useFrames </strong>:<em> Boolean</em> -
 * 				 If <code>useFrames</code> is <code>true</code>, the tweens's timing will be 
 * 				 based on frames instead of seconds. This causes both its <code>duration</code>
 * 				 and <code>delay</code> to be based on frames.</li>
 * 	
 * 	<li><strong> immediateRender </strong>:<em> Boolean</em> -
 * 				 Normally when you create a tween, it begins rendering on the very next frame (update cycle) 
 * 				 unless you specify a <code>delay</code>. However, if you prefer to force the tween to 
 * 				 render immediately when it is created, set <code>immediateRender</code> to <code>true</code>. 
 * 				 Or to prevent a <code>from()</code> from rendering immediately, set <code>immediateRender</code> 
 * 				 to <code>false</code>.</li>
 * 	
 * 	<li><strong> onUpdate </strong>:<em> Function</em> -
 * 				 A function that should be called every time the tween updates  
 * 				 (on every frame while the tween is active)</li>
 * 	
 * 	<li><strong> onUpdateParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the <code>onUpdate</code> function. For example,
 * 				 <code>TweenNano.to(mc, 1, {x:100, onUpdate:myFunction, onUpdateParams:[mc, "param2"]});</code></li>
 * 	
 * 	<li><strong> overwrite </strong>:<em> String</em> -
 * 				 Controls how (and if) other tweens of the same target are overwritten. 
 * 				 By default, no tweens are overwritten, but if you'd like to immediately overwrite
 * 				 other tweens of the same target, use <code>overwrite:"all"</code></li>
 * 	</ul>
 * 
 * 
 * <strong>NOTES:</strong><br /><br />
 * <ul>
 * 	<li> The base TweenNano class adds about 2k to your Flash file.</li>
 * 	  
 * 	<li> Passing values as Strings and a preceding "+=" or "-=" will make the tween relative to the 
 * 		current value. For example, if you do <code>TweenNano.to(mc, 2, {x:"-=20"});</code> it'll 
 * 		tween <code>mc.x</code> to the left 20 pixels. <code>{x:"+=20"}</code> would move it to the right.</li>
 * 	  
 * 	<li> You can change the <code>TweenNano.defaultEase</code> if you prefer something other 
 * 		than <code>QuadOut.ease</code>.</li>
 * 	
 * 	<li> Kill all tweens of a particular object anytime with <code>TweenNano.killTweensOf(myObject);</code></li>
 * 	  
 * 	<li> You can kill all delayedCalls to a particular function using <code>TweenNano.killTweensOf(myFunction);</code></li>
 * 	  
 * 	<li> Use the <code>TweenNano.from()</code> method to animate things into place. For example, 
 * 		if you have things set up on the stage in the spot where they should end up, and you 
 * 		just want to animate them into place, you can pass in the beginning x and/or y and/or 
 * 		alpha (or whatever properties you want).</li>
 * 	  
 * 	<li> If you find this class useful, please consider joining <a href="http://www.greensock.com/club/">Club GreenSock</a>
 * 		which not only helps to sustain ongoing development, but also gets you bonus plugins, classes 
 * 		and other benefits that are ONLY available to members. Learn more at 
 * 		<a href="http://www.greensock.com/club/">http://www.greensock.com/club/</a></li>
 * </ul>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */	 
	public class TweenNano {
		/** @private **/
		protected static var _time:Number;
		/** @private **/
		protected static var _frame:uint;
		
		/**
		 * The object that dispatches a <code>"tick"</code> event each time the engine updates, making it easy for 
		 * you to add your own listener(s) to run custom logic after each update (great for game developers).
		 * Add as many listeners as you want. The basic syntax is the same for all versions (AS2, AS3, and Javascript):
		 * 
		 * <p><strong>Basic example (AS2, AS3, and Javascript):</strong></p><listing version="3.0">
//add listener
TweenNano.ticker.addEventListener("tick", myFunction);
 
function myFunction(event) {
 	//executes on every tick after the core engine updates
}
 
//to remove the listener later...
TweenNano.ticker.removeEventListener("tick", myFunction);
</listing>
		 * 
		 * <p>Due to differences in the core languages (and to maximize efficiency), the advanced syntax is slightly different
		 * for the AS3 version compared to AS2 and Javascript. The parameters beyond the first 2 in the addEventListener() 
		 * method are outlined below:</p>
		 * 
		 * <p><strong>Javascript and AS2</strong></p>
		 * <p><code>addEventListener(type, callback, scope, useParam, priority)</code></p>
		 * <p>Parameters:
		 * <ol>
		 * 		<li><strong>type</strong> <em>: String</em> - type of listener, should always be <code>"tick"</code></li>
		 * 		<li><strong>callback</strong> <em>: Function</em> - the function to call when the event occurs</li>
		 * 		<li><strong>scope</strong> <em>: Object</em> - binds the scope to a particular object (scope is basically what "<code>this</code>" refers to in your function). This can be very useful in Javascript and AS2 because scope isn't generally maintained. </li>
		 * 		<li><strong>useParam</strong> <em>: Boolean</em> - if <code>true</code>, an event object will be generated and fed to the callback each time the event occurs. The event is a generic object and has two properties: <code>type</code> (always <code>"tick"</code>) and <code>target</code> which refers to the ticker instance. The default for <code>useParam</code> is <code>false</code> because it improves performance.</li>
		 * 		<li><strong>priority</strong> <em>: Integer</em> - influences the order in which the listeners are called. Listeners with lower priorities are called after ones with higher priorities.</li>
		 * </ol>
		 * </p>
		 * 
		 * <p><strong>Advanced example (Javascript and AS2):</strong></p><listing version="3.0">
//add listener that requests an event object parameter, binds scope to the current scope (this), and sets priority to 1 so that it is called before any other listeners that had a priority lower than 1...
TweenNano.ticker.addEventListener("tick", myFunction, this, true, 1);
 
function myFunction(event) {
	//executes on every tick after the core engine updates
}
 
//to remove the listener later...
TweenNano.ticker.removeEventListener("tick", myFunction);
</listing>
		 * 
		 * <p><strong>AS3</strong></p>
		 * <p>The AS3 version uses the standard <code>EventDispatcher.addEventListener()</code> syntax which 
		 * basically allows you to define a priority and whether or not to use weak references (see Adobe's 
		 * docs for details).</p>
		 * 
		 * <p><strong>Advanced example [AS3 only]:</strong></p><listing version="3.0">
import flash.events.Event;
		 
//add listener with weak reference (standard syntax - notice the 5th parameter is true)
TweenNano.ticker.addEventListener("tick", myFunction, false, 0, true);
		 
function myFunction(event:Event):void {
	//executes on every tick after the core engine updates
}
		 
//to remove the listener later...
TweenNano.ticker.removeEventListener("tick", myFunction);
</listing>
		 **/
		public static var ticker:Shape = new Shape(); 
		
		/** Provides An easy way to change the default easing equation. Choose from any of the GreenSock eases in the <code>com.greensock.easing</code> package or any standard easing function like the ones in Adobe's <code>fl.motion.easing</code> package. @default QuadOut.ease **/
		public static var defaultEase:Object =  function (t:Number, b:Number, c:Number, d:Number):Number {
													return -1 * (t /= d) * (t - 2);
												}
		/** @private **/
		protected static var _reservedProps:Object;
		/** @private **/
		protected static var _tickEvent:Event = new Event("tick");
		/** @private **/
		protected static var _first:TweenNano;
		/** @private **/
		protected static var _last:TweenNano;
	
		/** @private Duration of the tween in seconds (or in frames if "useFrames" is true). **/
		public var _duration:Number; 
		/** Stores variables (things like "alpha", "y" or whatever we're tweening, as well as special properties like "onComplete"). **/
		public var vars:Object; 
		/** @private Start time in seconds (or frames for frames-based tweens) **/
		public var _startTime:Number;
		/** Target object whose properties this tween affects. This can be ANY object or even an array. **/
		public var target:Object;
		/** @private Flagged for garbage collection **/
		public var _gc:Boolean;
		/** @private Indicates that frames should be used instead of seconds for timing purposes. So if useFrames is true and the tween's duration is 10, it would mean that the tween should take 10 frames to complete, not 10 seconds. **/
		public var _useFrames:Boolean;
		/** @private result of _ease(this.time, 0, 1, this.duration). Usually between 0 and 1, but not always (like with Elastic.easeOut). **/
		public var ratio:Number = 0;
		
		/** @private Easing method to use which determines how the values animate over time. Examples are Elastic.easeOut and Strong.easeIn. Many are found in the fl.motion.easing package or com.greensock.easing. **/
		protected var _ease:Function;
		/** @private **/
		protected var _rawEase:Object;
		/** @private Indicates whether or not init() has been called (where all the tween property start/end value information is recorded) **/
		protected var _initted:Boolean;
		
		/** @private **/
		protected var _firstPT:Object;
		/** @private **/
		public var _next:TweenNano;
		/** @private **/
		public var _prev:TweenNano;
		/** @private **/
		public var _targets:Array;
		
		/**
		 * Constructor
		 *  
		 * @param target Target object (or array of objects) whose properties this tween affects 
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is set in the <code>vars</code> parameter)
		 * @param vars An object defining the end value for each property that should be tweened as well as any special properties like <code>onComplete</code>, <code>ease</code>, etc. For example, to tween <code>mc.x</code> to 100 and <code>mc.y</code> to 200 and then call <code>myFunction</code>, do this: <code>new TweenNano(mc, 1, {x:100, y:200, onComplete:myFunction})</code>.
		 */
		public function TweenNano(target:Object, duration:Number, vars:Object) {
			if (!_reservedProps) {
				_reservedProps = {ease:1, delay:1, useFrames:1, overwrite:1, onComplete:1, onCompleteParams:1, runBackwards:1, immediateRender:1, onUpdate:1, onUpdateParams:1, startAt:1};
				_time = getTimer() / 1000;
				_frame = 0;
				ticker.addEventListener(Event.ENTER_FRAME, _updateRoot, false, 0, true);
			}
			this.vars = vars;
			_duration = duration;
			this.target = target;
			if (target is Array && typeof(target[0]) === "object") {
				_targets = target.concat();
			}
			_rawEase = this.vars.ease || defaultEase;
			_ease = (typeof(_rawEase) == "function") ? _rawEase as Function : _rawEase.getRatio;
			_useFrames = Boolean(vars.useFrames == true);
			_startTime = (_useFrames ? _frame : _time) + (this.vars.delay || 0);
			
			if (this.vars.overwrite == "all" || int(this.vars.overwrite) == 1) { 
				killTweensOf(this.target);
			}
			
			_prev = _last;
			if (_last) {
				_last._next = this;
			} else {
				_first = this;
			}
			_last = this;
			
			if (this.vars.immediateRender == true || (duration == 0 && this.vars.delay == 0 && this.vars.immediateRender != false)) {
				_render(0);
			}
		}
		
		/** @private Initializes the property tweens, determining their start values and amount of change. **/
		public function _init():void {
			if (vars.startAt) {
				vars.startAt.immediateRender = true;
				TweenNano.to(target, 0, vars.startAt);
			}
			var i:int, pt:Object;
			if (_targets != null) {
				i = _targets.length;
				while (--i > -1) {
					_initProps(_targets[i]);
				}
			} else {
				_initProps(target);
			}
			if (vars.runBackwards) {
				pt = _firstPT;
				while (pt) {
					pt.s += pt.c;
					pt.c = -pt.c;
					pt = pt._next;
				}
			}
			_initted = true;
		}
		
		/** @private **/
		protected function _initProps(target:*):void {
			if (target != null) {
				for (var p:String in vars) {
					if (!(p in _reservedProps)) {
						_firstPT = {_next:_firstPT, t:target, p:p, f:(typeof(target[p]) === "function")};
						_firstPT.s = (!_firstPT.f) ? Number(target[p]) : target[ ((p.indexOf("set") || typeof(target["get" + p.substr(3)]) !== "function") ? p : "get" + p.substr(3)) ]();
						_firstPT.c = (typeof(vars[p]) === "number") ? Number(vars[p]) - _firstPT.s : (typeof(vars[p]) === "string" && vars[p].charAt(1) === "=") ? int(vars[p].charAt(0)+"1") * Number(vars[p].substr(2)) : Number(vars[p]) || 0;
						if (_firstPT._next) {
							_firstPT._next._prev = _firstPT;
						}
					}
				}
			}
		}
		
		/**
		 * @private
		 * Renders the tween at a particular time (or frame number for frames-based tweens)
		 * WITHOUT changing its _startTime, meaning if the tween is in progress when you call
		 * _render(), it will not adjust the tween's timing to continue from the new time. 
		 * The time is based simply on the overall duration. For example, if a tween's duration
		 * is 3, _render(1.5) would render it at the halfway finished point.
		 * 
		 * @param time time (or frame number for frames-based tweens) to render.
		 */
		public function _render(time:Number):void {
			if (!_initted) {
				_init();
			}
			if (time >= _duration) {
				time = _duration;
				this.ratio = (_ease != _rawEase && _rawEase._calcEnd) ? _ease.call(_rawEase, 1) : 1;
			} else if (time <= 0) {
				this.ratio = (_ease != _rawEase && _rawEase._calcEnd) ? _ease.call(_rawEase, 0) : 0;
			} else {
				this.ratio = (_ease == _rawEase) ? _ease(time, 0, 1, _duration) : _ease.call(_rawEase, time / _duration);
			}
			var pt:Object = _firstPT;
			while (pt) {
				if (pt.f) {
					pt.t[pt.p](pt.c * ratio + pt.s);
				} else {
					pt.t[pt.p] = pt.c * ratio + pt.s;
				}
				pt = pt._next;
			}
			if (vars.onUpdate) {
				vars.onUpdate.apply(null, vars.onUpdateParams);
			}
			if (time == _duration) {
				kill();
				if (vars.onComplete) {
					vars.onComplete.apply(null, vars.onCompleteParams);
				}
			}
		}
		
		/** 
		 * Kills the tween, stopping it immediately. You can optionally define a particular target 
		 * to isolate (or an array of targets) which is only useful in tweens whose target is 
		 * an array. For example, let's say we have a tween like this:
		 * 
		 * <p><code>
		 * var tween = TweenNan.to([mc1, mc2, mc3], 2, {x:100});
		 * </code></p>
		 * 
		 * <p>Later, we could kill <strong>only</strong> the mc2 portion of the tween like this:</p>
		 * 
		 * <p><code>
		 * tween.kill(mc2);
		 * </code></p>
		 * 
		 * <p>To kill the entire tween, simply omit the <code>target</code> parameter, like <code>tween.kill()</code></p>
		 * 
		 * @param target [optional] To kill only aspects of the animation related to a particular target (or targets), reference it here. It can be an array or a single object. For example, to kill only parts having to do with <code>myObject</code>, do <code>kill(myObject)</code> or to kill only parts having to do with <code>myObject1</code> and <code>myObject2</code>, do <code>kill([myObject1, myObject2])</code>. If no target is defined, <strong>ALL</strong> targets will be affected. 
		 **/
		public function kill(target:*=null):void {
			var i:int, pt:Object = _firstPT;
			target = target || _targets || this.target;
			if (target is Array && typeof(target[0]) === "object") {
				i = target.length;
				while (--i > -1) {
					kill(target[i]);
				}
				return;
			} else if (_targets != null) {
				i = _targets.length; 
				while (--i > -1) {
					if (target == _targets[i]) {
						_targets.splice(i, 1);
					}
				}
				while (pt) {
					if (pt.t == target) {
						if (pt._next) {
							pt._next._prev = pt._prev;
						}
						if (pt._prev) {
							pt._prev._next = pt._next;
						} else {
							_firstPT = pt._next;
						}
					}
					pt = pt._next;
				}
			}
			if (_targets == null || _targets.length == 0) {
				_gc = true;
				if (_prev) {
					_prev._next = _next;
				} else if (this == _first) {
					_first = _next;
				}
				if (_next) {
					_next._prev = _prev;
				} else if (this == _last) {
					_last = _prev;
				}
				_next = _prev = null;
			}
		}
		
		
//---- STATIC FUNCTIONS -------------------------------------------------------------------------
		
		/**
		 * Static method for creating a TweenNano instance that animates to the specified destination values
		 * (from the current values). The following lines of code all produce identical results: 
		 * 
		 * <listing version="3.0">
TweenNano.to(mc, 1, {x:100});
var myTween = new TweenNano(mc, 1, {x:100});
var myTween = TweenNano.to(mc, 1, {x:100});
</listing>
		 * 
		 * <p>Each line above will tween the <code>"x"</code> property of the <code>mc</code> object 
		 * to a value of 100 over the coarse of 1 second. They each use a slightly different syntax,
		 * all of which are valid. If you don't need to store a reference of the tween, just use the 
		 * static <code>TweenNano.to( )</code> call.</p>
		 * 
		 * <p>Since the <code>target</code> parameter can also be an array of objects, the following 
		 * code will tween the x property of mc1, mc2, and mc3 to a value of 100 simultaneously:</p>
		 * 
		 * <listing version="3.0">
TweenNano.to([mc1, mc2, mc3], 1, {x:100});
</listing>
		 * <p>Even though 3 objects are animating, there is still only one tween created.</p>
		 * 
		 * <p>For simple sequencing, you can use the <code>delay</code> special property
		 * (like <code>TweenNano.to(mc, 1, {x:100, delay:0.5})</code>), 
		 * but it is highly recommended that you consider using TimelineLite (or TimelineMax) 
		 * for all but the simplest sequencing tasks. It has an identical <code>to()</code> method
		 * that allows you to append tweens one-after-the-other and then control the entire sequence 
		 * as a whole. You can even have the tweens overlap as much as you want.</p>
		 * 
		 * @param target Target object (or array of objects) whose properties this tween affects. 
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is set in the <code>vars</code> parameter)
		 * @param vars An object defining the end value for each property that should be tweened as well as any special properties like <code>onComplete</code>, <code>ease</code>, etc. For example, to tween <code>mc.x</code> to 100 and <code>mc.y</code> to 200 and then call <code>myFunction</code>, do this: <code>TweenNano.to(mc, 1, {x:100, y:200, onComplete:myFunction});</code>
		 * @return TweenNano instance
		 * @see com.greensock.TimelineLite#to()
		 * @see #from()
		 */
		public static function to(target:Object, duration:Number, vars:Object):TweenNano {
			return new TweenNano(target, duration, vars);
		}
		
		/**
		 * Static method for creating a TweenNano instance that tweens backwards - 
		 * you define the <strong>BEGINNING</strong> values and the current values are used
		 * as the destination values which is great for doing things like animating objects
		 * onto the screen because you can set them up initially the way you want them to look 
		 * at the end of the tween and then animate in from elsewhere.
		 * 
		 * <p><strong>NOTE:</strong> By default, <code>immediateRender</code> is <code>true</code> in 
		 * <code>from()</code> tweens, meaning that they immediately render their starting state 
		 * regardless of any delay that is specified. You can override this behavior by passing 
		 * <code>immediateRender:false</code> in the <code>vars</code> parameter so that it will 
		 * wait to render until the tween actually begins. To illustrate the default behavior, the 
		 * following code will immediately set the <code>alpha</code> of <code>mc</code> 
		 * to 0 and then wait 2 seconds before tweening the <code>alpha</code> back to 1 over
		 * the course of 1.5 seconds:</p>
		 * 
		 * <p><code>
		 * TweenNano.from(mc, 1.5, {alpha:0, delay:2});
		 * </code></p>
		 * 
		 * <p>Since the <code>target</code> parameter can also be an array of objects, the following 
		 * code will tween the alpha property of mc1, mc2, and mc3 from a value of 0 simultaneously:</p>
		 * 
		 * <listing version="3.0">
TweenNano.from([mc1, mc2, mc3], 1.5, {alpha:0});
</listing>
		 * <p>Even though 3 objects are animating, there is still only one tween created.</p>
		 * 
		 * <p>For simple sequencing, you can use the <code>delay</code> special property
		 * (like <code>TweenNano.from(mc, 1, {alpha:0, delay:0.5})</code>), 
		 * but it is highly recommended that you consider using TimelineLite (or TimelineMax) 
		 * for all but the simplest sequencing tasks. It has an identical <code>from()</code> method
		 * that allows you to append tweens one-after-the-other and then control the entire sequence 
		 * as a whole. You can even have the tweens overlap as much as you want.</p>
		 * 
		 * @param target Target object (or array of objects) whose properties this tween affects.  
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is set in the <code>vars</code> parameter)
		 * @param vars An object defining the starting value for each property that should be tweened as well as any special properties like <code>onComplete</code>, <code>ease</code>, etc. For example, to tween <code>mc.x</code> from 100 and <code>mc.y</code> from 200 and then call <code>myFunction</code>, do this: <code>TweenNano.from(mc, 1, {x:100, y:200, onComplete:myFunction});</code>
		 * @return TweenNano instance
		 * @see #to()
		 * @see com.greensock.TimelineLite#from()
		 * @see com.greensock.TimelineLite#staggerFrom()
		 */
		public static function from(target:Object, duration:Number, vars:Object):TweenNano {
			vars.runBackwards = true;
			if (!("immediateRender" in vars)) {
				vars.immediateRender = true;
			}
			return new TweenNano(target, duration, vars);
		}
		
		/**
		 * Provides a simple way to call a function after a set amount of time (or frames). You can
		 * optionally pass any number of parameters to the function too.
		 * 
		 * <p><strong>Javascript and AS2 note:</strong> - Due to the way Javascript and AS2 don't 
		 * maintain scope (what "<code>this</code>" refers to, or the context) in function calls, 
		 * it can be useful to define the scope specifically. Therefore, in the Javascript and AS2 
		 * versions the 4th parameter is <code>scope</code>, bumping <code>useFrames</code> 
		 * back to the 5th parameter:</p>
		 * 
		 * <p><code>TweenNano.delayedCall(delay, callback, params, scope, useFrames)</code> <em>[Javascript and AS2 only]</em></p>
		 * 
		 * <listing version="3.0">
//calls myFunction after 1 second and passes 2 parameters:
TweenNano.delayedCall(1, myFunction, ["param1", 2]);
		 
function myFunction(param1, param2) {
	//do stuff
}
</listing>
		 * 
		 * @param delay Delay in seconds (or frames if <code>useFrames</code> is <code>true</code>) before the function should be called
		 * @param callback Function to call
		 * @param params An Array of parameters to pass the function (optional).
		 * @param useFrames If the delay should be measured in frames instead of seconds, set <code>useFrames</code> to <code>true</code> (default is <code>false</code>)
		 * @return TweenNano instance
		 * @see com.greensock.TimelineLite#call()
		 */
		public static function delayedCall(delay:Number, callback:Function, params:Array=null, useFrames:Boolean=false):TweenNano {
			return new TweenNano(callback, 0, {delay:delay, onComplete:callback, onCompleteParams:params, useFrames:useFrames});
		}
		
		/**
		 * @private
		 * Updates active tweens and inits those whose startTime precedes the current _time/_frame.
		 * 
		 * @param e ENTER_FRAME Event
		 */
		public static function _updateRoot(e:Event=null):void {
			_frame += 1;
			_time = getTimer() * 0.001;
			var tween:TweenNano = _first,
				next:TweenNano,
				t:Number;
			while (tween) {
				next = tween._next;
				t = (tween._useFrames) ? _frame : _time;
				if (t >= tween._startTime && !tween._gc) {
					tween._render(t - tween._startTime);
				}
				tween = next;
			}
			ticker.dispatchEvent(_tickEvent);
		}
		
		/**
		 * Kills all the tweens of a particular object or the delayedCalls to a particular function. 
		 * If, for example, you want to kill all tweens of <code>myObject</code>, you'd do this:
		 * 
		 * <p><code>
		 * TweenNano.killTweensOf(myObject);
		 * </code></p>
		 * 
		 * <p>To kill all the delayedCalls that were created like <code>TweenNano.delayedCall(5, myFunction);</code>, 
		 * you can simply call <code>TweenNano.killTweensOf(myFunction);</code> because delayedCalls 
		 * are simply tweens that have their <code>target</code> and <code>onComplete</code> set to 
		 * the same function (as well as a <code>delay</code> of course).</p>
		 * 
		 * <p><code>killTweensOf()</code> affects tweens that haven't begun yet too. If, for example, 
		 * a tween of <code>myObject</code> has a <code>delay</code> of 5 seconds and 
		 * <code>TweenNano.killTweensOf(mc)</code> is called 2 seconds after the tween was created, 
		 * it will still be killed even though it hasn't started yet. </p>
		 * 
		 * @param target Object whose tweens should be killed immediately
		 **/
		 public static function killTweensOf(target:Object):void {
			var t:TweenNano = _first,
				next:TweenNano;
			while (t) {
				next = t._next;
				if (t.target == target) {
					t.kill();
				} else if (t._targets != null) {
					t.kill(target);
				}
				t = next;
			}
		}
		
	}
}