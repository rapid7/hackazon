/**
 * VERSION: 12.1.1
 * DATE: 2013-12-07
 * AS3 (AS2 version is also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.core {
	import flash.display.Shape;
	import flash.events.Event;
	import flash.utils.getTimer;
/**
 * Base class for all TweenLite, TweenMax, TimelineLite, and TimelineMax classes, providing
 * core methods/properties/functionality, but there is no reason to create an instance of this 
 * class directly. It can be very useful, however, as a data type in AS3/AS2 for methods/properties that 
 * can contain tweens or timelines. For example, maybe you build an <code>animateIn()</code> and 
 * <code>animateOut()</code> method for many of your own custom classes, and they each return an 
 * Animation instance which could be a tween or a timeline:
 * 
 * <listing version="3.0">
function animateIn():Animation {
	return TweenLite.to(this, 1, {scaleX:1, scaleY:1, autoAlpha:1});
}
 
function animateOut():Animation {
	var tl:TimelineLite = new TimelineLite();
	tl.to(this, 1, {scaleX:0.5, scaleY:0.5});
	tl.to(this, 0.5, {autoAlpha:0}, "-=0.25");
	return tl;
}

var anim:Animation = animateIn();

//now we can control the animation with the common methods:
anim.pause();
anim.play();
anim.reverse();

//or somewhere else, we could build a sequence like this:
var tl:TimelineLite = new TimelineLite();
tl.add( animateIn() );
tl.add( animateOut(), 3);
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class Animation {
		/** @private **/
		public static const version:String = "12.1.1";
		
		/**
		 * The object that dispatches a <code>"tick"</code> event each time the engine updates, making it easy for 
		 * you to add your own listener(s) to run custom logic after each update (great for game developers).
		 * Add as many listeners as you want. The basic syntax is the same for all versions (AS2, AS3, and JavaScript):
		 * 
		 * <p><strong>Basic example (AS2, AS3, and JavaScript):</strong></p><listing version="3.0">
//add listener
Animation.ticker.addEventListener("tick", myFunction);

function myFunction(event) {
    //executes on every tick after the core engine updates
}

//to remove the listener later...
Animation.ticker.removeEventListener("tick", myFunction);
</listing>
		 * 
		 * <p>Due to differences in the core languages (and to maximize efficiency), the advanced syntax is slightly different
		 * for the AS3 version compared to AS2 and JavaScript. The parameters beyond the first 2 in the addEventListener() 
		 * method are outlined below:</p>
		 * 
		 * <p><strong>JavaScript and AS2</strong></p>
		 * <p><code>addEventListener(type, callback, scope, useParam, priority)</code></p>
		 * <p>Parameters:
		 * <ol>
		 * 		<li><strong>type</strong> <em>: String</em> - type of listener, should always be <code>"tick"</code></li>
		 * 		<li><strong>callback</strong> <em>: Function</em> - the function to call when the event occurs</li>
		 * 		<li><strong>scope</strong> <em>: Object</em> - binds the scope to a particular object (scope is basically what "<code>this</code>" refers to in your function). This can be very useful in JavaScript and AS2 because scope isn't generally maintained. </li>
		 * 		<li><strong>useParam</strong> <em>: Boolean</em> - if <code>true</code>, an event object will be generated and fed to the callback each time the event occurs. The event is a generic object and has two properties: <code>type</code> (always <code>"tick"</code>) and <code>target</code> which refers to the ticker instance. The default for <code>useParam</code> is <code>false</code> because it improves performance.</li>
		 * 		<li><strong>priority</strong> <em>: Integer</em> - influences the order in which the listeners are called. Listeners with lower priorities are called after ones with higher priorities.</li>
		 * </ol>
		 * </p>
		 * 
		 * <p>In JavaScript, the Animation object/class is located at <code>com.greensock.core.Animation</code> - it is not added to the global namespace in order to avoid polluting it (developers rarely directly access the Animation class)</p>
		 * 
		 * <p><strong>Advanced example (JavaScript and AS2):</strong></p><listing version="3.0">
//add listener that requests an event object parameter, binds scope to the current scope (this), and sets priority to 1 so that it is called before any other listeners that had a priority lower than 1...
Animation.ticker.addEventListener("tick", myFunction, this, true, 1);

function myFunction(event) {
    //executes on every tick after the core engine updates
}

//to remove the listener later...
Animation.ticker.removeEventListener("tick", myFunction);
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
Animation.ticker.addEventListener("tick", myFunction, false, 0, true);

function myFunction(event:Event):void {
    //executes on every tick after the core engine updates
}

//to remove the listener later...
Animation.ticker.removeEventListener("tick", myFunction);
</listing>
		 **/
		public static var ticker:Shape = new Shape();
		/** @private root timeline on which all time-based tweens/timelines are initially placed (<code>_rootFramesTimeline</code> is for frames-based tweens/timelines where <code>useFrames:true</code> is defined in the constructor's <code>vars</code> parameter). **/
		public static var _rootTimeline:SimpleTimeline;
		/** @private root timeline on which all frames-based tweens/timelines are initially placed (<code>_rootTimeline</code> is for time-based tweens/timelines). A frames-based animation is one that has <code>useFrames:true</code> defined in the constructor's <code>vars</code> parameter or it is placed into a parent timeline that is frames-based (the parent timeline always defines the timing mode). **/
		public static var _rootFramesTimeline:SimpleTimeline;
		/** @private Each time the root timelines are updated, <code>_rootFrame</code> is incremented in order to keep track of how many frames have been rendered. **/
		protected static var _rootFrame:Number = -1;
		/** @private We reuse this event instance for better memory management rather than recreating a new instance on every frame. **/
		protected static var _tickEvent:Event = new Event("tick");
		/** @private **/
		protected static var _tinyNum:Number = 0.0000000001;
		
		/** @private The <code>onUpdate</code> callback (if one is defined). Checking an instance property is faster than looking it up in the vars object on every render. This is purely a speed optimization **/
		protected var _onUpdate:Function;

		/** @private Delay in seconds (or frames for frames-based tweens/timelines) **/
		public var _delay:Number; 
		/** @private Primarily used for zero-duration tweens to determine the direction/momentum of time in its parent timeline which controls whether the starting or ending values should be rendered. See the render() method for usage (which is slightly different in tweens versus timelines) **/
		public var _rawPrevTime:Number;
		/** @private Indicates whether or not the tween is currently active (typically when the parent timeline's playhead is between the start and end time of this animation). Makes conditional logic faster in the rendering queue of the parent timeline because if a tween is active, it'll always get rendered and we can flip _acitve to false when it completes. **/
		public var _active:Boolean; 
		/** @private Flagged for garbage collection (indicates the tween has been disabled, but keep in mind that a tween can be re-enabled later too!) **/
		public var _gc:Boolean; 
		/** @private Indicates whether or not the animation has been initialized (for tweens, this is when all the tweening properties get analyzed and their start/end values recorded, etc.) **/
		public var _initted:Boolean; 
		/** @private The time at which the animation begins, according to its parent timeline's time. For example, if the tween starts at exactly 3 seconds into the timeline on which it is placed, startTime would be 3. **/
		public var _startTime:Number; 
		/** @private The local position of the playhead (essentially the current time). If the animation has a non-zero <code>repeat</code> (only available on TweenMax and TimelineMax instances), its <code>time</code> goes back to zero upon repeating even though the <code>totalTime</code> continues forward linearly (or if it <code>yoyo</code> is <code>true</code>, the <code>time</code> alternates between moving forward and backward). <code>time</code> never exceeds the duration whereas the <code>totalTime</code> reflects the overall time including any repeats and repeatDelays. For example, if a TweenMax instance has a duration of 2 and a repeat of 3, <code>totalTime</code> will go from 0 to 8 during the course of the tween (plays once then repeats 3 times, making 4 total cycles) whereas <code>time</code> will go from 0 to 2 a total of 4 times. **/
		public var _time:Number; 
		/** @private The overall position of the playhead including any repeats and repeatDelays (which are only available in TweenMax and TimelineMax). For example, if a TweenMax instance has a duration of 2 and a repeat of 3, <code>totalTime</code> will go from 0 to 8 during the course of the tween (plays once then repeats 3 times, making 4 total cycles) whereas <code>time</code> will go from 0 to 2 a total of 4 times. **/
		public var _totalTime:Number; 
		/** @private Duration of the animation, not including any repeats or repeatDelays (which are only available in TweenMax and TimelineMax). For example, if a TweenMax instance has a duration of 2 and a repeat of 3, its totalDuration would be 8 (one standard play plus 3 repeats equals 4 total cycles). **/
		public var _duration:Number; 
		/** @private Total duration of the animation including any repeats or repeatDelays (which are only available in TweenMax and TimelineMax). For example, if a TweenMax instance has a duration of 2 and a repeat of 3, its totalDuration would be 8 (one standard play plus 3 repeats equals 4 total cycles). **/
		public var _totalDuration:Number; 
		/** @private Records the parent timeline's <code>rawTime</code> when the animation is paused (so that we can place it at the appropriate time when it is unpaused). NaN when the animation isn't paused. **/
		public var _pauseTime:Number;
		/** @private Factor that's used to scale the time in the animation where 1 = normal speed, 0.5 = half speed, 2 = double speed, etc. For example, if a tween's duration is 2 but its <code>timeScale</code> is 0.5, it will take 4 seconds to finish. If you nest that tween in a TimelineLite that has a <code>timeScale</code> of 0.5 as well, it will take 8 seconds to finish. You can even tween another tween's (or timeline's) <code>timeScale</code> to gradually slow it down or speed it up. **/
		public var _timeScale:Number;
		/** @private Indicates whether or not the animation is reversed. **/ 
		public var _reversed:Boolean;
		/** @private The most recent parent timeline (only <code>null</code> for the <code>_rootTimeline</code> and <code>_rootFramesTimeline</code>). The <code>timeline</code> property (no "_" prefix) is null whenever the animation is removed from its parent timeline. We use this internally in slightly different ways. We need to always maintain a reference to the last parent timeline so that if the animation is re-enabled, we know where to put it. "_gc" is different in that a Animation could be eligible for gc yet not removed from its timeline, like when a TimelineLite completes for example. It makes things much faster to enable again if/when necessary, like if the TimelineLite gets restarted. **/
		public var _timeline:SimpleTimeline;
		/** @private If <code>true</code>, the <code>_duration</code> or <code>_totalDuration</code> may need refreshing. For example, if a TimelineLite's child had a change in duration or startTime, it could affect the parent timeline's duration but we don't want to always make the update immediately because there may be many more changes made before the timeline actually need to be rendered again, so this helps improve performance. If the <code>_dirty</code> is <code>false</code>, we can skip the method call and quickly read from the _duration and/or _totalDuration. **/
		public var _dirty:Boolean; 
		/** @private Provides a quick way to check whether or not a animation is currently paused (skipping the paused() method call). **/
		public var _paused:Boolean; 
		/** @private Next Animation in the linked list. **/
		public var _next:Animation;
		/** @private Previous Animation in the linked list. **/
		public var _prev:Animation;
		
		/** The <code>vars</code> object passed into the constructor which stores configuration variables like onComplete, onUpdate, etc. as well as tweening properties like opacity, x, y or whatever. **/
		public var vars:Object;
		/** [Read-only] Parent timeline. Every animation is placed onto a timeline (the root timeline by default) and can only have one parent. An instance cannot exist in multiple timelines at once. **/
		public var timeline:SimpleTimeline;
		/** A place to store any data you want (initially populated with <code>vars.data</code> if it exists). **/
		public var data:*; 
		
		/**
		 * Constructor 
		 * 
		 * @param duration duration in seconds (or frames for frames-based tweens)
		 * @param vars configuration variables (for example, <code>{x:100, y:0, opacity:0.5, onComplete:myFunction}</code>)
		 */
		public function Animation(duration:Number=0, vars:Object=null) {
			this.vars = vars || {};
			if (this.vars._isGSVars) {
				this.vars = this.vars.vars;
			}
			_duration = _totalDuration = duration || 0;
			_delay = Number(this.vars.delay) || 0;
			_timeScale = 1;
			_totalTime = _time = 0;
			data = this.vars.data;
			_rawPrevTime = -1;
			
			if (_rootTimeline == null) {
				if (_rootFrame == -1) {
					_rootFrame = 0;
					_rootFramesTimeline = new SimpleTimeline();
					_rootTimeline = new SimpleTimeline();
					_rootTimeline._startTime = getTimer() / 1000;
					_rootFramesTimeline._startTime = 0;
					_rootTimeline._active = _rootFramesTimeline._active = true;
					ticker.addEventListener("enterFrame", _updateRoot, false, 0, true);
				} else {
					return;
				}
			}
			
			var tl:SimpleTimeline = (this.vars.useFrames) ? _rootFramesTimeline : _rootTimeline;
			tl.add(this, tl._time);
			
			_reversed = (this.vars.reversed == true);
			if (this.vars.paused) {
				paused(true);
			}
		}
		
		
		/**
		 * Begins playing forward, optionally from a specific time (by default playback begins from
		 * wherever the playhead currently is). This also ensures that the instance is neither paused 
		 * nor reversed.
		 * 
		 * <p>If you define a "from" time (the first parameter, which could also be a label for TimelineLite
		 * or TimelineMax instances), the playhead moves there immediately and if there are any 
		 * events/callbacks inbetween where the playhead was and the new time, they will not be triggered 
		 * because by default <code>suppressEvents</code> (the 2nd parameter) is <code>true</code>. 
		 * Think of it like picking the needle up on a record player and moving it to a new position 
		 * before placing it back on the record. If, however, you do not want the events/callbacks suppressed 
		 * during that initial move, simply set the <code>suppressEvents</code> parameter to <code>false</code>.</p>
		 * 
		 * <listing version="3.0">
//begins playing from wherever the playhead currently is:
myAnimation.play();

//begins playing from exactly 2-seconds into the animation:
myAnimation.play(2);

//begins playing from exactly 2-seconds into the animation but doesn't suppress events during the initial move:
myAnimation.play(2, false);
</listing>
		 * 
		 * @param from The time (or label for TimelineLite/TimelineMax instances) from which the animation should begin playing (if none is defined, it will begin playing from wherever the playhead currently is).
		 * @param suppressEvents If <code>true</code> (the default), no events or callbacks will be triggered when the playhead moves to the new position defined in the <code>from</code> parameter.
		 * @return self (makes chaining easier)
		 */
		public function play(from:*=null, suppressEvents:Boolean=true):* {
			if (from != null) {
				seek(from, suppressEvents);
			}
			reversed(false);
			return paused(false);
		}
		
		/**
		 * Pauses the instance, optionally jumping to a specific time. 
		 * 
		 * <p>If you define a time to jump to (the first parameter, which could also be a label for TimelineLite
		 * or TimelineMax instances), the playhead moves there immediately and if there are any 
		 * events/callbacks inbetween where the playhead was and the new time, they will not be triggered 
		 * because by default <code>suppressEvents</code> (the 2nd parameter) is <code>true</code>. 
		 * Think of it like picking the needle up on a record player and moving it to a new position 
		 * before placing it back on the record. If, however, you do not want the events/callbacks suppressed 
		 * during that initial move, simply set the <code>suppressEvents</code> parameter to <code>false</code>.</p>
		 * 
		 * <listing version="3.0">
 //pauses wherever the playhead currently is:
 myAnimation.pause();
 
 //jumps to exactly 2-seconds into the animation and then pauses:
 myAnimation.pause(2);
 
 //jumps to exactly 2-seconds into the animation and pauses but doesn't suppress events during the initial move:
 myAnimation.pause(2, false);
 </listing>
		 * 
		 * @param atTime The time (or label for TimelineLite/TimelineMax instances) that the instance should jump to before pausing (if none is defined, it will pause wherever the playhead is currently located).
		 * @param suppressEvents If <code>true</code> (the default), no events or callbacks will be triggered when the playhead moves to the new position defined in the <code>atTime</code> parameter.
		 * @return self (makes chaining easier)
		 */
		public function pause(atTime:*=null, suppressEvents:Boolean=true):* {
			if (atTime != null) {
				seek(atTime, suppressEvents);
			}
			return paused(true);
		}
		
		/**
		 * Resumes playing without altering direction (forward or reversed), optionally jumping to a specific time first. 
		 * 
		 * <p>If you define a time to jump to (the first parameter, which could also be a label for TimelineLite
		 * or TimelineMax instances), the playhead moves there immediately and if there are any 
		 * events/callbacks inbetween where the playhead was and the new time, they will not be triggered 
		 * because by default <code>suppressEvents</code> (the 2nd parameter) is <code>true</code>. 
		 * Think of it like picking the needle up on a record player and moving it to a new position 
		 * before placing it back on the record. If, however, you do not want the events/callbacks suppressed 
		 * during that initial move, simply set the <code>suppressEvents</code> parameter to <code>false</code>.</p>
		 * 
		 * <listing version="3.0">
 //resumes from wherever the playhead currently is:
 myAnimation.resume();
 
 //jumps to exactly 2-seconds into the animation and then resumes playback:
 myAnimation.resume(2);
 
 //jumps to exactly 2-seconds into the animation and resumes playbck but doesn't suppress events during the initial move:
 myAnimation.resume(2, false);
		 </listing>
		 * 
		 * @param from The time (or label for TimelineLite/TimelineMax instances) that the instance should jump to before resuming playback (if none is defined, it will resume wherever the playhead is currently located).
		 * @param suppressEvents If <code>true</code> (the default), no events or callbacks will be triggered when the playhead moves to the new position defined in the <code>from</code> parameter.
		 * @return self (makes chaining easier)
		 */
		public function resume(from:*=null, suppressEvents:Boolean=true):* {
			if (from != null) {
				seek(from, suppressEvents);
			}
			return paused(false);
		}
		
		/**
		 * Jumps to a specific time without affecting whether or not the instance is paused or reversed.
		 * 
		 * <p>If there are any events/callbacks inbetween where the playhead was and the new time, 
		 * they will not be triggered because by default <code>suppressEvents</code> (the 2nd parameter) 
		 * is <code>true</code>. Think of it like picking the needle up on a record player and moving it 
		 * to a new position before placing it back on the record. If, however, you do not want the 
		 * events/callbacks suppressed during that initial move, simply set the <code>suppressEvents</code> 
		 * parameter to <code>false</code>.</p>
		 * 
		 * <listing version="3.0">
 //jumps to exactly 2 seconds
 myAnimation.seek(2);
 
 //jumps to exactly 2 seconds but doesn't suppress events during the initial move:
 myAnimation.seek(2, false);
		 </listing>
		 * 
		 * @param time The time (or label for TimelineLite/TimelineMax instances) to go to.
		 * @param suppressEvents If <code>true</code> (the default), no events or callbacks will be triggered when the playhead moves to the new position defined in the <code>time</code> parameter.
		 * @return self (makes chaining easier)
		 */
		public function seek(time:*, suppressEvents:Boolean=true):* {
			return totalTime(Number(time), suppressEvents);
		}
		
		/**
		 * Restarts and begins playing forward from the beginning.
		 * 
		 * <listing version="3.0">
 //restarts, not including any delay that was defined
 myAnimation.restart();
 
 //restarts, including any delay, and doesn't suppress events during the initial move back to time:0
 myAnimation.restart(true, false);
		 </listing>
		 * 
		 * @param includeDelay Determines whether or not the delay (if any) is honored when restarting. For example, if a tween has a delay of 1 second, like <code>new TweenLite(mc, 2, {x:100, delay:1});</code> and then later <code>restart()</code> is called, it will begin immediately, but <code>restart(true)</code> will cause the delay to be honored so that it won't begin for another 1 second.
		 * @param suppressEvents If <code>true</code> (the default), no events or callbacks will be triggered when the playhead moves to the new position defined in the <code>time</code> parameter. 
		 * @return self (makes chaining easier)
		 */
		public function restart(includeDelay:Boolean=false, suppressEvents:Boolean=true):* {
			reversed(false);
			paused(false);
			return totalTime((includeDelay ? -_delay : 0), suppressEvents, true);
		}
		
		/**
		 * Reverses playback so that all aspects of the animation are oriented backwards including, for example,
		 * a tween's ease. This will cause the instance's <code>time</code> and <code>totalTime</code> to move 
		 * back towards zero as well. You can optionally define a specific time to jump to before reversing
		 * (by default it begins playing in reverse from wherever the playhead currently is). 
		 * Calling <code>reverse()</code> also ensures that the instance is neither paused nor reversed.
		 * 
		 * <p>To jump to the very end of the animation and play in reverse from there, use 0 as the 
		 * "from" parameter, like <code>reverse(0)</code>.</p>
		 * 
		 * <p>To check whether or not the instance is reversed, use the <code>reversed()</code> method, like
		 * <code>if (myAnimation.reversed()) {...}</code></p>
		 * 
		 * <p>If you define a "from" time (the first parameter, which could also be a label for TimelineLite
		 * or TimelineMax instances), the playhead moves there immediately and if there are any 
		 * events/callbacks inbetween where the playhead was and the new time, they will not be triggered 
		 * because by default <code>suppressEvents</code> (the 2nd parameter) is <code>true</code>. 
		 * Think of it like picking the needle up on a record player and moving it to a new position 
		 * before placing it back on the record. If, however, you do not want the events/callbacks suppressed 
		 * during that initial move, simply set the <code>suppressEvents</code> parameter to <code>false</code>.</p>
		 * 
		 * <listing version="3.0">
 //reverses playback from wherever the playhead currently is:
 myAnimation.reverse();
 
 //reverses playback from exactly 2 seconds into the animation:
 myAnimation.reverse(2);
 
 //reverses playback from exactly 2 seconds into the animation but doesn't suppress events during the initial move:
 myAnimation.reverse(2, false);
 
 //reverses playback from the very END of the animation:
 myAnimation.reverse(0);
  
 //reverses playback starting from exactly 1 second before the end of the animation:
 myAnimation.reverse(-1);
 
 //flips the orientation (if it's forward, it will go backward, if it is backward, it will go forward):
 if (myAnimation.reversed()) {
     myAnimation.play();
 } else {
     myAnimation.reverse();
 }
 
 //flips the orientation using the reversed() method instead (shorter version of the code above):
 myAnimation.reversed( !myAnimation.reversed() );
		 </listing>
		 * 
		 * @param from The time (or label for TimelineLite/TimelineMax instances) from which the animation should begin playing in reverse (if none is defined, it will begin playing from wherever the playhead currently is). To begin at the very end of the animation, use <code>0</code>. Negative numbers are relative to the end of the animation, so -1 would be 1 second from the end.
		 * @param suppressEvents If <code>true</code> (the default), no events or callbacks will be triggered when the playhead moves to the new position defined in the <code>from</code> parameter.
		 * @return self (makes chaining easier)
		 */
		public function reverse(from:*=null, suppressEvents:Boolean=true):* {
			if (from != null) {
				seek((from || totalDuration()), suppressEvents);
			}
			reversed(true);
			return paused(false);
		}
		
		/**
		 * @private
		 * Renders the animation at a particular time (or frame number for frames-based tweens). 
		 * The time is based simply on the overall duration. For example, if an animations's duration
		 * is 3, render(1.5) would render it as halfway finished.
		 * 
		 * @param time time (or frame number for frames-based animations) to render. If a negative value is used, it will act like 0. If the value exceeds the <code>totalDuration</code>, it will act like the <code>totalDuration</code>.
		 * @param suppressEvents If true, no events or callbacks will be triggered for this render (like onComplete, onUpdate, onReverseComplete, etc.)
		 * @param force Normally the animation will skip rendering if the time matches the _totalTime (to improve performance), but if force is true, it forces a render. This is primarily used internally for tweens with durations of zero in TimelineLite/Max instances.
		 */
		public function render(time:Number, suppressEvents:Boolean=false, force:Boolean=false):void {
			
		}
		
		
		/** 
		 * Clears any initialization data (like starting/ending values in tweens) which can be useful if, for example, 
		 * you want to restart a tween without reverting to any previously recorded starting values. When you <code>invalidate()</code> 
		 * an animation, it will be re-initialized the next time it renders and its <code>vars</code> object will be re-parsed. 
		 * The timing of the animation (duration, startTime, delay) will not be affected.
		 * 
		 * <p>Another example would be if you have a <code>TweenMax(mc, 1, {x:100, y:100})</code> that ran when mc.x and mc.y 
		 * were initially at 0, but now mc.x and mc.y are 200 and you want them tween to 100 again, you could simply 
		 * <code>invalidate()</code> the tween and <code>restart()</code> it. Without invalidating first, restarting it 
		 * would cause the values jump back to 0 immediately (where they started when the tween originally began). 
		 * When you invalidate a TimelineLite/TimelineMax, it automatically invalidates all of its children.</p>
		 * @return self (makes chaining easier)
		 **/
		public function invalidate():* {
			return this;
		}
		
		/** 
		 * Indicates whether or not the animation is currently active (meaning the virtual playhead is actively moving across 
		 * this instance's time span and it is not paused, nor are any of its ancestor timelines).
		 * So for example, if a tween is in the middle of tweening, it's active, but after it is finished (or before 
		 * it begins), it is <strong>not</strong> active. If it is paused or if it is placed inside of a timeline that's paused
		 * (or if any of its ancestor timelines are paused), <code>isActive()</code> will return <code>false</code>. If the
		 * playhead is directly on top of the animation's start time (even if it hasn't rendered quite yet), that counts
		 * as "active".
		 * 
		 * <p>You may also check the <code>progress()</code> or <code>totalProgress()</code>, but those don't take into consideration
		 * the paused state or the position of the parent timeline's playhead.</p>
		 * 
		 * @see #progress()
		 * @see #totalProgress()
		 **/
		public function isActive():Boolean {
			var tl:SimpleTimeline = _timeline, //the 2 root timelines won't have a _timeline; they're always active.
				rawTime:Number;
			return ((tl == null) || (!_gc && !_paused && tl.isActive() && (rawTime = tl.rawTime()) >= _startTime && rawTime < _startTime + totalDuration() / _timeScale));
		}
		
		/**
		 * @private
		 * If an animation is enabled, it is eligible to be rendered (unless it is paused). Disabling it
		 * essentially removes it from its parent timeline and stops protecting it from garbage collection.
		 * 
		 * @param enabled Enabled state of the animation
		 * @param ignoreTimeline By default, the tween/timeline will remove itself from its parent timeline when it is disabled and add itself when it is enabled, but this parameter allows you to skip that behavior.
		 * @return Boolean value indicating whether or not important properties may have changed when the animation was enabled/disabled. For example, when a MotionBlurPlugin is disabled, it swaps out a BitmapData for the target and may alter the opacity. We need to know this in order to determine whether or not a new tween that is overwriting this one should be re-initialized with the changed properties. 
		 **/
		public function _enabled(enabled:Boolean, ignoreTimeline:Boolean=false):Boolean {
			_gc = !enabled; //note: it is possible for _gc to be true and timeline not to be null in situations where a parent TimelineLite/Max has completed and is removed - the developer might hold a reference to that timeline and later restart() it or something. 
			_active = Boolean(enabled && !_paused && _totalTime > 0 && _totalTime < _totalDuration);
			if (!ignoreTimeline) {
				if (enabled && timeline == null) {
					_timeline.add(this, _startTime - _delay);
				} else if (!enabled && timeline != null) {
					_timeline._remove(this, true);
				}
			}
			
			return false;
		}
		
		/** @private Same as <code>kill()</code> except that it returns a Boolean that indicates whether or not important properties may have changed when the animation was killed. For example, when a MotionBlurPlugin is disabled, it swaps out a BitmapData for the target and may alter the opacity. We need to know this in order to determine whether or not a new tween that is overwriting this one should be re-initialized with the changed properties. **/
		public function _kill(vars:Object=null, target:Object=null):Boolean {
			return _enabled(false, false);
		}
		
		/**
		 * Kills the animation entirely or in part depending on the parameters. Simply calling <code>kill()</code>
		 * (omitting the parameters) will immediately stop the animation and release it for garbage collection. 
		 * To kill only particular tweening properties of the animation, use the first parameter which should
		 * be a generic object with enumerable properties corresponding to those that should be killed,
		 * like <code>{x:true, y:true}</code>. The second parameter allows you to define a target 
		 * (or array of targets) to affect. 
		 * 
		 * <p>Note: the values assigned to each property of the <code>vars</code> parameter object don't 
		 * matter - the sole purpose of the object is for iteration over the named properties. In other 
		 * words, <code>{x:true, y:true}</code> would produce the same results as <code>{x:false, y:false}</code>.</p>
		 * 
		 * <listing version="3.0">
 //kill the entire animation:
 myAnimation.kill();
 
 //kill only the "x" and "y" properties of the animation (all targets):
 myAnimation.kill({x:true, y:true});
 
 //kill all parts of the animation related to the target "myObject" (if the tween has multiple targets, the others will not be affected):
 myAnimation.kill(null, myObject);
 
 //kill only the "x" and "y" properties of animations of the target "myObject":
 myAnimation.kill({x:true, y:true}, myObject);
  
 //kill only the "opacity" properties of animations of the targets "myObject1" and "myObject2":
 myAnimation.kill({opacity:true}, [myObject1, myObject2]);
		 </listing>
		 * 
		 * @param vars To kill only specific properties, use a generic object containing enumerable properties corresponding to the ones that should be killed, like <code>{x:true, y:true}</code>. The values assigned to each property of the object don't matter - the sole purpose of the object is for iteration over the named properties (in this case, <code>x</code> and <code>y</code>). If no object (or <code>null</code>) is defined, <strong>ALL</strong> properties will be killed.
		 * @param target To kill only aspects of the animation related to a particular target (or targets), reference it here. For example, to kill only parts having to do with <code>myObject</code>, do <code>kill(null, myObject)</code> or to kill only parts having to do with <code>myObject1</code> and <code>myObject2</code>, do <code>kill(null, [myObject1, myObject2])</code>. If no target is defined, <strong>ALL</strong> targets will be affected. 
		 * @return self (makes chaining easier)
		 **/
		public function kill(vars:Object=null, target:Object=null):* {
			_kill(vars, target);
			return this;
		}
		
		/**
		 * @private
		 * Sets the <code>_dirty</code> property of all anscestor timelines (and optionally this instance too). Setting
		 * the <code>_dirty</code> property to <code>true</code> forces any necessary recalculation of its _duration and 
		 * _totalDuration properties and sorts the affected timelines' children animations so that they're in the proper order 
		 * next time the <code>duration</code> or <code>totalDuration</code> is requested. We don't just recalculate them 
		 * immediately because it can be much faster to do it this way.
		 * 
		 * @param includeSelf indicates whether or not this tween's <code>_dirty</code> property should be affected.
		 * @return self (makes chaining easier)
		 */
		protected function _uncache(includeSelf:Boolean):* {
			var tween:Animation = includeSelf ? this : timeline;
			while (tween) {
				tween._dirty = true;
				tween = tween.timeline;
			}
			return this;
		}
		
		/** @private This method gets called on every frame and is responsible for rendering/updating the root timelines. If you want to unhook the engine from its ticker, you could do <code>Animation.ticker.removeEventListener("enterFrame", _updateRoot)</code> and then call it yourself whenever you want to update. **/
		public static function _updateRoot(event:Event=null):void {
			_rootFrame++;
			_rootTimeline.render((getTimer() / 1000 - _rootTimeline._startTime) * _rootTimeline._timeScale, false, false);
			_rootFramesTimeline.render((_rootFrame - _rootFramesTimeline._startTime) * _rootFramesTimeline._timeScale, false, false);
			ticker.dispatchEvent(_tickEvent);
		}
		
		/** @private **/
		protected function _swapSelfInParams(params:Array):Array {
			var i:int = params.length,
				copy:Array = params.concat();
			while (--i > -1) {
				if (params[i] === "{self}") {
					copy[i] = this;
				}
			}
			return copy;
		}
		
		
//---- GETTERS / SETTERS ------------------------------------------------------------
		
		
		
		/**
		 * Gets or sets an event callback like <code>"onComplete", "onUpdate", "onStart", "onReverseComplete"</code>
		 * or <code>"onRepeat"</code> (<code>onRepeat</code> only applies to TweenMax or TimelineMax instances)
		 * along with any parameters that should be passed to that callback. This is the same as defining
		 * the values directly in the constructor's <code>vars</code> parameter initially, so the following 
		 * two lines are functionally equivalent:
		 * 
		 * <listing version="3.0">
//the following two lines produce IDENTICAL results:
var myAnimation = new TweenLite(mc, 1, {x:100, onComplete:myFunction, onCompleteParams:["param1","param2"]});
myAnimation.eventCallback("onComplete", myFunction, ["param1","param2"]);
</listing>
		 * <p>The benefit of using <code>eventCallback()</code> is that it allows you to set callbacks 
		 * even after the animation instance has been created and it also allows you to inspect the
		 * callback references or even delete them on-the-fly (use <code>null</code> to delete the 
		 * event callback).</p>
		 * 
		 * <listing version="3.0">
//deletes the onUpdate
myAnimation.eventCallback("onUpdate", null);
</listing>
		 * 
		 * <p><strong>IMPORTANT:</strong>Animation instances can only have one callback associated with each
		 * event type (one <code>onComplete</code>, one <code>onUpdate</code>, one <code>onStart</code>, etc.). 
		 * So setting a new value will overwrite the old one. All of the values populate the <code>vars</code>
		 * object too which was originally passed into the constructor (think of that like a storage place for 
		 * configuration data).</p>
		 * 
		 * <p>This method serves as both a getter and setter. Omitting all but the first parameter returns 
		 * the current value (getter), whereas defining more than the first parameter sets the value (setter) 
		 * and returns the instance itself for easier chaining, like 
		 * <code>myAnimation.eventCallback("onComplete", completeHandler).eventCallback("onUpdate", updateHandler, ["param1","{self}"]).play(1);</code></p>
		 * 
		 * <listing version="3.0">
var currentOnComplete = myAnimation.eventCallback("onComplete"); //gets current onComplete
myAnimation.eventCallback("onComplete", myFunction); //sets the onComplete
</listing>
		 * 
		 * <p><strong>JavaScript and AS2 note:</strong> - Due to the way JavaScript and AS2 don't 
		 * maintain scope (what "<code>this</code>" refers to, or the context) in function calls, 
		 * it can be useful to define the scope specifically. Therefore, in the JavaScript and AS2 
		 * versions accept an extra (4th) parameter for <code>scope</code>.</p>
		 * 
		 * @param type The type of event callback, like <code>"onComplete", "onUpdate", "onStart"</code> or <code>"onRepeat"</code>. This is case-sensitive.
		 * @param callback The function that should be called when the event occurs.
		 * @param params An array of parameters to pass the callback. Use <code>"{self}"</code> to refer to the animation instance itself. Example: <code>["param1","{self}"]</code>
		 * @return Omitting the all but the first parameter returns the current value (getter), whereas defining more than the first parameter sets the callback (setter) and returns the instance itself for easier chaining.
		 */
		public function eventCallback(type:String, callback:Function=null, params:Array=null):* {
			if (type == null) {
				return null;
			} else if (type.substr(0,2) == "on") {
				if (arguments.length == 1) {
					return vars[type];
				}
				if (callback == null) {
					delete vars[type];
				} else {
					vars[type] = callback;
					vars[type + "Params"] = ((params is Array) && params.join("").indexOf("{self}") !== -1) ? _swapSelfInParams(params) : params;
				}
				if (type == "onUpdate") {
					_onUpdate = callback;
				}
			}
			return this;
		}
		
		
		/** 
		 * Gets or sets the animation's initial delay which is the length of time in seconds 
		 * (or frames for frames-based tweens) before the animation should begin. 
		 * A tween's starting values are not recorded until after the delay has expired (except in 
		 * <code>from()</code> tweens which render immediately by default unless <code>immediateRender:false</code> 
		 * is set in the <code>vars</code> parameter). An animation's <code>delay</code> is unaffected
		 * by its <code>timeScale</code>, so if you were to change <code>timeScale</code> from 1 to 10, 
		 * for example, it wouldn't cause the delay to grow tenfold.
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myAnimation.delay(2).timeScale(0.5).play(1);</code></p>
		 * 
		 * <listing version="3.0">
 var currentDelay = myAnimation.delay(); //gets current delay
 myAnimation.delay(2); //sets delay
</listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 **/
		public function delay(value:Number=NaN):* {
			if (!arguments.length) {
				return _delay;
			}
			if (_timeline.smoothChildTiming) {
				startTime( _startTime + value - _delay );
			}
			_delay = value;
			return this;
		}
		
		/**
		 * Gets or sets the animation's duration, <strong>not</strong> including any repeats or repeatDelays 
		 * (which are only available in TweenMax and TimelineMax). For example, if a TweenMax instance has 
		 * a <code>duration</code> of 2 and a <code>repeat</code> of 3, its <code>totalDuration</code> 
		 * would be 8 (one standard play plus 3 repeats equals 4 total cycles).
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myAnimation.duration(2).delay(0.5).play(1);</code></p>
		 * 
		 * <listing version="3.0">
 var currentDuration = myAnimation.duration(); //gets current duration
 myAnimation.duration(2); //sets duration
</listing>
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #totalDuration()
		 * @see #timeScale()
		 **/
		public function duration(value:Number=NaN):* {
			if (!arguments.length) {
				_dirty = false;
				return _duration;
			}
			_duration = _totalDuration = value;
			_uncache(true); //true in case it's a TweenMax or TimelineMax that has a repeat - we'll need to refresh the totalDuration. 
			if (_timeline.smoothChildTiming) if (_time > 0) if (_time < _duration) if (value != 0) {
				totalTime(_totalTime * (value / _duration), true);
			}
			return this;
		}
		
		/**
		 * Gets or sets the animation's <strong>total</strong> duration including 
		 * any repeats or repeatDelays (which are only available in TweenMax and TimelineMax). 
		 * For example, if a TweenMax instance has a <code>duration</code> of 2 and a repeat of 3, 
		 * its <code>totalDuration</code> would be 8 (one standard play plus 3 repeats equals 4 total cycles).
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myAnimation.totalDuration(2).delay(0.5).play(1);</code></p>
		 * 
		 * <listing version="3.0">
 var ctd = myAnimation.totalDuration(); //gets current total duration
 myAnimation.totalDuration(2); //sets total duration
		 </listing>
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #duration()
		 * @see #timeScale()
		 **/
		public function totalDuration(value:Number=NaN):* {
			_dirty = false;
			return (!arguments.length) ? _totalDuration : duration(value);
		}
		
		/**
		 * Gets or sets the local position of the playhead (essentially the current time), 
		 * described in seconds (or frames for frames-based animations) which
		 * will never be less than 0 or greater than the animation's <code>duration</code>. 
		 * For example, if the <code>duration</code> is 10 and you were to watch the 
		 * <code>time</code> during the course of the animation, you'd see it go from 0
		 * at the beginning to 10 at the end. Setting <code>time</code> to 5 would cause the 
		 * animation to jump to its midway point (because it's half of the duration). 
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining.</p>
		 * 
		 * <listing version="3.0">
var currentTime = myAnimation.time(); //gets current time
myAnimation.time(2); //sets time, jumping to new value just like seek().
</listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining. Negative values will be interpreted from the <strong>END</strong> of the animation.
		 * @param suppressEvents If <code>true</code>, no events or callbacks will be triggered when the playhead moves to the new position defined in the <code>value</code> parameter.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #seek()
		 * @see #play()
		 * @see #reverse()
		 * @see #pause()
		 * @see #totalTime()
		 **/
		public function time(value:Number=NaN, suppressEvents:Boolean=false):* {
			if (!arguments.length) {
				return _time;
			}
			if (_dirty) {
				totalDuration();
			}
			if (value > _duration) {
				value = _duration;
			}
			return totalTime(value, suppressEvents);
		}
		
		/**
		 * Gets or sets the position of the playhead according to the <code>totalDuration</code>
		 * which <strong>includes any repeats and repeatDelays</strong> (only available 
		 * in TweenMax and TimelineMax). For example, if a TweenMax instance has a 
		 * <code>duration</code> of 2 and a <code>repeat</code> of 3, <code>totalTime</code> 
		 * will go from 0 to 8 during the course of the tween (plays once then repeats 3 times, 
		 * making 4 total cycles) whereas <code>time</code> will go from 0 to 2 a total of 4 times. 
		 * If you added a <code>repeatDelay</code> of 1, that would make the <code>totalTime</code>
		 * go from 0 to 11 over the course of the tween. 
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining.</p>
		 * 
		 * <p><code>totalTime</code> will never exceed the <code>totalDuration</code>, nor will it be 
		 * less than 0 (values will be clipped appropriately). Negative values will be interpreted from 
		 * the <strong>END</strong> of the animation. For example, -2 would be 2 seconds before the end. If the 
		 * animation's <code>totalDuration</code> is 6 and you do <code>myAnimation.totalTime(-2)</code>, 
		 * it will jump to a <code>totalTime</code> of 4.</p>
		 * 
		 * <listing version="3.0">
 var tt = myAnimation.totalTime(); //gets total time
 myAnimation.totalTime(2); //sets total time, jumping to new value just like seek().
		 </listing>
		 * 
		 * @param time Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining. Negative values will be interpreted from the <strong>END</strong> of the animation.
		 * @param suppressEvents If <code>true</code>, no events or callbacks will be triggered when the playhead moves to the new position defined in the <code>time</code> parameter.
		 * @param uncapped By default, the <code>time</code> will be capped at <code>totalDuration</code> and if a negative number is used, it will be measured from the END of the animation, but if <code>uncapped</code> is <code>true</code>, the <code>time</code> won't be adjusted at all (negatives will be allowed, as will values that exceed totalDuration).
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #time()
		 * @see #seek()
		 * @see #play()
		 * @see #reverse()
		 * @see #pause()
		 **/
		public function totalTime(time:Number=NaN, suppressEvents:Boolean=false, uncapped:Boolean=false):* {
			if (!arguments.length) {
				return _totalTime;
			}
			if (_timeline) {
				if (time < 0 && !uncapped) {
					time += totalDuration();
				}
				if (_timeline.smoothChildTiming) {
					if (_dirty) {
						totalDuration();
					}
					if (time > _totalDuration && !uncapped) {
						time = _totalDuration;
					}
					var tl:SimpleTimeline = _timeline;
					_startTime = (_paused ? _pauseTime : tl._time) - ((!_reversed ? time : _totalDuration - time) / _timeScale);
					if (!_timeline._dirty) { //for performance improvement. If the parent's cache is already dirty, it already took care of marking the anscestors as dirty too, so skip the function call here.
						_uncache(false);
					}
					//in case any of the ancestor timelines had completed but should now be enabled, we should reset their totalTime() which will also ensure that they're lined up properly and enabled. Skip for animations that are on the root (wasteful). Example: a TimelineLite.exportRoot() is performed when there's a paused tween on the root, the export will not complete until that tween is unpaused, but imagine a child gets restarted later, after all [unpaused] tweens have completed. The startTime of that child would get pushed out, but one of the ancestors may have completed.
					if (tl._timeline != null) { 
						while (tl._timeline) {
							if (tl._timeline._time !== (tl._startTime + tl._totalTime) / tl._timeScale) {
								tl.totalTime(tl._totalTime, true);
							}
							tl = tl._timeline;
						}
					}
				}
				if (_gc) {
					_enabled(true, false);
				}				
				if (_totalTime != time || _duration === 0) {
					render(time, suppressEvents, false);
				}
			}
			return this;
		}
		
		/** 
		 * Gets or sets the animations's progress which is a value between 0 and 1 indicating the position 
		 * of the virtual playhead (<strong>excluding</strong> repeats) where 0 is at the beginning, 0.5 is at the halfway point, 
		 * and 1 is at the end (complete). If the animation has a non-zero <code>repeat</code> defined (only available in TweenMax and TimelineMax), 
		 * <code>progress()</code> and <code>totalProgress()</code> will be different because <code>progress()</code> doesn't include the 
		 * <code>repeat</code> or <code>repeatDelay</code> whereas <code>totalProgress()</code> does. For example, if a TimelineMax instance 
		 * is set to repeat once, at the end of the first cycle <code>totalProgress()</code> would only be 0.5 
		 * whereas <code>progress()</code> would be 1. If you watched both properties over the course of the entire 
		 * animation, you'd see <code>progress()</code> go from 0 to 1 twice (once for each cycle) in the 
		 * same time it takes the <code>totalProgress()</code> to go from 0 to 1 once.
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myAnimation.progress(0.5).play();</code></p>
		 * 
		 * <listing version="3.0">
var progress = myAnimation.progress(); //gets current progress
myAnimation.progress(0.25); //sets progress to one quarter finished
		 </listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @param suppressEvents If <code>true</code>, no events or callbacks will be triggered when the playhead moves to the new position.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #seek()
		 * @see #time()
		 * @see #totalTime()
		 * @see #totalProgress()
		 **/
		public function progress(value:Number=NaN, suppressEvents:Boolean=false):* {
			return (!arguments.length) ? _time / duration() : totalTime(duration() * value, suppressEvents);
		}
		
		/** 
		 * Gets or sets the animation's total progress which is a value between 0 and 1 indicating the position 
		 * of the virtual playhead (<strong>including</strong> repeats) where 0 is at the beginning, 0.5 is 
		 * at the halfway point, and 1 is at the end (complete). If the animation has a non-zero <code>repeat</code> defined  (only available in TweenMax and TimelineMax), 
		 * <code>progress()</code> and <code>totalProgress()</code> will be different because <code>progress()</code> 
		 * doesn't include the <code>repeat</code> or <code>repeatDelay</code> whereas <code>totalProgress()</code> does. For example, 
		 * if a TimelineMax instance is set to repeat once, at the end of the first cycle <code>totalProgress()</code> 
		 * would only be 0.5 whereas <code>progress</code> would be 1. If you watched both properties over the 
		 * course of the entire animation, you'd see <code>progress</code> go from 0 to 1 twice (once for 
		 * each cycle) in the same time it takes the <code>totalProgress()</code> to go from 0 to 1 once.
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myAnimation.totalProgress(0.5).play();</code></p>
		 * 
		 * <listing version="3.0">
var progress = myAnimation.totalProgress(); //gets total progress
myAnimation.totalProgress(0.25); //sets total progress to one quarter finished
		 </listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @param suppressEvents If <code>true</code>, no events or callbacks will be triggered when the playhead moves to the new position.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #progress()
		 * @see #seek()
		 * @see #time()
		 * @see #totalTime()
		 **/
		public function totalProgress(value:Number=NaN, suppressEvents:Boolean=false):* {
			return (!arguments.length) ? _time / duration() : totalTime(duration() * value, suppressEvents);
		}
		
		/** 
		 * Gets or sets the time at which the animation begins on its parent timeline (after any <code>delay</code>
		 * that was defined). For example, if a tween starts at exactly 3 seconds into the timeline 
		 * on which it is placed, the tween's <code>startTime</code> would be 3. 
		 * 
		 * <p>The <code>startTime</code> may be automatically adjusted to make the timing appear
		 * seamless if the parent timeline's <code>smoothChildTiming</code> property is <code>true</code> 
		 * and a timing-dependent change is made on-the-fly, like <code>reverse()</code> is called or 
		 * <code>timeScale()</code> is changed, etc. See the documentation for the <code>smoothChildTiming</code> 
		 * property of timelines for more details.</p>
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining.</p>
		 * 
		 * <listing version="3.0">
var start = myAnimation.startTime(); //gets current start time
myAnimation.startTime(2); //sets the start time
</listing>
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 **/
		public function startTime(value:Number=NaN):* {
			if (!arguments.length) {
				return _startTime;
			}
			if (value != _startTime) {
				_startTime = value;
				if (timeline) if (timeline._sortChildren) {
					timeline.add(this, value - _delay); //ensures that any necessary re-sequencing of Animations in the timeline occurs to make sure the rendering order is correct.
				}
			}
			return this;
		}
		
		/** 
		 * Factor that's used to scale time in the animation where 1 = normal speed (the default),
		 * 0.5 = half speed, 2 = double speed, etc. For example, if an animation's <code>duration</code> 
		 * is 2 but its <code>timeScale</code> is 0.5, it will take 4 seconds to finish. If you nest that 
		 * animation in a timeline whose <code>timeScale</code> is 0.5 as well, it would take 8 seconds 
		 * to finish. You can even tween the <code>timeScale</code> to gradually slow it down or speed it up.
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myAnimation.timeScale(2).play(1);</code></p>
		 * 
		 * <listing version="3.0">
var currentTimeScale = myAnimation.timeScale(); //gets current timeScale
myAnimation.timeScale( 0.5 ); //sets timeScale to half-speed
</listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #duration()
		 **/
		public function timeScale(value:Number=NaN):* {
			if (!arguments.length) {
				return _timeScale;
			}
			value = value || 0.000001; //can't allow zero because it'll throw the math off
			if (_timeline && _timeline.smoothChildTiming) {
				var t:Number = (_pauseTime || _pauseTime == 0) ? _pauseTime : _timeline._totalTime;
				_startTime = t - ((t - _startTime) * _timeScale / value);
			}
			_timeScale = value;
			return _uncache(false);
		}
		
		/** 
		 * Gets or sets the animation's reversed state which indicates whether or not the animation 
		 * should be played backwards. This value is not affected by <code>yoyo</code> repeats 
		 * (TweenMax and TimelineMax only) and it does not take into account the reversed state of 
		 * anscestor timelines. So for example, a tween that is not reversed might appear reversed 
		 * if its parent timeline (or any ancenstor timeline) is reversed. 
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining.</p>
		 * 
		 * <listing version="3.0">
var rev = myAnimation.reversed(); //gets current orientation
myAnimation.reversed( true ); //sets the orientation to reversed
myAnimation.reversed( !myAnimation.reversed() ); //toggles the orientation
</listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #reverse()
		 * @see #play()
		 **/
		public function reversed(value:Boolean=false):* {
			if (!arguments.length) {
				return _reversed;
			}
			if (value != _reversed) {
				_reversed = value;
				totalTime(((_timeline && !_timeline.smoothChildTiming) ? totalDuration() - _totalTime : _totalTime), true);
			}
			return this;
		}
		
		/** 
		 * Gets or sets the animation's paused state which indicates whether or not the animation 
		 * is currently paused. This does not take into account anscestor timelines. So for example, 
		 * a tween that is not paused might appear paused if its parent timeline (or any ancenstor 
		 * timeline) is paused. Pausing an animation doesn't remove it from its parent timeline, 
		 * but it does cause it not to be factored into the parent timeline's 
		 * <code>duration/totalDuration</code>. When an animation completes, it does 
		 * <strong>NOT</strong> alter its paused state.
		 * 
		 * <p>In most cases, it is easiest to use the <code>pause()</code> method to pause
		 * the animation, and <code>resume()</code> to resume it. But to check the current
		 * state, you must use the <code>paused()</code> method. It can also be useful for 
		 * toggling like <code>myAnimation.paused( !myAnimation.paused() );</code></p>
		 * 
		 * <p>You can set the <code>paused</code> state initially by passing <code>paused:true</code>
		 * in the <code>vars</code> parameter.</p>
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myAnimation.paused(true).delay(2).timeScale(0.5);</code></p>
		 * 
		 * <listing version="3.0">
 var paused = myAnimation.paused(); //gets current paused state
 myAnimation.paused( true ); //sets paused state to true (just like pause())
 myAnimation.paused( !myAnimation.paused() ); //toggles the paused state
		 </listing>
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #pause()
		 * @see #resume()
		 * @see #play()
		 **/
		public function paused(value:Boolean=false):* {
			if (!arguments.length) {
				return _paused;
			}
			if (value != _paused) if (_timeline) {
				var raw:Number = _timeline.rawTime(),
					elapsed:Number = raw - _pauseTime;
				if (!value && _timeline.smoothChildTiming) {
					_startTime += elapsed;
					_uncache(false);
				}
				_pauseTime = value ? raw : NaN;
				_paused = value;
				_active = (!value && _totalTime > 0 && _totalTime < _totalDuration);
				if (!value && elapsed != 0 && _initted && duration() !== 0) {
					render((_timeline.smoothChildTiming ? _totalTime : (raw - _startTime) / _timeScale), true, true); //in case the target's properties changed via some other tween or manual update by the user, we should force a render.
				}
			}
			if (_gc && !value) {
				_enabled(true, false);
			}
			return this;
		}

	}
}