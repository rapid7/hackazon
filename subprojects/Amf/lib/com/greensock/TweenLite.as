/**
 * VERSION: 12.1.5
 * DATE: 2014-07-19
 * AS3 (AS2 version is also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock {
	import com.greensock.core.Animation;
	import com.greensock.core.PropTween;
	import com.greensock.core.SimpleTimeline;
	import com.greensock.easing.Ease;
	
	import flash.display.Shape;
	import flash.events.Event;
	import flash.utils.Dictionary;

/**
 * TweenLite is an extremely fast, lightweight, and flexible animation tool that serves as the foundation of 
 * the GreenSock Animation Platform (GSAP), available in AS2, AS3, and JavaScript. A TweenLite instance handles 
 * tweening one or more properties of <strong>any object</strong> (or array of objects) over time. TweenLite
 * can be used on its own to accomplish most animation chores with minimal file size or it can be use in 
 * conjuction with advanced sequencing tools like TimelineLite or TimelineMax to make complex tasks much
 * simpler. With scores of other animation frameworks to choose from, why consider the GreenSock Animation Platform?:
 * 
 * 	<ul>
 * 		<li><strong> SPEED </strong>- The platform has been highly optimized for maximum performance. See some 
 *			speed comparisons yourself at <a href="http://www.greensock.com/tweening-speed-test/">http://www.greensock.com/tweening-speed-test/</a></li>
 * 		  
 * 		<li><strong> Freakishly robust feature set </strong>- In addition to tweening any numeric property of any object, 
 * 			TweenLite has plugins that give it the ability to tween hex colors, beziers, arrays, filters, plus 
 * 			<strong>LOTS</strong> more. It can round values, use relative values, smoothly reverse() on the 
 * 			fly, automatically detect and accommodate getter/setter functions, employ virtually any easing 
 * 			equation, <code>pause()/resume()</code> anytime, and intelligently manage conflicting tweens of 
 * 			the same object with various overwrite modes. TweenMax extends TweenLite and adds even 
 * 			more capabilities like repeat, yoyo, repeatDelay, on-the-fly destination value 
 * 			updates and more.</li>
 * 		  
 * 		<li><strong> Sequencing, grouping, and management features </strong>- TimelineLite and TimelineMax 
 * 			make it surprisingly simple to create complex sequences or groups of tweens that you can 
 * 			control as a whole. play(), pause(), restart(),  or reverse(). You can even tween a timeline's 
 * 			<code>time</code> or <code>progress</code> to fastforward or rewind the entire timeline. Add 
 * 			labels, change the timeline's timeScale, nest timelines within timelines, and much more.
 * 			This can revolutionize your animation workflow, making it more modular and concise.</li>
 * 
 * 		<li><strong> AS3, AS2, and JavaScript </strong>- Most other engines are only developed for one language, 
 * 			but the GreenSock Animation Platform allows you to use a consistent API across all your Flash and
 * 			HTML5 projects.</li>
 * 
 * 		<li><strong> Ease of use </strong>- Designers and Developers alike rave about how intuitive the platform is.</li>
 * 		
 * 		<li><strong> Support and reliability </strong>- With frequent updates, <a href="http://forums.greensock.com">dedicated forums</a>, 
 * 			committed authorship, a solid track record, a proven funding mechansim, and a thriving community of users, 
 * 			the platform is a safe long-term bet (unlike many open source projects).</li>
 * 
 * 		<li><strong> Expandability </strong>- With its plugin architecture, you can activate as many (or as few) 
 * 			extra features as your project requires. Write your own plugin to handle particular special 
 * 			properties in custom ways. Minimize bloat and maximize performance.</li>
 * 		
 * 	</ul>
 * 
 * <p><strong>USAGE</strong></p>
 * <p>The most common type of tween is a <a href="TweenLite.html#to()">to()</a> tween which allows you 
 * to define the destination values:</p>
 * 
 * <p><code>
 * TweenLite.to(myObject, 2, {x:100, y:200});
 * </code></p>
 * 
 * <p>The above code will tween <code>myObject.x</code> from whatever it currently is to 100 and 
 * <code>myObject.y</code> property to 200 over the course of 2 seconds. Notice the x and y values are 
 * defined inside a generic object (between curly braces). Put as many properties there as you want.</p>
 * 
 * <p>By default, tweens begin immediately, although you can delay them using the <code>delay</code>
 * special property or pause them initially using the <code>paused</code> special property (see below).</p>
 * 
 * <p>The <code>target</code> can also be an array of objects. For example, the following tween will
 * tween the alpha property to 0.5 and y property to 100 for obj1, obj2, and obj3:</p>
 * 
 * <p><code>
 * TweenLite.to([obj1, obj2, obj3], 1, {alpha:0.5, y:100});
 * </code></p>
 * 
 * <p>You can also use a <a href="TweenLite.html#from()">from()</a> tween if you want to define the 
 * <strong>starting</strong> values instead of the ending values so that the target tweens <em>from</em> 
 * the defined values to wherever they currently are. Or a <a href="TweenLite.html#fromTo()">fromTo()</a> 
 * lets you define both starting and ending values.</p>
 * 
 * <p>Although the <code>to()</code>, <code>from()</code>, and <code>fromTo()</code> static methods
 * are popular because they're quick and can avoid some garbage collection hassles, you can also
 * use the more object-oriented syntax like this:</p>
 * 
 * <p><code>
 * var tween = new TweenLite(myObject, 2, {x:100, y:200});
 * </code></p>
 * 
 * <p>or even:</p>
 * 
 * <p><code>
 * var tween = TweenLite.to(myObject, 2, {x:100, y:200});
 * </code></p>
 * 
 * 
 * <p><strong>SPECIAL PROPERTIES (no plugins required):</strong></p>
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
 * 				 or <code>StrongInOut.ease</code>. For best performance, use one of the GreenSock eases
 * 				 (which are in the <code>com.greensock.easing</code> package). TweenLite also works with 
 * 				 any standard easing equation that uses the typical 4 parameters (<code>time, start, 
 * 				 change, duration</code>) like Adobe's <code>fl.motion.easing</code> eases.
 * 				 The default is <code>Power1.easeOut</code>. For linear animation, use the GreenSock 
 * 				 <code>Linear.ease</code> ease</li>
 * 	
 * 	<li><strong> onComplete </strong>:<em> Function</em> -
 * 				 A function that should be called when the tween has completed</li>
 * 	
 * 	<li><strong> onCompleteParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the <code>onComplete</code> function. For example,
 * 				 <code>TweenLite.to(mc, 1, {x:100, onComplete:myFunction, onCompleteParams:[mc, "param2"]});</code>
 * 				 To self-reference the tween instance itself in one of the parameters, use <code>"{self}"</code>,
 * 				 like: <code>onCompleteParams:["{self}", "param2"]</code></li>
 * 	
 * 	<li><strong> useFrames </strong>:<em> Boolean</em> -
 * 				 If <code>useFrames</code> is <code>true</code>, the tweens's timing will be 
 * 				 based on frames instead of seconds because it is intially added to the root
 * 				 frames-based timeline. This causes both its <code>duration</code>
 * 				 and <code>delay</code> to be based on frames. An animations's timing mode is 
 * 				 always determined by its parent <code>timeline</code>.</li>
 * 	
 * 	<li><strong> easeParams </strong>:<em> Array</em> [deprecated] -
 * 				 Some GreenSock eases (like <code>OutIn</code> or <code>ElasticOut</code>) have a <code>config()</code> 
 * 				 method that allows them to be configured to change their behavior (like <code>TweenLite.to(mc, 1, {x:100, ease:ElasticOut.ease.config(0.5, 1)})</code>
 * 				 but if you are using a non-GreenSock ease that accepts extra parameters like Adobe's
 * 				 <code>fl.motion.easing.Elastic</code>, <code>easeParams</code> allows you to define 
 * 				 those extra parameters as an array like <code>TweenLite.to(mc, 1, {x:100, ease:Elastic.easeOut, easeParams:[0.5, 1]})</code>. 
 * 				 Most easing equations, however, don't require extra parameters so you won't need to 
 * 				 pass in any easeParams. GreenSock eases provide the best performance, so use them 
 * 				 whenever possible.</li>
 * 	
 * 	<li><strong> immediateRender </strong>:<em> Boolean</em> -
 * 				 Normally when you create a tween, it begins rendering on the very next frame (update cycle) 
 * 				 unless you specify a <code>delay</code>. However, if you prefer to force the tween to 
 * 				 render immediately when it is created, set <code>immediateRender</code> to <code>true</code>. 
 * 				 Or to prevent a <code>from()</code> from rendering immediately, set <code>immediateRender</code> 
 * 				 to <code>false</code>. By default, <code>from()</code> tweens set <code>immediateRender</code> to <code>true</code>.</li>
 * 
 *  <li><strong> onStart </strong>:<em> Function</em> -
 * 				 A function that should be called when the tween begins (when its <code>time</code>
 * 				 changes from 0 to some other value which can happen more than once if the 
 * 				 tween is restarted multiple times).</li>
 * 	
 * 	<li><strong> onStartParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the <code>onStart</code> function. For example, 
 * 				 <code>TweenLite.to(mc, 1, {x:100, delay:1, onStart:myFunction, onStartParams:[mc, "param2"]});</code>
 * 				 To self-reference the tween instance itself in one of the parameters, use <code>"{self}"</code>,
 * 				 like: <code>onStartParams:["{self}", "param2"]</code></li>
 * 	
 * 	<li><strong> onUpdate </strong>:<em> Function</em> -
 * 				 A function that should be called every time the tween updates  
 * 				 (on every frame while the tween is active)</li>
 * 	
 * 	<li><strong> onUpdateParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the <code>onUpdate</code> function. For example,
 * 				 <code>TweenLite.to(mc, 1, {x:100, onUpdate:myFunction, onUpdateParams:[mc, "param2"]});</code>
 * 				 To self-reference the tween instance itself in one of the parameters, use <code>"{self}"</code>,
 * 				 like: <code>onUpdateParams:["{self}", "param2"]</code></li>
 * 	
 * 	<li><strong> onReverseComplete </strong>:<em> Function</em> -
 * 				 A function that should be called when the tween has reached its beginning again from the 
 * 				 reverse direction. For example, if <code>reverse()</code> is called the tween will move
 * 				 back towards its beginning and when its <code>time</code> reaches 0, <code>onReverseComplete</code>
 * 				 will be called. This can also happen if the tween is placed in a TimelineLite or TimelineMax instance
 * 				 that gets reversed and plays the tween backwards to (or past) the beginning.</li>
 * 	
 * 	<li><strong> onReverseCompleteParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the <code>onReverseComplete</code> function. For example, 
 * 				 <code>TweenLite.to(mc, 1, {x:100, onReverseComplete:myFunction, onReverseCompleteParams:[mc, "param2"]});</code>
 * 				 To self-reference the tween instance itself in one of the parameters, use <code>"{self}"</code>,
 * 				 like: <code>onReverseCompleteParams:["{self}", "param2"]</code></li>
 * 
 *  <li><strong> paused </strong>:<em> Boolean</em> -
 * 				 If <code>true</code>, the tween will pause itself immediately upon creation.</li>
 * 	
 * 	<li><strong> overwrite </strong>:<em> String (or integer)</em> -
 * 				 Controls how (and if) other tweens of the same target are overwritten. 
 * 				 There are several modes to choose from, but <code>"auto"</code> is the default (although
 * 				 you can change the default mode using the <code>TweenLite.defaultOverwrite</code> property):
 * 					<ul>
 * 			  			<li><code>"none"</code> (0) (or <code>false</code>) - no overwriting will occur.</li>
 * 						
 * 						<li><code>"all"</code> (1) (or <code>true</code>) - immediately overwrites all existing 
 * 									tweens of the same target even if they haven't started yet or don't have 
 * 									conflicting properties.</li>
 * 													
 * 						<li><code>"auto"</code> (2) - when the tween renders for the first time, it will analyze 
 * 									tweens of the same target that are currently active/running and only overwrite 
 * 									individual tweening properties that overlap/conflict. Tweens that haven't begun
 * 									yet are ignored. For example, if another active tween is found that is tweening
 * 									3 properties, only 1 of which it shares in common with the new tween, the other
 * 									2 properties will be left alone. Only the conflicting property gets overwritten/killed.
 * 									This is the default mode and typically the most intuitive for developers.</li>
 * 							
 * 						<li><code>"concurrent"</code> (3) - when the tween renders for the first time, it kills
 * 									only the active (in-progress) tweens of the same target regardless of whether 
 * 									or not they contain conflicting properties. Like a mix of <code>"all"</code> 
 * 									and <code>"auto"</code>. Good for situations where you only want one tween 
 * 									controling the target at a time.</li>
 * 												
 * 						<li><code>"allOnStart"</code> (4) - Identical to <code>"all"</code> but waits to run
 * 									the overwrite logic until the tween begins (after any delay). Kills
 * 									tweens of the same target even if they don't contain conflicting properties
 * 									or haven't started yet.</li>
 * 												
 * 						<li><code>"preexisting"</code> (5) - when the tween renders for the first time, it kills
 * 									only the tweens of the same target that existed BEFORE this tween was created
 * 									regardless of their scheduled start times. So, for example, if you create a tween
 * 									with a delay of 10 and then a tween with a delay of 1 and then a tween with a 
 * 									delay of 2 (all of the same target), the 2nd tween would overwrite the first
 * 									but not the second even though scheduling might seem to dictate otherwise. 
 * 									<code>"preexisting"</code> only cares about the order in which the instances
 * 									were actually created. This can be useful when the order in which your code runs 
 * 									plays a critical role.</li>
 * 
 * 					</ul></li>
 * 	</ul>
 * 
 * <p><strong>AS3 note:</strong> In AS3, using a <code><a href="data/TweenLiteVars.html">TweenLiteVars</a></code> 
 * instance instead of a generic object to define your <code>vars</code> is a bit more verbose but provides 
 * code hinting and improved debugging because it enforces strict data typing. Use whichever one you prefer.</p>
 * 
 * 
 * <p><strong>PLUGINS:</strong></p>
 * 
 * <p>Think of plugins like special properties that are dynamically added, delivering extra abilities without
 * forcing them to be baked into the core engine, keeping it relatively lean and mean. Each plugin is associated 
 * with a property name and it takes responsibility for handling that property. For example, the TintPlugin 
 * is associated with the "tint" property name so if it is activated it will intercept the "tint" property 
 * in the following tween and manage it uniquely:</p>
 * 
 * <p><code>
 * TweenLite.to(mc, 1, {tint:0xFF0000});
 * </code></p>
 * 
 * <p>If the TintPlugin wasn't activated, TweenLite would act as though you were trying to literally tween the 
 * <code>mc.tint</code> property (and there is no such thing).</p>
 * 
 * <p>In the JavaScript version of TweenLite, activating a plugin is as simple as loading the associated .js file. 
 * No extra activation code is necessary. In the ActionScript version, activating a plugin requires a single line 
 * of code and you only need to do it once, so it's pretty easy. Simply pass an Array containing the names of all 
 * the plugins you'd like to activate to the <code>TweenPlugin.activate()</code> method, like this:</p>
 * 
 * <p><code>
 * TweenPlugin.activate([FrameLabelPlugin, ColorTransformPlugin, TintPlugin]);
 * </code></p>
 * 
 * <p>To make it even easier, there is a <a href="http://www.greensock.com/tweenlite/#plugins">Plugin Explorer</a>
 * which writes the code for you. All you need to do is select the plugins and copy/paste the code 
 * from the bottom of the tool. It also displays interactive examples of each plugin and the assocaited 
 * code so that it’s easy to see the correct syntax.</p>
 * 
 * 
 * <p><strong>EXAMPLES:</strong></p>
 * 
 * <p>Please see <a href="http://www.greensock.com">http://www.greensock.com</a> for examples, tutorials, and interactive demos. </p>
 * 
 * <strong>NOTES / TIPS:</strong>
 * <ul> 	  
 * 	<li> Passing values as Strings and a preceding "+=" or "-=" will make the tween relative to the 
 * 		current value. For example, if you do <code>TweenLite.to(mc, 2, {x:"-=20"});</code> it'll 
 * 		tween <code>mc.x</code> to the left 20 pixels. <code>{x:"+=20"}</code> would move it to the right.</li>
 * 	  
 * 	<li> You can change the <code>TweenLite.defaultEase</code> if you prefer something other 
 * 		than <code>Power1.easeOut</code>.</li>
 * 	
 * 	<li> Kill all tweens of a particular object anytime with <code>TweenLite.killTweensOf(myObject);</code></li>
 * 	  
 * 	<li> You can kill all delayedCalls to a particular function using <code>TweenLite.killDelayedCallsTo(myFunction);</code> 
 * 		 or <code>TweenLite.killTweensOf(myFunction);</code></li>
 * 	  
 * 	<li> Use the <code>TweenLite.from()</code> method to animate things into place. For example, 
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
 * <p><strong>Copyright 2006-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */	 
	public class TweenLite extends Animation {
		
		/** @private **/
		public static const version:String = "12.1.5";
		
		/** Provides An easy way to change the default easing equation. Choose from any of the GreenSock eases in the <code>com.greensock.easing</code> package. @default Power1.easeOut **/
		public static var defaultEase:Ease = new Ease(null, null, 1, 1);
		
		/** Provides An easy way to change the default overwrite mode. Choose from any of the following: <code>"auto", "all", "none", "allOnStart", "concurrent", "preexisting"</code>. @default "auto" **/
		public static var defaultOverwrite:String = "auto";
		
		/**
		 * The object that dispatches a <code>"tick"</code> event each time the engine updates, making it easy for 
		 * you to add your own listener(s) to run custom logic after each update (great for game developers).
		 * Add as many listeners as you want. The basic syntax is the same for all versions (AS2, AS3, and JavaScript):
		 * 
		 * <p><strong>Basic example (AS2, AS3, and JavaScript):</strong></p><listing version="3.0">
 //add listener
 TweenLite.ticker.addEventListener("tick", myFunction);
 
 function myFunction(event) {
 	//executes on every tick after the core engine updates
 }
		 
 //to remove the listener later...
 TweenLite.ticker.removeEventListener("tick", myFunction);
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
		 * <p><strong>Advanced example (JavaScript and AS2):</strong></p><listing version="3.0">
 //add listener that requests an event object parameter, binds scope to the current scope (this), and sets priority to 1 so that it is called before any other listeners that had a priority lower than 1...
 TweenLite.ticker.addEventListener("tick", myFunction, this, true, 1);
 
 function myFunction(event) {
 	//executes on every tick after the core engine updates
 }
 
 //to remove the listener later...
 TweenLite.ticker.removeEventListener("tick", myFunction);
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
 TweenLite.ticker.addEventListener("tick", myFunction, false, 0, true);
 
 function myFunction(event:Event):void {
 	//executes on every tick after the core engine updates
 }
 
 //to remove the listener later...
 TweenLite.ticker.removeEventListener("tick", myFunction);
		</listing>
		 **/
		public static var ticker:Shape = Animation.ticker;
		
		/** @private When plugins are activated, the class is added (named based on the special property) to this object so that we can quickly look it up in the <code>_initProps()</code> method.**/
		public static var _plugins:Object = {}; 
		
		/** @private For notifying plugins of significant events like when the tween finishes initializing or when it is disabled/enabled (some plugins need to take actions when those events occur). TweenPlugin sets this (in order to keep file size small, avoiding dependencies on that or other classes) **/
		public static var _onPluginEvent:Function;
		
		/** @private Holds references to all our tween instances organized by target for quick lookups (for overwriting). **/
		protected static var _tweenLookup:Dictionary = new Dictionary(false); 
		
		/** @private Lookup for all of the reserved "special property" keywords (excluding plugins).**/
		protected static var _reservedProps:Object = {ease:1, delay:1, overwrite:1, onComplete:1, onCompleteParams:1, onCompleteScope:1, useFrames:1, runBackwards:1, startAt:1, onUpdate:1, onUpdateParams:1, onUpdateScope:1, onStart:1, onStartParams:1, onStartScope:1, onReverseComplete:1, onReverseCompleteParams:1, onReverseCompleteScope:1, onRepeat:1, onRepeatParams:1, onRepeatScope:1, easeParams:1, yoyo:1, onCompleteListener:1, onUpdateListener:1, onStartListener:1, onReverseCompleteListener:1, onRepeatListener:1, orientToBezier:1, immediateRender:1, repeat:1, repeatDelay:1, data:1, paused:1, reversed:1};
		
		/** @private An object for associating String overwrite modes with their corresponding integers (faster) **/
		protected static var _overwriteLookup:Object;
		
		
		/** [READ-ONLY] Target object (or array of objects) whose properties the tween affects. **/
		public var target:Object; 
		
		/** @private The result of feeding the tween's current progress (0-1) into the easing equation - typically between 0 and 1 but not always (like with <code>ElasticOut.ease</code>). **/
		public var ratio:Number;
		
		/** @private Lookup object for PropTween objects. For example, if this tween is handling the "x" and "y" properties of the target, the _propLookup object will have an "x" and "y" property, each pointing to the associated PropTween object (for tweens with targets that are arrays, _propTween will be an Array with corresponding objects). This can be very helpful for speeding up overwriting. **/
		public var _propLookup:Object;
		
		/** @private First PropTween instance in the linked list. **/
		public var _firstPT:PropTween;
		
		/** @private Only used for tweens whose target is an array. **/
		protected var _targets:Array;
		
		/** @private Ease to use which determines the rate of change during the animation. Examples are <code>ElasticOut.ease</code>, <code>StrongIn.ease</code>, etc. (all in the <code>com.greensock.easing package</code>) **/
		public var _ease:Ease;
		
		/** @private To speed the handling of the ease, we store the type here (1 = easeOut, 2 = easeIn, 3 = easeInOut, and 0 = none of these) **/
		protected var _easeType:int;
		
		/** @private To speed handling of the ease, we store its strength here (Linear is 0, Quad is 1, Cubic is 2, Quart is 3, Quint (and Strong) is 4, etc.) **/
		protected var _easePower:int;
		
		/** @private The array that stores the tweens of the same target (or targets) for the purpose of speeding overwrites. **/
		protected var _siblings:Array;
		
		/** @private Overwrite mode (0 = none, 1 = all, 2 = auto, 3 = concurrent, 4 = allOnStart, 5 = preexisting) **/
		protected var _overwrite:int;
		
		/** @private When properties are overwritten in this tween, the properties get added to this object because sometimes properties are overwritten <strong>BEFORE</strong> the tween inits. **/
		protected var _overwrittenProps:Object;
		
		/** @private If this tween has any TweenPlugins that need to be notified of a change in the "enabled" status, this will be true. (speeds things up in the _enable() setter) **/
		protected var _notifyPluginsOfEnabled:Boolean;
		
		/** @private Only used in tweens where a startAt is defined (like fromTo() tweens) so that we can record the pre-tween starting values and revert to them properly if/when the playhead on the timeline moves backwards, before this tween started. In other words, if alpha is at 1 and then someone does a fromTo() tween that makes it go from 0 to 1 and then the playhead moves BEFORE that tween, alpha should jump back to 1 instead of reverting to 0. **/
		protected var _startAt:TweenLite;
		
		
		/**
		 * Constructor
		 *  
		 * @param target Target object (or array of objects) whose properties this tween affects 
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is set in the <code>vars</code> parameter)
		 * @param vars An object defining the end value for each property that should be tweened as well as any special properties like <code>onComplete</code>, <code>ease</code>, etc. For example, to tween <code>mc.x</code> to 100 and <code>mc.y</code> to 200 and then call <code>myFunction</code>, do this: <code>new TweenLite(mc, 1, {x:100, y:200, onComplete:myFunction})</code>.
		 */
		public function TweenLite(target:Object, duration:Number, vars:Object) {
			super(duration, vars);
			
			if (target == null) {
				throw new Error("Cannot tween a null object. Duration: "+duration+", data: "+this.data);
			}
			
			if (!_overwriteLookup) {
				_overwriteLookup = {none:0, all:1, auto:2, concurrent:3, allOnStart:4, preexisting:5, "true":1, "false":0};
				ticker.addEventListener("enterFrame", _dumpGarbage, false, -1, true);
			}
			
			ratio = 0;
			this.target = target;
			_ease = defaultEase; //temporary - we'll replace it in _init(). We need to set it here for speed purposes so that on the first render(), it doesn't throw an error. 
			
			_overwrite = (!("overwrite" in this.vars)) ? _overwriteLookup[defaultOverwrite] : (typeof(this.vars.overwrite) === "number") ? this.vars.overwrite >> 0 : _overwriteLookup[this.vars.overwrite];
			
			if (this.target is Array && typeof(this.target[0]) === "object") {
				_targets = this.target.concat();
				_propLookup = [];
				_siblings = [];
				var i:int = _targets.length;
				while (--i > -1) {
					_siblings[i] = _register(_targets[i], this, false);
					if (_overwrite == 1) if (_siblings[i].length > 1) {
						_applyOverwrite(_targets[i], this, null, 1, _siblings[i]);
					}
				}
				
			} else {
				_propLookup = {};
				_siblings = _tweenLookup[target]
				if (_siblings == null) { //the next few lines accomplish the same thing as _siblings = _register(target, this, false) but faster and only slightly more verbose.
					_siblings = _tweenLookup[target] = [this];
				} else {
					_siblings[_siblings.length] = this;
					if (_overwrite == 1) {
						_applyOverwrite(target, this, null, 1, _siblings);
					}
				}
			}
			
			if (this.vars.immediateRender || (duration == 0 && _delay == 0 && this.vars.immediateRender != false)) {
				render(-_delay, false, true);
			}
		}
		
		/**
		 * @private
		 * Initializes the tween
		 */
		protected function _init():void {
			var immediate:Boolean = vars.immediateRender,
				i:int, initPlugins:Boolean, pt:PropTween, p:String, copy:Object;
			if (vars.startAt) {
				if (_startAt != null) {
					_startAt.render(-1, true); //if we've run a startAt previously (when the tween instantiated), we should revert it so that the values re-instantiate correctly particularly for relative tweens. Without this, a TweenLite.fromTo(obj, 1, {x:"+=100"}, {x:"-=100"}), for example, would actually jump to +=200 because the startAt would run twice, doubling the relative change.
				}
				vars.startAt.overwrite = 0;
				vars.startAt.immediateRender = true;
				_startAt = new TweenLite(target, 0, vars.startAt);
				if (immediate) {
					if (_time > 0) {
						_startAt = null; //tweens that render immediately (like most from() and fromTo() tweens) shouldn't revert when their parent timeline's playhead goes backward past the startTime because the initial render could have happened anytime and it shouldn't be directly correlated to this tween's startTime. Imagine setting up a complex animation where the beginning states of various objects are rendered immediately but the tween doesn't happen for quite some time - if we revert to the starting values as soon as the playhead goes backward past the tween's startTime, it will throw things off visually. Reversion should only happen in TimelineLite/Max instances where immediateRender was false (which is the default in the convenience methods like from()).
					} else if (_duration !== 0) {
						return; //we skip initialization here so that overwriting doesn't occur until the tween actually begins. Otherwise, if you create several immediateRender:true tweens of the same target/properties to drop into a TimelineLite or TimelineMax, the last one created would overwrite the first ones because they didn't get placed into the timeline yet before the first render occurs and kicks in overwriting.
					}
				}
			} else if (vars.runBackwards && _duration !== 0) {
				//from() tweens must be handled uniquely: their beginning values must be rendered but we don't want overwriting to occur yet (when time is still 0). Wait until the tween actually begins before doing all the routines like overwriting. At that time, we should render at the END of the tween to ensure that things initialize correctly (remember, from() tweens go backwards)
				if (_startAt != null) {
					_startAt.render(-1, true);
					_startAt = null;
				} else {
					copy = {};
					for (p in vars) { //copy props into a new object and skip any reserved props, otherwise onComplete or onUpdate or onStart could fire. We should, however, permit autoCSS to go through.
						if (!(p in _reservedProps)) {
							copy[p] = vars[p];
						}
					}
					copy.overwrite = 0;
					copy.data = "isFromStart"; //we tag the tween with as "isFromStart" so that if [inside a plugin] we need to only do something at the very END of a tween, we have a way of identifying this tween as merely the one that's setting the beginning values for a "from()" tween. For example, clearProps in HTML5's CSSPlugin should only get applied at the very END of a tween and without this tag, from(...{height:100, clearProps:"height", delay:1}) would wipe the height at the beginning of the tween and after 1 second, it'd kick back in.
					_startAt = TweenLite.to(target, 0, copy);
					if (!immediate) {
						_startAt.render(-1, true); //for tweens that aren't rendered immediately, we still need to use the _startAt to record the starting values so that we can revert to them if the parent timeline's playhead goes backward beyond the beginning, but we immediately revert the tween back otherwise the parent tween that's currently instantiating wouldn't see the wrong starting values (since they were changed by the _startAt tween) 
					} else if (_time === 0) {
						return;
					}
				}
			}
			
			if (vars.ease is Ease) {
				_ease = (vars.easeParams is Array) ? vars.ease.config.apply(vars.ease, vars.easeParams) : vars.ease;
			} else if (typeof(vars.ease) === "function") {
				_ease = new Ease(vars.ease, vars.easeParams);
			} else {
				_ease = defaultEase;
			}
			_easeType = _ease._type;
			_easePower = _ease._power;
			_firstPT = null;
			
			if (_targets) {
				i = _targets.length;
				while (--i > -1) {
					if ( _initProps( _targets[i], (_propLookup[i] = {}), _siblings[i], (_overwrittenProps ? _overwrittenProps[i] : null)) ) {
						initPlugins = true;
					}
				}
			} else {
				initPlugins = _initProps(target, _propLookup, _siblings, _overwrittenProps);
			}
			
			if (initPlugins) {
				_onPluginEvent("_onInitAllProps", this); //reorders the array in order of priority. Uses a static TweenPlugin method in order to minimize file size in TweenLite
			}
			if (_overwrittenProps) if (_firstPT == null) if (typeof(target) !== "function") { //if all tweening properties have been overwritten, kill the tween. If the target is a function, it's most likely a delayedCall so let it live.
				_enabled(false, false);
			}
			if (vars.runBackwards) {
				pt = _firstPT;
				while (pt) {
					pt.s += pt.c;
					pt.c = -pt.c;
					pt = pt._next;
				}
			}
			_onUpdate = vars.onUpdate;
			_initted = true;
		}
		
		/** @private Loops through the <code>vars</code> properties, captures starting values, triggers overwriting if necessary, etc. **/
		protected function _initProps(target:Object, propLookup:Object, siblings:Array, overwrittenProps:Object):Boolean {
			var vars:Object = this.vars,
				p:String, i:int, initPlugins:Boolean, plugin:Object, val:Object;
			if (target == null) {
				return false;
			}
			for (p in vars) {
				val = vars[p];
				if (p in _reservedProps) {
					if (val is Array) if (val.join("").indexOf("{self}") !== -1) {
						vars[p] = _swapSelfInParams(val as Array);
					}
					
				} else if ((p in _plugins) && (plugin = new _plugins[p]())._onInitTween(target, val, this)) {
					_firstPT = new PropTween(plugin, "setRatio", 0, 1, p, true, _firstPT, plugin._priority);
					i = plugin._overwriteProps.length;
					while (--i > -1) {
						propLookup[plugin._overwriteProps[i]] = _firstPT;
					}
					if (plugin._priority || ("_onInitAllProps" in plugin)) {
						initPlugins = true;
					}
					if (("_onDisable" in plugin) || ("_onEnable" in plugin)) {
						_notifyPluginsOfEnabled = true;
					}
					
				} else {
					_firstPT = propLookup[p] = new PropTween(target, p, 0, 1, p, false, _firstPT);
					_firstPT.s = (!_firstPT.f) ? Number(target[p]) : target[ ((p.indexOf("set") || !("get" + p.substr(3) in target)) ? p : "get" + p.substr(3)) ]();
					_firstPT.c = (typeof(val) === "number") ? Number(val) - _firstPT.s : (typeof(val) === "string" && val.charAt(1) === "=") ? int(val.charAt(0)+"1") * Number(val.substr(2)) : Number(val) || 0;					
				}
			}
			
			if (overwrittenProps) if (_kill(overwrittenProps, target)) { //another tween may have tried to overwrite properties of this tween before init() was called (like if two tweens start at the same time, the one created second will run first)
				return _initProps(target, propLookup, siblings, overwrittenProps);
			}
			if (_overwrite > 1) if (_firstPT != null) if (siblings.length > 1) if (_applyOverwrite(target, this, propLookup, _overwrite, siblings)) {
				_kill(propLookup, target);
				return _initProps(target, propLookup, siblings, overwrittenProps);
			}
			return initPlugins;
		}
		
		
		
		/** @private (see Animation.render() for notes) **/
		override public function render(time:Number, suppressEvents:Boolean=false, force:Boolean=false):void {
			var isComplete:Boolean, callback:String, pt:PropTween, rawPrevTime:Number, prevTime:Number = _time;
			if (time >= _duration) {
				_totalTime = _time = _duration;
				ratio = _ease._calcEnd ? _ease.getRatio(1) : 1;
				if (!_reversed) {
					isComplete = true;
					callback = "onComplete";
				}
				if (_duration == 0) { //zero-duration tweens are tricky because we must discern the momentum/direction of time in order to determine whether the starting values should be rendered or the ending values. If the "playhead" of its timeline goes past the zero-duration tween in the forward direction or lands directly on it, the end values should be rendered, but if the timeline's "playhead" moves past it in the backward direction (from a postitive time to a negative time), the starting values must be rendered.
					rawPrevTime = _rawPrevTime;
					if (_startTime === _timeline._duration) { //if a zero-duration tween is at the VERY end of a timeline and that timeline renders at its end, it will typically add a tiny bit of cushion to the render time to prevent rounding errors from getting in the way of tweens rendering their VERY end. If we then reverse() that timeline, the zero-duration tween will trigger its onReverseComplete even though technically the playhead didn't pass over it again. It's a very specific edge case we must accommodate.
						time = 0;
					}
					if (time === 0 || rawPrevTime < 0 || rawPrevTime === _tinyNum) if (rawPrevTime !== time) {
						force = true;
						if (rawPrevTime > 0 && rawPrevTime !== _tinyNum) {
							callback = "onReverseComplete";
						}
					}
					_rawPrevTime = rawPrevTime = (!suppressEvents || time !== 0 || _rawPrevTime === time) ? time : _tinyNum; //when the playhead arrives at EXACTLY time 0 (right on top) of a zero-duration tween, we need to discern if events are suppressed so that when the playhead moves again (next time), it'll trigger the callback. If events are NOT suppressed, obviously the callback would be triggered in this render. Basically, the callback should fire either when the playhead ARRIVES or LEAVES this exact spot, not both. Imagine doing a timeline.seek(0) and there's a callback that sits at 0. Since events are suppressed on that seek() by default, nothing will fire, but when the playhead moves off of that position, the callback should fire. This behavior is what people intuitively expect. We set the _rawPrevTime to be a precise tiny number to indicate this scenario rather than using another property/variable which would increase memory usage. This technique is less readable, but more efficient.
				}
				
			} else if (time < 0.0000001) { //to work around occasional floating point math artifacts, round super small values to 0. 
				_totalTime = _time = 0;
				ratio = _ease._calcEnd ? _ease.getRatio(0) : 0;
				if (prevTime !== 0 || (_duration === 0 && _rawPrevTime > 0 && _rawPrevTime !== _tinyNum)) {
					callback = "onReverseComplete";
					isComplete = _reversed;
				}
				if (time < 0) {
					_active = false;
					if (_duration == 0) { //zero-duration tweens are tricky because we must discern the momentum/direction of time in order to determine whether the starting values should be rendered or the ending values. If the "playhead" of its timeline goes past the zero-duration tween in the forward direction or lands directly on it, the end values should be rendered, but if the timeline's "playhead" moves past it in the backward direction (from a postitive time to a negative time), the starting values must be rendered.
						if (_rawPrevTime >= 0) {
							force = true;
						}
						_rawPrevTime = rawPrevTime = (!suppressEvents || time !== 0 || _rawPrevTime === time) ? time : _tinyNum; //when the playhead arrives at EXACTLY time 0 (right on top) of a zero-duration tween, we need to discern if events are suppressed so that when the playhead moves again (next time), it'll trigger the callback. If events are NOT suppressed, obviously the callback would be triggered in this render. Basically, the callback should fire either when the playhead ARRIVES or LEAVES this exact spot, not both. Imagine doing a timeline.seek(0) and there's a callback that sits at 0. Since events are suppressed on that seek() by default, nothing will fire, but when the playhead moves off of that position, the callback should fire. This behavior is what people intuitively expect. We set the _rawPrevTime to be a precise tiny number to indicate this scenario rather than using another property/variable which would increase memory usage. This technique is less readable, but more efficient.
					}
				} else if (!_initted) { //if we render the very beginning (time == 0) of a fromTo(), we must force the render (normal tweens wouldn't need to render at a time of 0 when the prevTime was also 0). This is also mandatory to make sure overwriting kicks in immediately.
					force = true;
				}
				
			} else {
				_totalTime = _time = time;
				if (_easeType) {
					var r:Number = time / _duration;
					if (_easeType == 1 || (_easeType == 3 && r >= 0.5)) {
						r = 1 - r;
					}
					if (_easeType == 3) {
						r *= 2;
					}
					if (_easePower == 1) {
						r *= r;
					} else if (_easePower == 2) {
						r *= r * r;
					} else if (_easePower == 3) {
						r *= r * r * r;
					} else if (_easePower == 4) {
						r *= r * r * r * r;
					}
					if (_easeType == 1) {
						ratio = 1 - r;
					} else if (_easeType == 2) {
						ratio = r;
					} else if (time / _duration < 0.5) {
						ratio = r / 2;
					} else {
						ratio = 1 - (r / 2);
					}
					
				} else {
					ratio = _ease.getRatio(time / _duration);
				}
				
			}
			
			if (_time == prevTime && !force) {
				return;
			} else if (!_initted) {
				_init();
				if (!_initted || _gc) { //immediateRender tweens typically won't initialize until the playhead advances (_time is greater than 0) in order to ensure that overwriting occurs properly. Also, if all of the tweening properties have been overwritten (which would cause _gc to be true, as set in _init()), we shouldn't continue otherwise an onStart callback could be called for example. 
					return;
				}
				//_ease is initially set to defaultEase, so now that init() has run, _ease is set properly and we need to recalculate the ratio. Overall this is faster than using conditional logic earlier in the method to avoid having to set ratio twice because we only init() once but renderTime() gets called VERY frequently.
				if (_time && !isComplete) {
					ratio = _ease.getRatio(_time / _duration);
				} else if (isComplete && _ease._calcEnd) {
					ratio = _ease.getRatio((_time === 0) ? 0 : 1);
				}
			}
			
			if (!_active) if (!_paused && _time !== prevTime && time >= 0) {
				_active = true;  //so that if the user renders a tween (as opposed to the timeline rendering it), the timeline is forced to re-render and align it with the proper time/frame on the next rendering cycle. Maybe the tween already finished but the user manually re-renders it as halfway done.
			}
			if (prevTime == 0) {
				if (_startAt != null) {
					if (time >= 0) {
						_startAt.render(time, suppressEvents, force);
					} else if (!callback) {
						callback = "_dummyGS"; //if no callback is defined, use a dummy value just so that the condition at the end evaluates as true because _startAt should render AFTER the normal render loop when the time is negative. We could handle this in a more intuitive way, of course, but the render loop is the MOST important thing to optimize, so this technique allows us to avoid adding extra conditional logic in a high-frequency area.
					}
				}
				if (vars.onStart) if (_time != 0 || _duration == 0) if (!suppressEvents) {
					vars.onStart.apply(null, vars.onStartParams);
				}
			}
			
			pt = _firstPT;
			while (pt) {
				if (pt.f) {
					pt.t[pt.p](pt.c * ratio + pt.s);
				} else {
					pt.t[pt.p] = pt.c * ratio + pt.s;
				}
				pt = pt._next;
			}
			
			if (_onUpdate != null) {
				if (time < 0 && _startAt != null && _startTime != 0) { //if the tween is positioned at the VERY beginning (_startTime 0) of its parent timeline, it's illegal for the playhead to go back further, so we should not render the recorded startAt values.
					_startAt.render(time, suppressEvents, force); //note: for performance reasons, we tuck this conditional logic inside less traveled areas (most tweens don't have an onUpdate). We'd just have it at the end before the onComplete, but the values should be updated before any onUpdate is called, so we ALSO put it here and then if it's not called, we do so later near the onComplete.
				}
				if (!suppressEvents) if (_time !== prevTime || isComplete) {
					_onUpdate.apply(null, vars.onUpdateParams);
				}
			}
			
			if (callback) if (!_gc) { //check gc because there's a chance that kill() could be called in an onUpdate
				
				if (time < 0 && _startAt != null && _onUpdate == null && _startTime != 0) { //if the tween is positioned at the VERY beginning (_startTime 0) of its parent timeline, it's illegal for the playhead to go back further, so we should not render the recorded startAt values.
					_startAt.render(time, suppressEvents, force);
				}
				if (isComplete) {
					if (_timeline.autoRemoveChildren) {
						_enabled(false, false);
					}
					_active = false;
				}
				if (!suppressEvents) if (vars[callback]) {
					vars[callback].apply(null, vars[callback + "Params"]);
				}
				if (_duration === 0 && _rawPrevTime === _tinyNum && rawPrevTime !== _tinyNum) { //the onComplete or onReverseComplete could trigger movement of the playhead and for zero-duration tweens (which must discern direction) that land directly back on their start time, we don't want to fire again on the next render. Think of several addPause()'s in a timeline that forces the playhead to a certain spot, but what if it's already paused and another tween is tweening the "time" of the timeline? Each time it moves [forward] past that spot, it would move back, and since suppressEvents is true, it'd reset _rawPrevTime to _tinyNum so that when it begins again, the callback would fire (so ultimately it could bounce back and forth during that tween). Again, this is a very uncommon scenario, but possible nonetheless.
					_rawPrevTime = 0;
				}
			}
			
		}
		
		/** @private Same as <code>kill()</code> except that it returns a Boolean indicating if any significant properties were changed (some plugins like MotionBlurPlugin may perform cleanup tasks that alter alpha, etc.). **/
		override public function _kill(vars:Object=null, target:Object=null):Boolean {
			if (vars === "all") {
				vars = null;
			}
			if (vars == null) if (target == null || target == this.target) {
				return _enabled(false, false);
			}
			target = target || _targets || this.target;
			var i:int, overwrittenProps:Object, p:String, pt:PropTween, propLookup:Object, changed:Boolean, killProps:Object, record:Boolean;
			if (target is Array && typeof(target[0]) === "object") {
				i = target.length;
				while (--i > -1) {
					if (_kill(vars, target[i])) {
						changed = true;
					}
				}
			} else {
				if (_targets) {
					i = _targets.length;
					while (--i > -1) {
						if (target === _targets[i]) {
							propLookup = _propLookup[i] || {};
							_overwrittenProps = _overwrittenProps || [];
							overwrittenProps = _overwrittenProps[i] = vars ? _overwrittenProps[i] || {} : "all";
							break;
						}
					}
				} else if (target !== this.target) {
					return false;
				} else {
					propLookup = _propLookup;
					overwrittenProps = _overwrittenProps = vars ? _overwrittenProps || {} : "all";
				}
				if (propLookup) {
					killProps = vars || propLookup;
					record = (vars != overwrittenProps && overwrittenProps != "all" && vars != propLookup && (typeof(vars) != "object" || vars._tempKill != true)); //_tempKill is a super-secret way to delete a particular tweening property but NOT have it remembered as an official overwritten property (like in BezierPlugin)
					for (p in killProps) {
						pt = propLookup[p]
						if (pt != null) {
							if (pt.pg && pt.t._kill(killProps)) {
								changed = true; //some plugins need to be notified so they can perform cleanup tasks first
							}
							if (!pt.pg || pt.t._overwriteProps.length === 0) {
								if (pt._prev) {
									pt._prev._next = pt._next;
								} else if (pt == _firstPT) {
									_firstPT = pt._next;
								}
								if (pt._next) {
									pt._next._prev = pt._prev;
								}
								pt._next = pt._prev = null;
							}
							delete propLookup[p];
						}
						if (record) { 
							overwrittenProps[p] = 1;
						}
					}
					if (_firstPT == null && _initted) { //if all tweening properties are killed, kill the tween. Without this line, if there's a tween with multiple targets and then you killTweensOf() each target individually, the tween would technically still remain active and fire its onComplete even though there aren't any more properties tweening. 
						_enabled(false, false);
					}
				}
			}
			return changed;
		}
				
		/** @inheritDoc **/
		override public function invalidate():* {
			if (_notifyPluginsOfEnabled) {
				_onPluginEvent("_onDisable", this);
			}
			_firstPT = null;
			_overwrittenProps = null;
			_onUpdate = null;
			_startAt = null;
			_initted = _active = _notifyPluginsOfEnabled = false;
			_propLookup = (_targets) ? {} : [];
			return this;
		}
		
		/** @private (see Animation._enabled() for notes) **/
		override public function _enabled(enabled:Boolean, ignoreTimeline:Boolean=false):Boolean {
			if (enabled && _gc) {
				if (_targets) {
					var i:int = _targets.length;
					while (--i > -1) {
						_siblings[i] = _register(_targets[i], this, true);
					}
				} else {
					_siblings = _register(target, this, true);
				}
			}
			super._enabled(enabled, ignoreTimeline);
			if (_notifyPluginsOfEnabled) if (_firstPT != null) {
				return _onPluginEvent(((enabled) ? "_onEnable" : "_onDisable"), this);
			} 
			return false;
		}
		
		
		
//---- STATIC FUNCTIONS -----------------------------------------------------------------------------------
		
		/**
		 * Static method for creating a TweenLite instance that animates to the specified destination values
		 * (from the current values). The following lines of code all produce identical results: 
		 * 
		 * <listing version="3.0">
TweenLite.to(mc, 1, {x:100});
var myTween = new TweenLite(mc, 1, {x:100});
var myTween = TweenLite.to(mc, 1, {x:100});
</listing>
		 * 
		 * <p>Each line above will tween the <code>"x"</code> property of the <code>mc</code> object 
		 * to a value of 100 over the coarse of 1 second. They each use a slightly different syntax,
		 * all of which are valid. If you don't need to store a reference of the tween, just use the 
		 * static <code>TweenLite.to( )</code> call.</p>
		 * 
		 * <p>Since the <code>target</code> parameter can also be an array of objects, the following 
		 * code will tween the x property of mc1, mc2, and mc3 to a value of 100 simultaneously:</p>
		 * 
		 * <listing version="3.0">
TweenLite.to([mc1, mc2, mc3], 1, {x:100});
</listing>
		 * <p>Even though 3 objects are animating, there is still only one tween created. 
		 * In order to stagger or offset the start times of each object animating, please see 
		 * the <code>staggerTo()</code> method of TimelineLite or TweenMax.</p>
		 * 
		 * <p>For simple sequencing, you can use the <code>delay</code> special property
		 * (like <code>TweenLite.to(mc, 1, {x:100, delay:0.5})</code>), 
		 * but it is highly recommended that you consider using TimelineLite (or TimelineMax) 
		 * for all but the simplest sequencing tasks. It has an identical <code>to()</code> method
		 * that allows you to append tweens one-after-the-other and then control the entire sequence 
		 * as a whole. You can even have the tweens overlap as much as you want.</p>
		 * 
		 * @param target Target object (or array of objects) whose properties this tween affects. 
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is set in the <code>vars</code> parameter)
		 * @param vars An object defining the end value for each property that should be tweened as well as any special properties like <code>onComplete</code>, <code>ease</code>, etc. For example, to tween <code>mc.x</code> to 100 and <code>mc.y</code> to 200 and then call <code>myFunction</code>, do this: <code>TweenLite.to(mc, 1, {x:100, y:200, onComplete:myFunction});</code>
		 * @return TweenLite instance
		 * @see com.greensock.TimelineLite#to()
		 * @see com.greensock.TimelineLite#staggerTo()
		 * @see com.greensock.TweenMax#staggerTo()
		 * @see #from()
		 * @see #fromTo()
		 */
		public static function to(target:Object, duration:Number, vars:Object):TweenLite {
			return new TweenLite(target, duration, vars);
		}
		
		/**
		 * Static method for creating a TweenLite instance that tweens backwards - 
		 * you define the <strong>BEGINNING</strong> values and the current values are used
		 * as the destination values which is great for doing things like animating objects
		 * onto the screen because you can set them up initially the way you want them to look 
		 * at the end of the tween and then animate in from elsewhere.
		 * 
		 * <p><strong>NOTE:</strong> By default, <code>immediateRender</code> is <code>true</code> in 
		 * <code>from()</code> tweens, meaning that they immediately render their starting state 
		 * regardless of any delay that is specified. You can override this behavior by passing 
		 * <code>immediateRender:false</code> in the <code>vars</code> parameter so that it will 
		 * wait to render until the tween actually begins (often the desired behavior when inserting 
		 * into TimelineLite or TimelineMax instances). To illustrate the default behavior, the 
		 * following code will immediately set the <code>alpha</code> of <code>mc</code> 
		 * to 0 and then wait 2 seconds before tweening the <code>alpha</code> back to 1 over
		 * the course of 1.5 seconds:</p>
		 * 
		 * <p><code>
		 * TweenLite.from(mc, 1.5, {alpha:0, delay:2});
		 * </code></p>
		 * 
		 * <p>Since the <code>target</code> parameter can also be an array of objects, the following 
		 * code will tween the alpha property of mc1, mc2, and mc3 from a value of 0 simultaneously:</p>
		 * 
		 * <listing version="3.0">
TweenLite.from([mc1, mc2, mc3], 1.5, {alpha:0});
</listing>
		 * <p>Even though 3 objects are animating, there is still only one tween created. 
		 * In order to stagger or offset the start times of each object animating, please see 
		 * the <code>staggerFrom()</code> method of TimelineLite or TweenMax.</p>
		 * 
		 * <p>For simple sequencing, you can use the <code>delay</code> special property
		 * (like <code>TweenLite.from(mc, 1, {alpha:0, delay:0.5})</code>), 
		 * but it is highly recommended that you consider using TimelineLite (or TimelineMax) 
		 * for all but the simplest sequencing tasks. It has an identical <code>from()</code> method
		 * that allows you to append tweens one-after-the-other and then control the entire sequence 
		 * as a whole. You can even have the tweens overlap as much as you want.</p>
		 * 
		 * @param target Target object (or array of objects) whose properties this tween affects.  
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is set in the <code>vars</code> parameter)
		 * @param vars An object defining the starting value for each property that should be tweened as well as any special properties like <code>onComplete</code>, <code>ease</code>, etc. For example, to tween <code>mc.x</code> from 100 and <code>mc.y</code> from 200 and then call <code>myFunction</code>, do this: <code>TweenLite.from(mc, 1, {x:100, y:200, onComplete:myFunction});</code>
		 * @return TweenLite instance
		 * @see #to()
		 * @see #fromTo()
		 * @see com.greensock.TimelineLite#from()
		 * @see com.greensock.TimelineLite#staggerFrom()
		 * @see com.greensock.TweenMax#staggerFrom()
		 */
		public static function from(target:Object, duration:Number, vars:Object):TweenLite {
			vars = _prepVars(vars, true);
			vars.runBackwards = true;
			return new TweenLite(target, duration, vars);
		}
		
		/**
		 * Static method for creating a TweenLite instance that allows you to define both the starting
		 * and ending values (as opposed to <code>to()</code> and <code>from()</code> tweens which are 
		 * based on the target's current values at one end or the other).
		 * 
		 * <p><strong>NOTE</strong>: Only put starting values in the <code>fromVars</code> parameter - all 
		 * special properties for the tween (like onComplete, onUpdate, delay, etc.) belong in the <code>toVars</code> 
		 * parameter. </p>
		 * 
		 * <p>By default, <code>immediateRender</code> is <code>true</code> in 
		 * <code>fromTo()</code> tweens, meaning that they immediately render their starting state 
		 * regardless of any delay that is specified. This is done for convenience because it is 
		 * often the preferred behavior when setting things up on the screen to animate into place, but 
		 * you can override this behavior by passing <code>immediateRender:false</code> in the 
		 * <code>fromVars</code> or <code>toVars</code> parameter so that it will wait to render 
		 * the starting values until the tween actually begins (often the desired behavior when inserting 
		 * into TimelineLite or TimelineMax instances).</p>
		 * 
		 * <p>Since the <code>target</code> parameter can also be an array of objects, the following 
		 * code will tween the x property of mc1, mc2, and mc3 from 0 to 100 simultaneously:</p>
		 * 
		 * <listing version="3.0">
TweenLite.fromTo([mc1, mc2, mc3], 1, {x:0}, {x:100});
</listing>
		 * <p>Even though 3 objects are animating, there is still only one tween created. 
		 * In order to stagger or offset the start times of each object animating, please see 
		 * the <code>staggerFromTo()</code> method of TimelineLite or TweenMax.</p>
		 * 
		 * <p>For simple sequencing, you can use the <code>delay</code> special property
		 * (like <code>TweenLite.fromTo(mc, 1, {x:0}, {x:100, delay:0.5})</code>), 
		 * but it is highly recommended that you consider using TimelineLite (or TimelineMax) 
		 * for all but the simplest sequencing tasks. It has an identical <code>fromTo()</code> method
		 * that allows you to append tweens one-after-the-other and then control the entire sequence 
		 * as a whole. You can even have the tweens overlap as much as you want.</p>
		 * 
		 * @param target Target object (or array of objects) whose properties this tween affects. 
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is set in the <code>vars</code> parameter)
		 * @param fromVars An object defining the starting value for each property that should be tweened. For example, to tween <code>mc.x</code> from 100 and <code>mc.y</code> from 200, <code>fromVars</code> would look like this: <code>{x:100, y:200}</code>.
		 * @param toVars An object defining the end value for each property that should be tweened as well as any special properties like <code>onComplete</code>, <code>ease</code>, etc. For example, to tween <code>mc.x</code> from 0 to 100 and <code>mc.y</code> from 0 to 200 and then call <code>myFunction</code>, do this: <code>TweenLite.fromTo(mc, 1, {x:0, y:0}, {x:100, y:200, onComplete:myFunction});</code>
		 * @return TweenLite instance
		 * @see #to()
		 * @see #from()
		 * @see com.greensock.TimelineLite#fromTo()
		 * @see com.greensock.TimelineLite#staggerFromTo()
		 * @see com.greensock.TweenMax#staggerFromTo()
		 */
		public static function fromTo(target:Object, duration:Number, fromVars:Object, toVars:Object):TweenLite {
			toVars = _prepVars(toVars, true);
			fromVars = _prepVars(fromVars);
			toVars.startAt = fromVars;
			toVars.immediateRender = (toVars.immediateRender != false && fromVars.immediateRender != false);
			return new TweenLite(target, duration, toVars);
		}
		
		/** @private Accommodates TweenLiteVars instances for strong data typing and code hinting **/
		protected static function _prepVars(vars:Object, immediateRender:Boolean=false):Object {
			if (vars._isGSVars) {
				vars = vars.vars;
			}
			if (immediateRender && !("immediateRender" in vars)) {
				vars.immediateRender = true;
			}
			return vars;
		}
		
		/**
		 * Provides a simple way to call a function after a set amount of time (or frames). You can
		 * optionally pass any number of parameters to the function too.
		 * 
		 * <p><strong>JavaScript and AS2 note:</strong> - Due to the way JavaScript and AS2 don't 
		 * maintain scope (what "<code>this</code>" refers to, or the context) in function calls, 
		 * it can be useful to define the scope specifically. Therefore, in the JavaScript and AS2 
		 * versions the 4th parameter is <code>scope</code>, bumping <code>useFrames</code> 
		 * back to the 5th parameter:</p>
		 * 
		 * <p><code>TweenLite.delayedCall(delay, callback, params, scope, useFrames)</code> <em>[JavaScript and AS2 only]</em></p>
		 * 
		 * <listing version="3.0">
//calls myFunction after 1 second and passes 2 parameters:
TweenLite.delayedCall(1, myFunction, ["param1", 2]);
 
function myFunction(param1, param2) {
    //do stuff
}
</listing>
		 * 
		 * @param delay Delay in seconds (or frames if <code>useFrames</code> is <code>true</code>) before the function should be called
		 * @param callback Function to call
		 * @param params An Array of parameters to pass the function (optional).
		 * @param useFrames If the delay should be measured in frames instead of seconds, set <code>useFrames</code> to <code>true</code> (default is <code>false</code>)
		 * @return TweenLite instance
		 * @see com.greensock.TimelineLite#call()
		 * @see com.greensock.TimelineMax#addCallback()
		 */
		public static function delayedCall(delay:Number, callback:Function, params:Array=null, useFrames:Boolean=false):TweenLite {
			return new TweenLite(callback, 0, {delay:delay, onComplete:callback, onCompleteParams:params, onReverseComplete:callback, onReverseCompleteParams:params, immediateRender:false, useFrames:useFrames, overwrite:0});
		}
		
		/**
		 * Immediately sets properties of the target accordingly - essentially a zero-duration to() tween with a more 
		 * intuitive name. So the following lines produce identical results:
		 * 
		 * <listing version="3.0">
TweenLite.set(myObject, {x:100, y:50, alpha:0});
TweenLite.to(myObject, 0, {x:100, y:50, alpha:0});
</listing>
		 * 
		 * <p>And of course you can use an array to set the properties of multiple targets at the same time, like:</p>
		 * 
		 * <listing version="3.0">
TweenLite.set([obj1, obj2, obj3], {x:100, y:50, alpha:0});
</listing>
		 * 
		 * @param target Target object (or array of objects) whose properties will be affected. 
		 * @param vars An object defining the value for each property that should be set. For example, to set <code>mc.x</code> to 100 and <code>mc.y</code> to 200, do this: <code>TweenLite.set(mc, {x:100, y:200});</code>
		 * @return A TweenLite instance (with a duration of 0) which can optionally be inserted into a TimelineLite/Max instance (although it's typically more concise to just use the timeline's <code>set()</code> method).
		 */
		public static function set(target:Object, vars:Object):TweenLite {
			return new TweenLite(target, 0, vars);
		}
		
		/** @private **/
		private static function _dumpGarbage(event:Event):void {
			if ((_rootFrame / 60) >> 0 === _rootFrame / 60) { //faster than !(_rootFrame % 60)
				var i:int, a:Array, tgt:Object;
				for (tgt in _tweenLookup) {
					a = _tweenLookup[tgt];
					i = a.length;
					while (--i > -1) {
						if (a[i]._gc) {
							a.splice(i, 1);
						}
					}
					if (a.length === 0) {
						delete _tweenLookup[tgt];
					}
				}
			}
		}
		
		
		
		/**
		 * Kills all the tweens (or specific tweening properties) of a particular object or delayedCalls 
		 * to a particular function. If, for example, you want to kill all tweens of <code>myObject</code>, 
		 * you'd do this:
		 * 
		 * <p><code>
		 * TweenLite.killTweensOf(myObject);
		 * </code></p>
		 * 
		 * <p>To kill only active (currently animating) tweens of <code>myObject</code>, you'd do this:</p>
		 * 
		 * <p><code>
		 * TweenLite.killTweensOf(myObject, true);
		 * </code></p>
		 * 
		 * <p>To kill only particular tweening properties of the object, use the third parameter. 
		 * For example, if you only want to kill all the tweens of <code>myObject.alpha</code> and 
		 * <code>myObject.x</code>, you'd do this:</p>
		 * 
		 * <p><code>
		 * TweenLite.killTweensOf(myObject, false, {alpha:true, x:true});
		 * </code></p>
		 * 
		 * <p>To kill all the delayedCalls that were created like <code>TweenLite.delayedCall(5, myFunction);</code>, 
		 * you can simply call <code>TweenLite.killTweensOf(myFunction);</code> because delayedCalls 
		 * are simply tweens that have their <code>target</code> and <code>onComplete</code> set to 
		 * the same function (as well as a <code>delay</code> of course).</p>
		 * 
		 * <p><code>killTweensOf()</code> affects tweens that haven't begun yet too. If, for example, 
		 * a tween of <code>myObject</code> has a <code>delay</code> of 5 seconds and 
		 * <code>TweenLite.killTweensOf(mc)</code> is called 2 seconds after the tween was created, 
		 * it will still be killed even though it hasn't started yet. </p>
		 * 
		 * @param target Object whose tweens should be killed immediately or selector text to feed the selector engine to find the target(s).
		 * @param onlyActive If <code>true</code>, only tweens that are currently active will be killed (a tween is considered "active" if the virtual playhead is actively moving across the tween and it is not paused, nor are any of its ancestor timelines paused). 
		 * @param vars To kill only specific properties, use a generic object containing enumerable properties corresponding to the ones that should be killed like <code>{x:true, y:true}</code>. The values assigned to each property of the object don't matter - the sole purpose of the object is for iteration over the named properties (in this case, <code>x</code> and <code>y</code>). If no object (or <code>null</code>) is defined, all matched tweens will be killed in their entirety.
		 **/
		public static function killTweensOf(target:*, onlyActive:*=false, vars:Object=null):void {
			if (typeof(onlyActive) === "object") {
				vars = onlyActive; //for backwards compatibility (before "onlyActive" parameter was inserted)
				onlyActive = false;
			}
			var a:Array = TweenLite.getTweensOf(target, onlyActive),
				i:int = a.length;
			while (--i > -1) {
				a[i]._kill(vars, target);
			}
		}
		
		/**
		 * Immediately kills all of the delayedCalls to a particular function. If, for example, 
		 * you want to kill all delayedCalls to <code>myFunction</code>, you'd do this:
		 * 
		 * <p><code>
		 * TweenLite.killDelayedCallsTo(myFunction);
		 * </code></p>
		 * 
		 * <p>Since a delayedCall is just a tween that uses the function/callback as both its <code>target</code>
		 * and its <code>onComplete</code>, <code>TweenLite.killTweensOf(myFunction)</code> produces exactly the 
		 * same result as <code>TweenLite.killDelayedCallsTo(myFunction)</code>.</p>
		 * 
		 * <p>This method affects all delayedCalls that were created using <code>TweenLite.delayedCall()</code>
		 * or <code>TweenMax.delayedCall()</code> or the <code>call()</code> or <code>addCallback()</code> methods
		 * of TimelineLite or TimelineMax. Basically, any tween whose target is the function you supply will 
		 * be killed.</p>
		 * 
		 * @param func The function for which all delayedCalls should be killed/cancelled.
		 **/
		public static function killDelayedCallsTo(func:Function):void {
			killTweensOf(func);
		}
		
		/**
		 * Returns an array containing all the tweens of a particular target (or group of targets) that have not
		 * been released for garbage collection yet which typically happens within a few seconds after the tween completes.
		 * For example, <code>TweenLite.getTweensOf(myObject)</code> returns an array of all tweens
		 * of <code>myObject</code>, even tweens that haven't begun yet. <code>TweenLite.getTweensOf([myObject1, myObject2]);</code>
		 * will return a condensed array of the tweens of <code>myObject1</code> plus all the tweens
		 * of <code>myObject2</code> combined into one array with duplicates removed. 
		 * 
		 * <p>Since the method only finds tweens that haven't been released for garbage collection, if you create a tween
		 * and then let it finish and then a while later try to find it with <code>getTweensOf()</code>, it may not be found 
		 * because it was released by the engine for garbage collection. Remember, one of the best parts of GSAP is that it 
		 * saves you from the headache of managing gc. Otherwise, you'd need to manually dispose each tween you create, making 
		 * things much more cumbersome.</p>
		 * 
		 * <listing version="3.0">
TweenLite.to(myObject1, 1, {x:100});
TweenLite.to(myObject2, 1, {x:100});
TweenLite.to([myObject1, myObject2], 1, {alpha:0});

var a1 = TweenLite.getTweensOf(myObject1); //finds 2 tweens
var a2 = TweenLite.getTweensOf([myObject1, myObject2]); //finds 3 tweens
</listing>
		 * @param target The target whose tweens should be returned, or an array of such targets
		 * @param onlyActive If <code>true</code>, only tweens that are currently active will be returned (a tween is considered "active" if the virtual playhead is actively moving across the tween and it is not paused, nor are any of its ancestor timelines paused). 
		 * @return An array of tweens
		 **/
		public static function getTweensOf(target:*, onlyActive:Boolean=false):Array {
			var i:int, a:Array, j:int, t:TweenLite;
			if (target is Array && typeof(target[0]) != "string" && typeof(target[0]) != "number") {
				i = target.length;
				a = [];
				while (--i > -1) {
					a = a.concat(getTweensOf(target[i], onlyActive));
				}
				i = a.length;
				//now get rid of any duplicates (tweens of arrays of objects could cause duplicates)
				while (--i > -1) {
					t = a[i];
					j = i;
					while (--j > -1) {
						if (t === a[j]) {
							a.splice(i, 1);
						}
					}
				}
			} else {
				a = _register(target).concat();
				i = a.length;
				while (--i > -1) {
					if (a[i]._gc || (onlyActive && !a[i].isActive())) {
						a.splice(i, 1);
					}
				}
			}
			return a;
		}
		
		/** 
		 * @private
		 * Used for one or more of the following purposes:
		 * 1) Register a target, putting it into the lookup/Dictionary for easy lookup later
		 * 2) Returns an array of sibling tweens (tweens of the same target)
		 * 3) scrubs the siblings array of duplicate instances of the tween (typically only used when re-enabling a tween instance).
		 **/
		protected static function _register(target:Object, tween:TweenLite=null, scrub:Boolean=false):Array {
			var a:Array = _tweenLookup[target], 
				i:int;
			if (a == null) {
				a = _tweenLookup[target] = [];
			}
			if (tween) {
				i = a.length;
				a[i] = tween;
				if (scrub) {
					while (--i > -1) {
						if (a[i] === tween) {
							a.splice(i, 1);
						}
					}
				}
			}
			return a;
		}
		
		/** @private Performs overwriting **/
		protected static function _applyOverwrite(target:Object, tween:TweenLite, props:Object, mode:int, siblings:Array):Boolean {
			var i:int, changed:Boolean, curTween:TweenLite;
			if (mode == 1 || mode >= 4) {
				var l:int = siblings.length;
				for (i = 0; i < l; i++) {
					curTween = siblings[i];
					if (curTween != tween) {
						if (!curTween._gc) if (curTween._enabled(false, false)) {
							changed = true;
						}
					} else if (mode == 5) {
						break;
					}
				}
				return changed;
			}
			//NOTE: Add 0.0000000001 to overcome floating point errors that can cause the startTime to be VERY slightly off (when a tween's time() is set for example)
			var startTime:Number = tween._startTime + 0.0000000001, overlaps:Array = [], oCount:int = 0, zeroDur:Boolean = (tween._duration == 0), globalStart:Number;
			i = siblings.length;
			while (--i > -1) {
				curTween = siblings[i];
				if (curTween === tween || curTween._gc || curTween._paused) {
					//ignore
				} else if (curTween._timeline != tween._timeline) {
					globalStart = globalStart || _checkOverlap(tween, 0, zeroDur);
					if (_checkOverlap(curTween, globalStart, zeroDur) === 0) {
						overlaps[oCount++] = curTween;
					}
				} else if (curTween._startTime <= startTime) if (curTween._startTime + curTween.totalDuration() / curTween._timeScale > startTime) if (!((zeroDur || !curTween._initted) && startTime - curTween._startTime <= 0.0000000002)) {
					overlaps[oCount++] = curTween;
				}
			}
			
			i = oCount;
			while (--i > -1) {
				curTween = overlaps[i];
				if (mode == 2) if (curTween._kill(props, target)) {
					changed = true;
				}
				if (mode !== 2 || (!curTween._firstPT && curTween._initted)) { 
					if (curTween._enabled(false, false)) { //if all property tweens have been overwritten, kill the tween.
						changed = true;
					}
				}
			}
			return changed;
		}
		
		/** 
		 * @private 
		 * Checks if a tween overlaps with a particular global time value. "reference" is the point in time on the global (root) timeline, 
		 * and if the tween overlaps with it, 0 is returned. If the tween starts AFTER the reference, the difference between the two (positive 
		 * value) is returned. If reference is AFTER the end of the tween, the negative offset is given (reference time minus where the end of 
		 * the tween is on the global timeline). If the tween lands EXACTLY on the reference, it will check to see if the tween's _initted property 
		 * is true. If not, 0.0000000001 is returned, indicating that the tween shouldn't be overwritten. If any of the child's anscestor timelines
		 * are paused, -100 is returned. This wraps a lot of functionality into a relatively concise method (keeps file size low and performance high)
		 **/
		private static function _checkOverlap(tween:Animation, reference:Number, zeroDur:Boolean):Number {
			var tl:SimpleTimeline = tween._timeline, 
				ts:Number = tl._timeScale, 
				t:Number = tween._startTime,
				min:Number = 0.0000000001;
			while (tl._timeline) {
				t += tl._startTime;
				ts *= tl._timeScale;
				if (tl._paused) {
					return -100;
				}
				tl = tl._timeline;
			}
			t /= ts;
			return (t > reference) ? t - reference : ((zeroDur && t == reference) || (!tween._initted && t - reference < 2 * min)) ? min : ((t += tween.totalDuration() / tween._timeScale / ts) > reference + min) ? 0 : t - reference - min;
		}
		
		
	}
	
}

