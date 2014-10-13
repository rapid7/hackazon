/**
 * VERSION: 12.1.5
 * DATE: 2014-07-19
 * AS3 (AS2 version is also available)
 * UPDATES AND DOCS AT: http://www.greensock.com 
 **/
package com.greensock {
	import com.greensock.TweenLite;
	import com.greensock.core.Animation;
	import com.greensock.core.PropTween;
	import com.greensock.core.SimpleTimeline;
	import com.greensock.events.TweenEvent;
	import com.greensock.plugins.*;
	
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.Shape;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	import flash.utils.getTimer;
/**
 * TweenMax extends TweenLite, adding many useful (but non-essential) features like repeat(), 
 * repeatDelay(), yoyo(), AS3 event dispatching, updateTo(), pauseAll(), and more. It also activates many extra plugins 
 * by default, making it extremely full-featured. Any of the plugins can work with TweenLite too, but TweenMax saves
 * you the step of activating the common ones. Since TweenMax extends TweenLite, it can do <strong>ANYTHING</strong> 
 * TweenLite can do plus more. The syntax is identical. You can mix and match TweenLite and TweenMax in your 
 * project as you please, but if file size is a concern it is best to stick with TweenLite unless you need 
 * a particular TweenMax-only feature. 
 * 
 * <p>Like TweenLite, a TweenMax instance handles tweening one or more properties of <strong>any object</strong> 
 * (or array of objects) over time. TweenMax can be used on its own or in conjuction with advanced sequencing 
 * tools like TimelineLite or TimelineMax to make complex tasks much simpler. With scores of other animation 
 * frameworks to choose from, why consider the GreenSock Animation Platform?:</p>
 * 
 * 	<ul>
 * 		<li><strong> SPEED </strong>- The platform has been highly optimized for maximum performance. 
 * 			See some speed comparisons yourself at 
 * 			<a href="http://www.greensock.com/tweening-speed-test/">http://www.greensock.com/tweening-speed-test/</a></li>
 * 		  
 * 		<li><strong> Freakishly robust feature set </strong>- In addition to tweening any numeric property 
 * 			of any object, plugins can be activated to tween hex colors, beziers, arrays, filters, plus 
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
 * <p>The most common type of tween is a <a href="TweenMax.html#to()">to()</a> tween which allows you 
 * to define the destination values:</p>
 * 
 * <p><code>
 * TweenMax.to(myObject, 2, {x:100, y:200});
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
 * TweenMax.to([obj1, obj2, obj3], 1, {alpha:0.5, y:100});
 * </code></p>
 * 
 * <p>You can also use a <a href="TweenMax.html#from()">from()</a> tween if you want to define the 
 * <strong>starting</strong> values instead of the ending values so that the target tweens <em>from</em> 
 * the defined values to wherever they currently are. Or a <a href="TweenMax.html#fromTo()">fromTo()</a> 
 * lets you define both starting and ending values.</p>
 * 
 * <p>Although the <code>to()</code>, <code>from()</code>, and <code>fromTo()</code> static methods
 * are popular because they're quick and can avoid some garbage collection hassles, you can also
 * use the more object-oriented syntax like this:</p>
 * 
 * <p><code>
 * var tween = new TweenMax(myObject, 2, {x:100, y:200});
 * </code></p>
 * 
 * <p>or even:</p>
 * 
 * <p><code>
 * var tween = TweenMax.to(myObject, 2, {x:100, y:200});
 * </code></p>
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
 * 				 or <code>StrongInOut.ease</code>. For best performance, use one of the GreenSock eases
 * 				 (which are in the <code>com.greensock.easing</code> package). TweenMax also works with 
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
 * 				 <code>TweenMax.to(mc, 1, {x:100, onComplete:myFunction, onCompleteParams:[mc, "param2"]});</code>
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
 * 				 method that allows them to be configured to change their behavior (like <code>TweenMax.to(mc, 1, {x:100, ease:ElasticOut.ease.config(0.5, 1)})</code>
 * 				 but if you are using a non-GreenSock ease that accepts extra parameters like Adobe's
 * 				 <code>fl.motion.easing.Elastic</code>, <code>easeParams</code> allows you to define 
 * 				 those extra parameters as an array like <code>TweenMax.to(mc, 1, {x:100, ease:Elastic.easeOut, easeParams:[0.5, 1]})</code>. 
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
 * 				 <code>TweenMax.to(mc, 1, {x:100, delay:1, onStart:myFunction, onStartParams:[mc, "param2"]});</code>
 * 				 To self-reference the tween instance itself in one of the parameters, use <code>"{self}"</code>,
 * 				 like: <code>onStartParams:["{self}", "param2"]</code></li>
 * 	
 * 	<li><strong> onUpdate </strong>:<em> Function</em> -
 * 				 A function that should be called every time the tween updates  
 * 				 (on every frame while the tween is active)</li>
 * 	
 * 	<li><strong> onUpdateParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the <code>onUpdate</code> function. For example,
 * 				 <code>TweenMax.to(mc, 1, {x:100, onUpdate:myFunction, onUpdateParams:[mc, "param2"]});</code>
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
 * 				 <code>TweenMax.to(mc, 1, {x:100, onReverseComplete:myFunction, onReverseCompleteParams:[mc, "param2"]});</code>
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
 * 	
 * 	<li><strong> repeat </strong>:<em> Number</em> -
 * 				 Number of times that the tween should repeat after its first iteration. For example, 
 * 				 if <code>repeat</code> is 1, the tween will play a total of twice (the initial play
 * 				 plus 1 repeat). To repeat indefinitely, use -1. <code>repeat</code> should always be an integer.</li>
 * 	
 * 	<li><strong> repeatDelay </strong>:<em> Number</em> -
 * 				 Amount of time in seconds (or frames for frames-based tweens) between repeats. For example,
 * 				 if <code>repeat</code> is 2 and <code>repeatDelay</code> is 1, the tween will play initially,
 * 				 then wait for 1 second before it repeats, then play again, then wait 1 second again before 
 * 				 doing its final repeat.</li>
 * 	
 * 	<li><strong> yoyo </strong>:<em> Boolean</em> -
 * 				 If <code>true</code>, every other <code>repeat</code> cycle will run in the opposite
 * 				 direction so that the tween appears to go back and forth (forward then backward).
 * 				 This has no affect on the "<code>reversed</code>" property though. So if <code>repeat</code> 
 * 				 is 2 and <code>yoyo</code> is <code>false</code>, it will look like: 
 * 				 start - 1 - 2 - 3 - 1 - 2 - 3 - 1 - 2 - 3 - end. But if <code>yoyo</code> is <code>true</code>, 
 * 				 it will look like: start - 1 - 2 - 3 - 3 - 2 - 1 - 1 - 2 - 3 - end.</li>
 *  
 * 	<li><strong> onRepeat </strong>:<em> Function</em> -
 * 				 A function that should be called each time the tween repeats</li>
 * 	
 * 	<li><strong> onRepeatParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the onRepeat function. For example, 
 * 				 <code>TweenMax.to(mc, 1, {x:100, onRepeat:myFunction, onRepeatParams:[mc, "param2"]});</code>
 * 				 To self-reference the tween instance itself in one of the parameters, use <code>"{self}"</code>,
 * 				 like: <code>onRepeatParams:["{self}", "param2"]</code></li>
 * 									
 * 	<li><strong> onStartListener </strong>:<em> Function</em> [AS3 only] -
 * 				 A function that should be called (and passed an event parameter) when the tween begins 
 * 				 (when its <code>totalTime</code> changes from 0 to some other value which can happen more 
 * 				 than once if the tween is restarted multiple times). Identical to <code>onStart</code> except
 * 				 that the function will always be passed an event parameter whose <code>target</code> property points
 * 				 to the tween. It's the same as doing <code>myTween.addEventListener("start", myFunction);</code>. 
 * 				 Unless you need the event parameter, it's better/faster to use <code>onStart</code>.</li>
 * 	
 * 	<li><strong> onUpdateListener </strong>:<em> Function</em> [AS3 only] -
 * 				 A function that should be called (and passed an event parameter) each time the tween updates 
 * 				 (on every frame while the tween is active). Identical to <code>onUpdate</code> except
 * 				 that the function will always be passed an event parameter whose <code>target</code> property points
 * 				 to the tween. It's the same as doing <code>myTween.addEventListener("update", myFunction);</code>. 
 * 				 Unless you need the event parameter, it's better/faster to use <code>onUpdate</code>.</li>
 * 	  
 * 	<li><strong> onCompleteListener </strong>:<em> Function</em> [AS3 only] - 
 * 				 A function that should be called (and passed an event parameter) each time the tween completes. 
 * 				 Identical to <code>onComplete</code> except that the function will always be passed an event 
 * 				 parameter whose <code>target</code> property points to the tween. It's the same as doing 
 * 				 <code>myTween.addEventListener("complete", myFunction);</code>. 
 * 				 Unless you need the event parameter, it's better/faster to use <code>onComplete</code>.</li>
 * 
 *  <li><strong> onReverseCompleteListener </strong>:<em> Function</em> [AS3 only] -
 * 				 A function that should be called (and passed an event parameter) each time the tween has reached 
 * 				 its beginning again from the reverse direction. For example, if <code>reverse()</code> is called 
 * 				 the tween will move back towards its beginning and when its <code>totalTime</code> reaches 0, 
 * 				 <code>onReverseCompleteListener</code> will be called. This can also happen if the tween is placed 
 * 				 in a TimelineLite or TimelineMax instance that gets reversed and plays the tween backwards to 
 * 				 (or past) the beginning. Identical to <code>onReverseComplete</code> except that the function 
 * 				 will always be passed an event parameter whose <code>target</code> property points to the tween. 
 * 				 It's the same as doing <code>myTween.addEventListener("reverseComplete", myFunction);</code>. 
 * 				 Unless you need the event parameter, it's better/faster to use <code>onReverseComplete</code>.</li>
 * 
 *  <li><strong> onRepeatListener </strong>:<em> Function</em> [AS3 only] -
 * 				 A function that should be called (and passed an event parameter) each time the tween repeats. 
 * 				 Identical to <code>onRepeat</code> except that the function will always be passed an event 
 * 				 parameter whose <code>target</code> property points to the tween. It's the same as doing 
 * 				 <code>myTween.addEventListener("repeat", myFunction);</code>. 
 * 				 Unless you need the event parameter, it's better/faster to use <code>onRepeat</code>.</li>
 * 	
 * 	<li><strong> startAt </strong>:<em> Object</em> -
 * 				 Allows you to define the starting values for tweening properties. Typically, TweenMax uses 
 * 				 the current value (whatever it happens to be at the time the tween begins) as the starting 
 * 				 value, but <code>startAt</code> allows you to override that behavior. Simply pass an object 
 * 				 in with whatever properties you'd like to set just before the tween begins. For example, 
 * 				 if <code>mc.x</code> is currently 100, and you'd like to tween it from 0 to 500, do 
 * 				 <code>TweenMax.to(mc, 2, {x:500, startAt:{x:0}});</code></li>
 * </ul>
 * 
 * <p><strong>AS3 note:</strong> In AS3, using a <code><a href="data/TweenMaxVars.html">TweenMaxVars</a></code> 
 * instance instead of a generic object to define your <code>vars</code> is a bit more verbose but provides 
 * code hinting and improved debugging because it enforces strict data typing. Use whichever one you prefer.</p>
 * 
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
 * <p>In the JavaScript version of TweenMax, activating a plugin is as simple as loading the associated .js file. 
 * No extra activation code is necessary. And by default, the JavaScript version of TweenMax includes the CSSPlugin
 * and RoundPropsPlugin so you don't need to load those separately. In the ActionScript version, activating a plugin 
 * requires a single line of code and you only need to do it once, so it's pretty easy. Simply pass an Array containing 
 * the names of all the plugins you'd like to activate to the <code>TweenPlugin.activate()</code> method, like this:</p>
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
 * <p>The following plugins are automatically activated by TweenMax:</p>
 * 	
 * <ul>
 * 	<li><strong> autoAlpha </strong>:<em> Number</em> - 
 * 				 <code>autoAlpha</code> is identical to tweening <code>alpha</code> except that it also 
 * 				 automatically hides the target when the value hits zero, and shows the target when the
 * 				 value isn't zero. In AS3, this means it toggles the target's <code>visible</code> property.
 * 				 In AS2, the <code>_visible</code> property is toggled, and in JS the <code>visibility</code> 
 * 				 style property is set to <code>"hidden"</code> to hide. This can help improve rendering performance.</li>
 * 						   
 *  <li><strong> visible </strong>:<em> Boolean</em> [AS3/AS2 only] - 
 * 				 hides or shows the target when the tween completes. In AS3, this means it toggles the target's 
 * 				 <code>visible</code> property. In AS2, the <code>_visible</code> property is toggled, and in 
 * 				 JS the <code>display</code> style is set to <code>"none"</code> to hide.</li>
 * 	  
 * 	<li><strong> volume </strong>:<em> Number</em> [AS3/AS2 only] - 
 * 				 Tweens the volume of an object. In AS3, it can handle anything with a <code>soundTransform</code> 
 * 				 property (MovieClip/SoundChannel/NetStream, etc.). In AS2, it is for MovieClips or Sound objects.</li>
 * 	  
 * 	<li><strong> tint </strong>:<em> Number</em> [AS3/AS2 only] - 
 * 				 Tweens the color (tint) of the target. Use a hex value, for example: 0xFF0000 for red or 0x0000FF 
 * 				 for blue, etc. To remove the tint, use <code>null</code>.</li>
 *  	  
 * 	<li><strong> frame </strong>:<em> Number</em> [AS3/AS2 only] - 
 * 				 Tweens a MovieClip to a particular frame. To tween to a label, use the FrameLabelPlugin.</li>
 * 	
 * 	<li><strong> bezier </strong>:<em> Array</em> - 
 * 				 Bezier tweening allows you to tween in a non-linear way. For example, you may want to tween
 * 				 the target's position from the origin (0,0) 500 pixels to the right (500,0) but curve downwards
 *  			 through the middle of the tween. Simply pass as many objects in the bezier array as you'd like, 
 * 				 one for each "control point". See the BezierPlugin documentation for more details. In this example, 
 * 				 let's say the control point would be at x/y coordinates 250,50. Just make sure your mc is at 
 * 				 coordinates 0,0 and then do: <code>TweenMax.to(my_mc, 3, {bezier:[{x:250, y:50}, {x:500, y:0}]});</code></li>
 * 					   
 * 	<li><strong> bezierThrough </strong>:<em> Array</em> - 
 * 				 Identical to <code>bezier</code> except that instead of passing bezier control point values, you
 * 				 pass values through which the bezier values should move. This can be more intuitive than using 
 * 				 control points.</li>
 * 							  
 * 	<li><strong> orientToBezier </strong>:<em> Boolean (or Array)</em> - 
 * 				 When doing a <code>bezier</code> or <code>bezierThrough</code> tween, you can use
 * 				 <code>orientToBezier</code> to cause the target to alter its rotation in the direction
 * 				 of the bezier, making it appear to turn with the curves. The simplest way is to set
 * 				 <code>orientToBezier</code> to <code>true</code>, but you can accomplish advanced effects
 * 				 like using a different property than "rotation" or adding a certain number of degrees to the
 * 				 standard rotational value, etc. by using an array instead. The array should contain the
 * 				 following 4 elements (in this order): 
 * 					<ol>
 * 						<li> Position property 1 (typically "x")</li>
 * 						<li> Position property 2 (typically "y")</li>
 * 						<li> Rotational property (typically "rotation")</li>
 * 						<li> Number of degrees to add (optional - makes it easy to orient your target properly)</li>
 *					</ol>
 * 				 For maximum flexibility, you can pass in any number of arrays inside the container array, one 
 * 				 for each rotational property. This can be convenient when working in 3D because you can rotate
 * 				 on multiple axis. If you're doing a standard 2D x/y tween on a bezier, you can simply pass 
 * 				 in a boolean value of true and TweenMax will use a typical setup, <code>[["x", "y", "rotation", 0]]</code>. 
 * 				 Hint: Don't forget the container Array (notice the double outer brackets)</li>
 * 							
 * 	<li><strong> hexColors </strong>:<em> Object</em> - 
 * 				 Although hex colors are technically numbers, if you try to tween them conventionally,
 * 				 you'll notice that they don't tween smoothly. To tween them properly, the red, green, and 
 * 				 blue components must be extracted and tweened independently. TweenMax makes it easy. To tween
 * 				 a property of your object that's a hex color, just pass an Object with properties named the 
 * 				 same as your object's hex color properties that should be tweened. For example, if your mc 
 * 				 object has a "myHexProp" property that you'd like to tween to red (0xFF0000) over the course 
 * 				 of 2 seconds, do: <code>TweenMax.to(mc, 2, {hexColors:{myHexProp:0xFF0000}});</code>
 * 				 You can pass in any number of hexColor properties.</li>
 * 				 
 * 	<li><strong> shortRotation </strong>:<em> Object</em> - 
 * 				 For rotational tweens, it can be useful to have the engine figure out the shortest direction
 * 				 to the destination value and go that way. For example, if the target's rotation property is
 * 				 at 0 and you need to rotate to 270, it would actually be shorter to go from 0 to -90. 
 * 				 If <code>rotation</code> is currently 170 degrees and you want to tween it to -170 degrees, 
 * 				 a normal rotation tween would travel a total of 340 degrees in the counter-clockwise direction, 
 * 				 but if you use shortRotation, it would travel 20 degrees in the clockwise direction instead.
 * 				 In order to accommodate any rotational property (including 3D ones like rotationX, rotationY, 
 * 				 and rotationZ or even a custom one), <code>shortRotation</code> should be an object whose properties
 * 				 correspond to the ones you want tweened. For example, to tween <code>mc.rotation</code> to 270 in ths shortest
 * 				 direction, do: <code>TweenMax.to(mc, 1, {shortRotation:{rotation:270}});</code> or to tween
 * 				 its <code>rotationX</code> to -80 and <code>rotationY</code> to 30 in the shortest direction, do: 
 * 				 <code>TweenMax.to(mc, 1, {shortRotation:{rotationX:-80, rotationY:30}});</code></li>
 * 	  					   
 * 	<li><strong> roundProps </strong>:<em> String</em> - 
 * 				 A comma-delimited list of property names whose value should be rounded to the nearest integer
 * 				 anytime they are updated during the tween. For example, if you're tweening the 
 * 				 x, y, and alpha properties of mc and you want to round the x and y values (not alpha)
 * 	  			 every time the tween is rendered, do: 
 * 				 <code>TweenMax.to(mc, 2, {x:300, y:200, alpha:0.5, roundProps:"x,y"});</code></li>
 * 	  					   
 * 	<li><strong> blurFilter </strong>:<em> Object</em> [AS3/AS2 only] - 
 * 				 Creates a BlurFilter tween affecting any of the following properties:
 * 	  			 <code>blurX, blurY, quality, remove, addFilter, index</code>. For example, 
 * 				 to blur the object 20 pixels on each axis, do:
 * 				 <code>TweenMax.to(mc, 1, {blurFilter:{blurX:20, blurY:20}});</code>
 * 				 To remove the filter as soon as the tween completes, set <code>remove:true</code>
 * 				 inside the <code>blurFilter</code> object.</li>
 * 	  						
 * 	<li><strong> glowFilter </strong>:<em> Object</em> [AS3/AS2 only] - 
 * 				 Creates a GlowFilter tween affecting any of the following properties:
 * 	  			 <code>alpha, blurX, blurY, color, strength, quality, inner, knockout, remove, addFilter, index</code>.
 * 				 For example, to create a 20 pixel red glow with a strength of 1.5 and alpha of 1, do:
 * 				 <code>TweenMax.to(mc, 1, {glowFilter:{blurX:20, blurY:20, color:0xFF0000, strength:1.5, alpha:1}});</code>
 * 				 To remove the filter as soon as the tween completes, set <code>remove:true</code>
 * 				 inside the <code>glowFilter</code> object.</li>
 * 	  						
 * 	<li><strong> colorMatrixFilter </strong>:<em> Object</em> [AS3/AS2 only] - 
 * 				 Creates a ColorMatrixFilter tween affecting any of the following properties:
 * 				 <code>colorize, amount, contrast, brightness, saturation, hue, threshold, relative, matrix, remove, addFilter, index</code>
 * 				 For example, to completely desaturate the target, do:
 * 				 <code>TweenMax.to(mc, 1, {colorMatrixFilter:{saturation:0}});</code>
 * 				 Or to colorize the object red at 50% strength, do:
 * 				 <code>TweenMax.to(mc, 1, {colorMatrixFilter:{colorize:0xFF0000, amount:0.5}});</code>
 * 				 To remove the filter as soon as the tween completes, set <code>remove:true</code>
 * 				 inside the <code>colorMatrixFilter</code> object.</li>
 * 								   
 * 	<li><strong> dropShadowFilter </strong>:<em> Object</em> [AS3/AS2 only] - 
 * 				 Creates a DropShadowFilter tween affecting any of the following properties:
 * 				 <code>alpha, angle, blurX, blurY, color, distance, strength, quality, remove, addFilter, index</code>
 * 				 For example, to create a 10 pixel red drop shadow with an alpha of 0.8 and an angle of 45, do:
 * 				 <code>TweenMax.to(mc, 1, {dropShadowFilter:{blurX:10, blurY:10, color:0xFF0000, angle:45, alpha:0.8}});</code>
 * 				 To remove the filter as soon as the tween completes, set <code>remove:true</code>
 * 				 inside the <code>dropShadowFilter</code> object.</li>
 * 								  
 * 	<li><strong> bevelFilter </strong>:<em> Object</em> [AS3/AS2 only] - 
 * 				 Creates a BevelFilter tween affecting any of the following properties:
 * 				 <code>angle, blurX, blurY, distance, highlightAlpha, highlightColor, shadowAlpha, shadowColor, strength, quality, remove, addFilter, index</code>
 * 				 For example, to create a 10 pixel bevel with a strength of 1.5 and distance of 10 and shadowAlpha of 0.8, do:
 * 				 <code>TweenMax.to(mc, 1, {bevelFilter:{blurX:10, blurY:10, strength:1.5, distance:10, shadowAlpha:0.8}});</code>
 * 				 To remove the filter as soon as the tween completes, set <code>remove:true</code>
 * 				 inside the <code>bevelFilter</code> object.</li>
 * 	</ul>
 * 	
 * 	
 * <p><strong>EXAMPLES:</strong></p> 
 * <p>Please see <a href="http://www.greensock.com">http://www.greensock.com</a> for 
 * examples, tutorials, and interactive demos.</p>
 * 
 * <strong>NOTES / TIPS:</strong>
 * <ul>
 * 	<li> Passing values as Strings and a preceding "+=" or "-=" will make the tween relative to the 
 * 		current value. For example, if you do <code>TweenMax.to(mc, 2, {x:"-=20"});</code> it'll 
 * 		tween <code>mc.x</code> to the left 20 pixels. <code>{x:"+=20"}</code> would move it to the right.</li>
 * 	  
 * 	<li> You can use <code>addEventListener()</code> to add listeners to the tween instance manually 
 * 		instead of using the onCompleteListener, onStartListener, and onUpdateListener special properties. 
 * 	 	Like <code>myTween.addEventListener("complete", myFunction);</code></li>
 * 	  
 * 	<li> You can change the default ease by setting the <code>TweenLite.defaultEase</code> static property. 
 * 		The default is <code>Power1.easeOut</code>.</li>
 * 	  
 * 	<li> You can kill all tweens of a particular object anytime with <code>TweenMax.killTweensOf(myObject); </code></li>
 * 	  
 * 	<li> You can kill all delayedCalls to a particular function with <code>TweenMax.killDelayedCallsTo(myFunction)</code>
 * 		 or <code>TweenMax.killTweensOf(myFunction);</code></li>
 * 	  
 * 	<li> Use the <code>TweenMax.from()</code> method to animate things into place. For example, 
 * 		if you have things set up on the stage in the spot where they should end up, and you 
 * 		just want to animate them into place, you can pass in the beginning x and/or y and/or 
 * 		alpha (or whatever properties you want).</li>
 * 	  
 * 	<li> If you find this class useful, please consider joining <a href="http://www.greensock.com/club/">Club GreenSock</a>
 * 		which not only helps to sustain ongoing development, but also gets you bonus plugins, classes 
 * 		and other benefits that are ONLY available to members. Learn more at 
 * 		<a href="http://www.greensock.com/club/">http://www.greensock.com/club/</a></li>
 * 	</ul>
 * 	  
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class TweenMax extends TweenLite implements IEventDispatcher {
		/** @private **/
		public static const version:String = "12.1.5";
		
		TweenPlugin.activate([
			
			//ACTIVATE (OR DEACTIVATE) PLUGINS HERE...
			
			AutoAlphaPlugin,			//tweens alpha and then toggles "visible" to false if/when alpha is zero
			EndArrayPlugin,				//tweens numbers in an Array
			FramePlugin,				//tweens MovieClip frames
			RemoveTintPlugin,			//allows you to remove a tint
			TintPlugin,					//tweens tints
			VisiblePlugin,				//tweens a target's "visible" property
			VolumePlugin,				//tweens the volume of a MovieClip or SoundChannel or anything with a "soundTransform" property
			BevelFilterPlugin,			//tweens BevelFilters
			BezierPlugin,				//enables bezier tweening
			BezierThroughPlugin,		//enables bezierThrough tweening
			BlurFilterPlugin,			//tweens BlurFilters
			ColorMatrixFilterPlugin,	//tweens ColorMatrixFilters (including hue, saturation, colorize, contrast, brightness, and threshold)
			ColorTransformPlugin,		//tweens advanced color properties like exposure, brightness, tintAmount, redOffset, redMultiplier, etc.
			DropShadowFilterPlugin,		//tweens DropShadowFilters
			FrameLabelPlugin,			//tweens a MovieClip to particular label
			GlowFilterPlugin,			//tweens GlowFilters
			HexColorsPlugin,			//tweens hex colors
			RoundPropsPlugin,			//enables the roundProps special property for rounding values
			ShortRotationPlugin			//tweens rotation values in the shortest direction
			
			]);
		
		/** @private **/
		protected static var _listenerLookup:Object = {onCompleteListener:TweenEvent.COMPLETE, onUpdateListener:TweenEvent.UPDATE, onStartListener:TweenEvent.START, onRepeatListener:TweenEvent.REPEAT, onReverseCompleteListener:TweenEvent.REVERSE_COMPLETE};
		
		/**
		 * The object that dispatches a <code>"tick"</code> event each time the engine updates, making it easy for 
		 * you to add your own listener(s) to run custom logic after each update (great for game developers).
		 * Add as many listeners as you want. The basic syntax is the same for all versions (AS2, AS3, and JavaScript):
		 * 
		 * <p><strong>Basic example (AS2, AS3, and JavaScript):</strong></p><listing version="3.0">
 //add listener
 TweenMax.ticker.addEventListener("tick", myFunction);
 
 function myFunction(event) {
	 //executes on every tick after the core engine updates
 }
		 
 //to remove the listener later...
 TweenMax.ticker.removeEventListener("tick", myFunction);
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
 TweenMax.ticker.addEventListener("tick", myFunction, this, true, 1);
 
 function myFunction(event) {
 	//executes on every tick after the core engine updates
 }
 
 //to remove the listener later...
 TweenMax.ticker.removeEventListener("tick", myFunction);
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
 TweenMax.ticker.addEventListener("tick", myFunction, false, 0, true);
 
 function myFunction(event:Event):void {
 	//executes on every tick after the core engine updates
 }
 
 //to remove the listener later...
 TweenMax.ticker.removeEventListener("tick", myFunction);
		 </listing>
		 **/
		public static var ticker:Shape = Animation.ticker;
		
		
		/**
		 * Kills all the tweens (or specific tweening properties) of a particular object or 
		 * the delayedCalls to a particular function. If, for example, you want to kill all 
		 * tweens of <code>myObject</code>, you'd do this:
		 * 
		 * <p><code>
		 * TweenMax.killTweensOf(myObject);
		 * </code></p>
		 * 
		 * <p>To kill only active (currently animating) tweens of <code>myObject</code>, you'd do this:</p>
		 * 
		 * <p><code>
		 * TweenLite.killTweensOf(myObject, true);
		 * </code></p>
		 * 
		 * <p>To kill only particular tweening properties of the object, use the second parameter. 
		 * For example, if you only want to kill all the tweens of <code>myObject.alpha</code> and 
		 * <code>myObject.x</code>, you'd do this:</p>
		 * 
		 * <p><code>
		 * TweenMax.killTweensOf(myObject, false, {alpha:true, x:true});
		 * </code></p>
		 * 
		 * <p>To kill all the delayedCalls (like ones created using <code>TweenMax.delayedCall(5, myFunction);</code>), 
		 * you can simply call <code>TweenMax.killTweensOf(myFunction);</code> because delayedCalls 
		 * are simply tweens that have their <code>target</code> and <code>onComplete</code> set to 
		 * the same function (as well as a <code>delay</code> of course).</p>
		 * 
		 * <p><code>killTweensOf()</code> affects tweens that haven't begun yet too. If, for example, 
		 * a tween of <code>myObject</code> has a <code>delay</code> of 5 seconds and 
		 * <code>TweenLite.killTweensOf(mc)</code> is called 2 seconds after the tween was created, 
		 * it will still be killed even though it hasn't started yet. </p>
		 * 
		 * @param target Object whose tweens should be killed immediately
		 * @param onlyActive If <code>true</code>, only tweens that are currently active will be killed (a tween is considered "active" if the virtual playhead is actively moving across the tween and it is not paused, nor are any of its ancestor timelines paused). 
		 * @param vars To kill only specific properties, use a generic object containing enumerable properties corresponding to the ones that should be killed like <code>{x:true, y:true}</code>. The values assigned to each property of the object don't matter - the sole purpose of the object is for iteration over the named properties (in this case, <code>x</code> and <code>y</code>). If no object (or <code>null</code>) is defined, all matched tweens will be killed in their entirety.
		 */
		public static function killTweensOf(target:*, onlyActive:*=false, vars:Object=null):void {
			TweenLite.killTweensOf(target, onlyActive, vars);
		}
		
		/**
		 * Immediately kills all of the delayedCalls to a particular function. If, for example, 
		 * you want to kill all delayedCalls to <code>myFunction</code>, you'd do this:
		 * 
		 * <p><code>
		 * TweenMax.killDelayedCallsTo(myFunction);
		 * </code></p>
		 * 
		 * <p>Since a delayedCall is just a tween that uses the function/callback as both its <code>target</code>
		 * and its <code>onComplete</code>, <code>TweenMax.killTweensOf(myFunction)</code> produces exactly the 
		 * same result as <code>TweenMax.killDelayedCallsTo(myFunction)</code>.</p>
		 * 
		 * <p>This method affects all delayedCalls that were created using <code>TweenLite.delayedCall()</code>
		 * or <code>TweenMax.delayedCall()</code> or the <code>call()</code> or <code>addCallback()</code> methods
		 * of TimelineLite or TimelineMax. Basically, any tween whose target is the function you supply will 
		 * be killed.</p>
		 * 
		 * @param func The function for which all delayedCalls should be killed/cancelled.
		 **/
		public static function killDelayedCallsTo(func:Function):void {
			TweenLite.killTweensOf(func);
		}
		
		/**
		 * Returns an array containing all the tweens of a particular target (or group of targets) that have not
		 * been released for garbage collection yet which typically happens within a few seconds after the tween completes.
		 * For example, <code>TweenMax.getTweensOf(myObject)</code> returns an array of all tweens
		 * of <code>myObject</code>, even tweens that haven't begun yet. <code>TweenMax.getTweensOf([myObject1, myObject2]);</code>
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
TweenMax.to(myObject1, 1, {x:100});
TweenMax.to(myObject2, 1, {x:100});
TweenMax.to([myObject1, myObject2], 1, {alpha:0});
		 
var a1 = TweenMax.getTweensOf(myObject1); //finds 2 tweens
var a2 = TweenMax.getTweensOf([myObject1, myObject2]); //finds 3 tweens
</listing>
		 * @param target The target whose tweens should be returned, or an array of such targets
		 * @param onlyActive If <code>true</code>, only tweens that are currently active will be returned (a tween is considered "active" if the virtual playhead is actively moving across the tween and it is not paused, nor are any of its ancestor timelines paused). 
		 * @return An array of tweens
		 **/
		public static function getTweensOf(target:*, onlyActive:Boolean=false):Array {
			return TweenLite.getTweensOf(target, onlyActive);
		}
		
		/** @private **/
		protected var _dispatcher:EventDispatcher;
		/** @private **/
		protected var _hasUpdateListener:Boolean;
		/** @private **/
		protected var _repeat:int = 0;
		/** @private **/
		protected var _repeatDelay:Number = 0;
		/** @private **/
		protected var _cycle:int = 0;
		/** @private **/
		public var _yoyo:Boolean;
		
		/**
		 * Constructor
		 *  
		 * @param target Target object (or array of objects) whose properties this tween affects 
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is set in the <code>vars</code> parameter)
		 * @param vars An object defining the end value for each property that should be tweened as well as any special properties like <code>onComplete</code>, <code>ease</code>, etc. For example, to tween <code>mc.x</code> to 100 and <code>mc.y</code> to 200 and then call <code>myFunction</code>, do this: <code>new TweenMax(mc, 1, {x:100, y:200, onComplete:myFunction})</code>.
		 */
		public function TweenMax(target:Object, duration:Number, vars:Object) {
			super(target, duration, vars);
			_yoyo = (this.vars.yoyo == true);
			_repeat = int(this.vars.repeat);
			_repeatDelay = this.vars.repeatDelay || 0;
			_dirty = true; //ensures that if there is any repeat, the _totalDuration will get recalculated to accurately report it.
			if (this.vars.onCompleteListener || this.vars.onUpdateListener || this.vars.onStartListener || this.vars.onRepeatListener || this.vars.onReverseCompleteListener) {
				_initDispatcher();
				if (_duration == 0) if (_delay == 0) if (this.vars.immediateRender) {
					_dispatcher.dispatchEvent(new TweenEvent(TweenEvent.UPDATE));
					_dispatcher.dispatchEvent(new TweenEvent(TweenEvent.COMPLETE));
				}
			}
		}
	
		/** @inheritDoc **/
		override public function invalidate():* {
			_yoyo = Boolean(this.vars.yoyo == true);
			_repeat = this.vars.repeat || 0;
			_repeatDelay = this.vars.repeatDelay || 0;
			_hasUpdateListener = false;
			_initDispatcher();
			_uncache(true);
			return super.invalidate();
		}
		
		/**
		 * Updates tweening values on the fly so that they appear to seamlessly change course even if 
		 * the tween is in-progress. Think of it like dynamically updating the <code>vars</code> object 
		 * that was passed in to the tween when it was originally created. You do <strong>NOT</strong> 
		 * need to redefine all of the <code>vars</code> properties/values - only the ones that you want
		 * to update. You can even define new properties that you didn't define in the original <code>vars</code> 
		 * object.
		 * 
		 * <p>If the <code>resetDuration</code> parameter is <code>true</code> and the tween has already 
		 * started (or finished), <code>updateTo()</code> will restart the tween. Otherwise, the tween's 
		 * timing will be honored. And if <code>resetDuration</code> is <code>false</code> and the tween 
		 * is in-progress, the starting values of each property will be adjusted so that the tween appears 
		 * to seamlessly redirect to the new destination values. This is typically not advisable if you
		 * plan to reverse the tween later on or jump to a previous point because the starting values would
		 * have been adjusted.</p>
		 * 
		 * <p><code>updateTo()</code> is only meant for non-plugin values. It's much more complicated to 
		 * dynamically update values that are being handled inside plugins - that is not what this method
		 * is intended to do.</p>
		 * 
		 * <p>Note: If you plan to constantly update values, please look into using the <code>DynamicPropsPlugin</code>.</p>
		 * 
		 * <listing version="3.0">
//create the tween
var tween:TweenMax = new TweenMax(mc, 2, {x:100, y:200, alpha:0.5});

//then later, update the destination x and y values, restarting the tween
tween.updateTo({x:300, y:0}, true);

//or to update the values mid-tween without restarting, do this:
tween.updateTo({x:300, y:0}, false);
</listing>
		 * 
		 * @param vars Object containing properties with the destination values that should be udpated. You do <strong>NOT</strong> need to redefine all of the original <code>vars</code> values - only the ones that should be updated (although if you change a plugin value, you will need to fully define it). For example, to update the destination <code>x</code> value to 300 and the destination <code>y</code> value to 500, pass: <code>{x:300, y:500}</code>.
		 * @param resetDuration If the tween has already started (or finished) and <code>resetDuration</code> is <code>true</code>, the tween will restart. If <code>resetDuration</code> is <code>false</code>, the tween's timing will be honored (no restart) and each tweening property's starting value will be adjusted so that it appears to seamlessly redirect to the new destination value.
		 * @return self (makes chaining easier)
		 **/
		public function updateTo(vars:Object, resetDuration:Boolean=false):* {
			var curRatio:Number = ratio;			
			if (resetDuration) if (_startTime < _timeline._time) {
				_startTime = _timeline._time;
				_uncache(false);
				if (_gc) {
					_enabled(true, false);
				} else {
					_timeline.insert(this, _startTime - _delay); //ensures that any necessary re-sequencing of Animations in the timeline occurs to make sure the rendering order is correct.
				}
			}
			for (var p:String in vars) {
				this.vars[p] = vars[p];
			}
			if (_initted) {
				if (resetDuration) {
					_initted = false;
				} else {
					if (_gc) {
						_enabled(true, false);
					}
					if (_notifyPluginsOfEnabled) if (_firstPT != null) {
						_onPluginEvent("_onDisable", this); //in case a plugin like MotionBlur must perform some cleanup tasks
					}
					if (_time / _duration > 0.998) { //if the tween has finished (or come extremely close to finishing), we just need to rewind it to 0 and then render it again at the end which forces it to re-initialize (parsing the new vars). We allow tweens that are close to finishing (but haven't quite finished) to work this way too because otherwise, the values are so small when determining where to project the starting values that binary math issues creep in and can make the tween appear to render incorrectly when run backwards. 
						var prevTime:Number = _time;
						render(0, true, false);
						_initted = false;
						render(prevTime, true, false);
					} else if (_time > 0) {
						_initted = false;
						_init();
						var inv:Number = 1 / (1 - curRatio),
							pt:PropTween = _firstPT, endValue:Number;
						while (pt) {
							endValue = pt.s + pt.c; 
							pt.c *= inv;
							pt.s = endValue - pt.c;
							pt = pt._next;
						}
					}
				}
			}
			return this;
		}
		
		/**
		 * @private
		 * Renders the tween at a particular time (or frame number for frames-based tweens). 
		 * The time is based simply on the overall duration. For example, if a tween's duration
		 * is 3, <code>renderTime(1.5)</code> would render it at the halfway finished point.
		 * 
		 * @param time time (or frame number for frames-based tweens) to render.
		 * @param suppressEvents If true, no events or callbacks will be triggered for this render (like onComplete, onUpdate, onReverseComplete, etc.)
		 * @param force Normally the tween will skip rendering if the time matches the cachedTotalTime (to improve performance), but if force is true, it forces a render. This is primarily used internally for tweens with durations of zero in TimelineLite/Max instances.
		 */
		override public function render(time:Number, suppressEvents:Boolean=false, force:Boolean=false):void {
			if (!_initted) if (_duration === 0 && vars.repeat) { //zero duration tweens that render immediately have render() called from TweenLite's constructor, before TweenMax's constructor has finished setting _repeat, _repeatDelay, and _yoyo which are critical in determining totalDuration() so we need to call invalidate() which is a low-kb way to get those set properly.
				invalidate();
			}
			var totalDur:Number = (!_dirty) ? _totalDuration : totalDuration(), 
				prevTime:Number = _time,
				prevTotalTime:Number = _totalTime, 
				prevCycle:Number = _cycle, 
				isComplete:Boolean, callback:String, pt:PropTween, rawPrevTime:Number;
			if (time >= totalDur) {
				_totalTime = totalDur;
				_cycle = _repeat;
				if (_yoyo && (_cycle & 1) != 0) {
					_time = 0;
					ratio = _ease._calcEnd ? _ease.getRatio(0) : 0;
				} else {
					_time = _duration;
					ratio = _ease._calcEnd ? _ease.getRatio(1) : 1;
				}
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
						if (rawPrevTime > _tinyNum) {
							callback = "onReverseComplete";
						}
					}
					_rawPrevTime = rawPrevTime = (!suppressEvents || time !== 0 || _rawPrevTime === time) ? time : _tinyNum; //when the playhead arrives at EXACTLY time 0 (right on top) of a zero-duration tween, we need to discern if events are suppressed so that when the playhead moves again (next time), it'll trigger the callback. If events are NOT suppressed, obviously the callback would be triggered in this render. Basically, the callback should fire either when the playhead ARRIVES or LEAVES this exact spot, not both. Imagine doing a timeline.seek(0) and there's a callback that sits at 0. Since events are suppressed on that seek() by default, nothing will fire, but when the playhead moves off of that position, the callback should fire. This behavior is what people intuitively expect. We set the _rawPrevTime to be a precise tiny number to indicate this scenario rather than using another property/variable which would increase memory usage. This technique is less readable, but more efficient.
				}
				
			} else if (time < 0.0000001) { //to work around occasional floating point math artifacts, round super small values to 0. 
				_totalTime = _time = _cycle = 0;
				ratio = _ease._calcEnd ? _ease.getRatio(0) : 0;
				if (prevTotalTime !== 0 || (_duration === 0 && _rawPrevTime > 0 && _rawPrevTime !== _tinyNum)) {
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
				if (_repeat != 0) {
					var cycleDuration:Number = _duration + _repeatDelay;
					_cycle = (_totalTime / cycleDuration) >> 0; //originally _totalTime % cycleDuration but floating point errors caused problems, so I normalized it. (4 % 0.8 should be 0 but Flash reports it as 0.79999999!)
					if (_cycle !== 0) if (_cycle === _totalTime / cycleDuration) {
						_cycle--; //otherwise when rendered exactly at the end time, it will act as though it is repeating (at the beginning)
					}
					_time = _totalTime - (_cycle * cycleDuration);
					if (_yoyo) if ((_cycle & 1) != 0) {
						_time = _duration - _time;
					}
					if (_time > _duration) {
						_time = _duration;
					} else if (_time < 0) {
						_time = 0;
					}
				}
				if (_easeType) {
					var r:Number = _time / _duration, type:int = _easeType, pow:int = _easePower;
					if (type == 1 || (type == 3 && r >= 0.5)) {
						r = 1 - r;
					}
					if (type == 3) {
						r *= 2;
					}
					if (pow == 1) {
						r *= r;
					} else if (pow == 2) {
						r *= r * r;
					} else if (pow == 3) {
						r *= r * r * r;
					} else if (pow == 4) {
						r *= r * r * r * r;
					}
					
					if (type == 1) {
						ratio = 1 - r;
					} else if (type == 2) {
						ratio = r;
					} else if (_time / _duration < 0.5) {
						ratio = r / 2;
					} else {
						ratio = 1 - (r / 2);
					}
					
				} else {
					ratio = _ease.getRatio(_time / _duration);
				}
			}
			
			if (prevTime == _time && !force && _cycle === prevCycle) {
				if (prevTotalTime !== _totalTime) if (_onUpdate != null) if (!suppressEvents) { //so that onUpdate fires even during the repeatDelay - as long as the totalTime changed, we should trigger onUpdate.
					_onUpdate.apply(vars.onUpdateScope || this, vars.onUpdateParams);
				}
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
			if (prevTotalTime == 0) {
				if (_startAt != null) {
					if (time >= 0) {
						_startAt.render(time, suppressEvents, force);
					} else if (!callback) {
						callback = "_dummyGS"; //if no callback is defined, use a dummy value just so that the condition at the end evaluates as true because _startAt should render AFTER the normal render loop when the time is negative. We could handle this in a more intuitive way, of course, but the render loop is the MOST important thing to optimize, so this technique allows us to avoid adding extra conditional logic in a high-frequency area.
					}
				}
				if (_totalTime != 0 || _duration == 0) if (!suppressEvents) {
					if (vars.onStart) {
						vars.onStart.apply(null, vars.onStartParams);
					}
					if (_dispatcher) {
						_dispatcher.dispatchEvent(new TweenEvent(TweenEvent.START));
					}
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
				if (time < 0 && _startAt != null && _startTime != 0) {
					_startAt.render(time, suppressEvents, force); //note: for performance reasons, we tuck this conditional logic inside less traveled areas (most tweens don't have an onUpdate). We'd just have it at the end before the onComplete, but the values should be updated before any onUpdate is called, so we ALSO put it here and then if it's not called, we do so later near the onComplete.
				}
				if (!suppressEvents) if (_totalTime !== prevTotalTime || isComplete) {
					_onUpdate.apply(null, vars.onUpdateParams);
				}
			}
			if (_hasUpdateListener) {
				if (time < 0 && _startAt != null && _onUpdate == null && _startTime != 0) {
					_startAt.render(time, suppressEvents, force);
				}
				if (!suppressEvents) {
					_dispatcher.dispatchEvent(new TweenEvent(TweenEvent.UPDATE));
				}
			}
			if (_cycle != prevCycle) if (!suppressEvents) if (!_gc) {
				if (vars.onRepeat) {
					vars.onRepeat.apply(null, vars.onRepeatParams);
				}
				if (_dispatcher) {
					_dispatcher.dispatchEvent(new TweenEvent(TweenEvent.REPEAT));
				}
			}
			if (callback) if (!_gc) { //check gc because there's a chance that kill() could be called in an onUpdate
				if (time < 0 && _startAt != null && _onUpdate == null && !_hasUpdateListener && _startTime != 0) { //if the tween is positioned at the VERY beginning (_startTime 0) of its parent timeline, it's illegal for the playhead to go back further, so we should not render the recorded startAt values.
					_startAt.render(time, suppressEvents, true);
				}
				if (isComplete) {
					if (_timeline.autoRemoveChildren) {
						_enabled(false, false);
					}
					_active = false;
				}
				if (!suppressEvents) {
					if (vars[callback]) {
						vars[callback].apply(null, vars[callback + "Params"]);
					}
					if (_dispatcher) {
						_dispatcher.dispatchEvent(new TweenEvent(((callback == "onComplete") ? TweenEvent.COMPLETE : TweenEvent.REVERSE_COMPLETE)));
					}
				}
				if (_duration === 0 && _rawPrevTime === _tinyNum && rawPrevTime !== _tinyNum) { //the onComplete or onReverseComplete could trigger movement of the playhead and for zero-duration tweens (which must discern direction) that land directly back on their start time, we don't want to fire again on the next render. Think of several addPause()'s in a timeline that forces the playhead to a certain spot, but what if it's already paused and another tween is tweening the "time" of the timeline? Each time it moves [forward] past that spot, it would move back, and since suppressEvents is true, it'd reset _rawPrevTime to _tinyNum so that when it begins again, the callback would fire (so ultimately it could bounce back and forth during that tween). Again, this is a very uncommon scenario, but possible nonetheless.
					_rawPrevTime = 0;
				}
			}
		}
		
		
//---- EVENT DISPATCHING ----------------------------------------------------------------------------------------------------------
		
		/**
		 * @private
		 * Initializes Event dispatching functionality
		 */
		protected function _initDispatcher():Boolean {
			var found:Boolean = false, p:String;
			for (p in _listenerLookup) {
				if (p in vars) if (vars[p] is Function) {
					if (_dispatcher == null) {
						_dispatcher = new EventDispatcher(this);
					}
					_dispatcher.addEventListener(_listenerLookup[p], vars[p], false, 0, true);
					found = true;
				}
			}
			return found;
		}
		
		/**
		 * [AS3 only]
		 * Registers a function that should be called each time a particular type of event occurs, like 
		 * <code>"complete"</code> or <code>"update"</code>. The function will be passed a single "event" 
		 * parameter whose "<code>target</code>" property refers to the tween. Typically it is more efficient
		 * to use callbacks like <code>onComplete, onUpdate, onStart, onReverseComplete,</code> and <code>onRepeat</code>
		 * unless you need the event parameter or if you need to register more than one listener for the same 
		 * type of event. 
		 * 
		 * <p>If you no longer need an event listener, remove it by calling <code>removeEventListener()</code>, or memory 
		 * problems could result. Event listeners are not automatically removed from memory because the garbage 
		 * collector does not remove the listener as long as the dispatching object exists (unless the 
		 * useWeakReference parameter is set to <code>true</code>).</p>
		 * 
		 * @param type The type of event
		 * @param listener The listener function that processes the event. This function must accept an Event object as its only parameter
		 * @param useCapture (not typically used) Determines whether the listener works in the capture phase or the target and bubbling phases. If useCapture is set to true, the listener processes the event only during the capture phase and not in the target or bubbling phase. If useCapture is false, the listener processes the event only during the target or bubbling phase. To listen for the event in all three phases, call addEventListener twice, once with useCapture set to true, then again with useCapture set to false.
		 * @param priority The priority level of the event listener. The priority is designated by a signed 32-bit integer. The higher the number, the higher the priority. All listeners with priority n are processed before listeners of priority n-1. If two or more listeners share the same priority, they are processed in the order in which they were added. The default priority is 0.
		 * @param useWeakReference Determines whether the reference to the listener is strong or weak. A strong reference (the default) prevents your listener from being garbage-collected. A weak reference does not. 
		 * @see #removeEventListener()
		 **/
		public function addEventListener(type:String, listener:Function, useCapture:Boolean=false, priority:int=0, useWeakReference:Boolean=false):void {
			if (_dispatcher == null) {
				_dispatcher = new EventDispatcher(this);
			}
			if (type == TweenEvent.UPDATE) {
				_hasUpdateListener = true;
			}
			_dispatcher.addEventListener(type, listener, useCapture, priority, useWeakReference);
		}
		
		/** 
		 * [AS3 only]
		 * Removes a listener from the EventDispatcher object. If there is no matching listener registered 
		 * with the EventDispatcher object, a call to this method has no effect.
		 * 
		 * @param type The type of event
		 * @param listener The listener object to remove. 
		 * @param useCapture Specifies whether the listener was registered for the capture phase or the target and bubbling phases. If the listener was registered for both the capture phase and the target and bubbling phases, two calls to removeEventListener() are required to remove both, one call with useCapture() set to true, and another call with useCapture() set to false.
		 **/
		public function removeEventListener(type:String, listener:Function, useCapture:Boolean = false):void {
			if (_dispatcher) {
				_dispatcher.removeEventListener(type, listener, useCapture);
			}
		}
		
		/** @private **/
		public function hasEventListener(type:String):Boolean {
			return (_dispatcher == null) ? false : _dispatcher.hasEventListener(type);
		}
		
		/** @private **/
		public function willTrigger(type:String):Boolean {
			return (_dispatcher == null) ? false : _dispatcher.willTrigger(type);
		}
		
		/** @private **/
		public function dispatchEvent(event:Event):Boolean {
			return (_dispatcher == null) ? false : _dispatcher.dispatchEvent(event);
		}
		
		
//---- STATIC FUNCTIONS -----------------------------------------------------------------------------------------------------------
		
		/**
		 * Static method for creating a TweenMax instance that animates to the specified destination values
		 * (from the current values). This static method can be more intuitive for some developers 
		 * and shields them from potential garbage collection issues that could arise when assigning a
		 * tween instance to a persistent variable. The following lines of code produce identical results: 
		 * 
		 * <listing version="3.0">
TweenMax.to(mc, 1, {x:100});
var myTween = new TweenMax(mc, 1, {x:100});
var myTween = TweenMax.to(mc, 1, {x:100});
</listing>
		 * <p>Each line above will tween the <code>"x"</code> property of the <code>mc</code> object 
		 * to a value of 100 over the coarse of 1 second. They each use a slightly different syntax,
		 * all of which are valid. If you don't need to store a reference of the tween, just use the 
		 * static <code>TweenMax.to( )</code> call.</p>
		 * 
		 * <p>Since the <code>target</code> parameter can also be an array of objects, the following 
		 * code will tween the x property of mc1, mc2, and mc3 to a value of 100 simultaneously:</p>
		 * 
		 * <listing version="3.0">
TweenMax.to([mc1, mc2, mc3], 1, {x:100});
</listing>
		 * <p>Even though 3 objects are animating, there is still only one tween created. 
		 * In order to stagger or offset the start times of each object animating, please see 
		 * the <code>staggerTo()</code> method (TimelineLite has one too).</p>
		 * 
		 * <p>For simple sequencing, you can use the <code>delay</code> special property
		 * (like <code>TweenMax.to(mc, 1, {x:100, delay:0.5})</code>), 
		 * but it is highly recommended that you consider using TimelineLite (or TimelineMax) 
		 * for all but the simplest sequencing tasks. It has an identical <code>to()</code> method
		 * that allows you to append tweens one-after-the-other and then control the entire sequence 
		 * as a whole. You can even have the tweens overlap as much as you want.</p>
		 * 
		 * @param target Target object (or array of objects) whose properties this tween affects. 
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is set in the <code>vars</code> parameter)
		 * @param vars An object defining the end value for each property that should be tweened as well as any special properties like <code>onComplete</code>, <code>ease</code>, etc. For example, to tween <code>mc.x</code> to 100 and <code>mc.y</code> to 200 and then call <code>myFunction</code>, do this: <code>TweenMax.to(mc, 1, {x:100, y:200, onComplete:myFunction});</code>
		 * @return TweenMax instance
		 * @see #from()
		 * @see #fromTo()
		 * @see #staggerTo()
		 * @see com.greensock.TimelineLite#to()
		 * @see com.greensock.TimelineLite#staggerTo()
		 */
		public static function to(target:Object, duration:Number, vars:Object):TweenMax {
			return new TweenMax(target, duration, vars);
		}
		
		/**
		 * Static method for creating a TweenMax instance that tweens backwards - 
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
		 * TweenMax.from(mc, 1.5, {alpha:0, delay:2});
		 * </code></p>
		 * 
		 * <p>Since the <code>target</code> parameter can also be an array of objects, the following 
		 * code will tween the alpha property of mc1, mc2, and mc3 from a value of 0 simultaneously:</p>
		 * 
		 * <listing version="3.0">
TweenMax.from([mc1, mc2, mc3], 1.5, {alpha:0});
</listing>
		 * <p>Even though 3 objects are animating, there is still only one tween that is created. 
		 * In order to stagger or offset the start times of each object animating, please see 
		 * the <code>staggerFrom()</code> method (TimelineLite has one too).</p>
		 * 
		 * <p>For simple sequencing, you can use the <code>delay</code> special property
		 * (like <code>TweenMax.from(mc, 1, {alpha:0, delay:0.5})</code>), 
		 * but it is highly recommended that you consider using TimelineLite (or TimelineMax) 
		 * for all but the simplest sequencing tasks. It has an identical <code>from()</code> method
		 * that allows you to append tweens one-after-the-other and then control the entire sequence 
		 * as a whole. You can even have the tweens overlap as much as you want.</p>
		 * 
		 * @param target Target object (or array of objects) whose properties this tween affects.  
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is set in the <code>vars</code> parameter)
		 * @param vars An object defining the starting value for each property that should be tweened as well as any special properties like <code>onComplete</code>, <code>ease</code>, etc. For example, to tween <code>mc.x</code> from 100 and <code>mc.y</code> from 200 and then call <code>myFunction</code>, do this: <code>TweenMax.from(mc, 1, {x:100, y:200, onComplete:myFunction});</code>
		 * @return TweenMax instance
		 * @see #to()
		 * @see #fromTo()
		 * @see #staggerFrom()
		 * @see com.greensock.TimelineLite#from()
		 * @see com.greensock.TimelineLite#staggerFrom()
		 */
		public static function from(target:Object, duration:Number, vars:Object):TweenMax {
			vars = _prepVars(vars, true);
			vars.runBackwards = true;
			return new TweenMax(target, duration, vars);
		}
		
		/**
		 * Static method for creating a TweenMax instance that allows you to define both the starting
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
TweenMax.fromTo([mc1, mc2, mc3], 1, {x:0}, {x:100});
</listing>
		 * <p>Even though 3 objects are animating, there is still only one tween created. 
		 * In order to stagger or offset the start times of each object animating, please see 
		 * the <code>staggerFromTo()</code> method (TimelineLite has one too).</p>
		 * 
		 * <p>For simple sequencing, you can use the <code>delay</code> special property
		 * (like <code>TweenMax.fromTo(mc, 1, {x:0}, {x:100, delay:0.5})</code>), 
		 * but it is highly recommended that you consider using TimelineLite (or TimelineMax) 
		 * for all but the simplest sequencing tasks. It has an identical <code>fromTo()</code> method
		 * that allows you to append tweens one-after-the-other and then control the entire sequence 
		 * as a whole. You can even have the tweens overlap as much as you want.</p>
		 * 
		 * @param target Target object (or array of objects) whose properties this tween affects. 
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is set in the <code>vars</code> parameter)
		 * @param fromVars An object defining the starting value for each property that should be tweened. For example, to tween <code>mc.x</code> from 100 and <code>mc.y</code> from 200, <code>fromVars</code> would look like this: <code>{x:100, y:200}</code>.
		 * @param toVars An object defining the end value for each property that should be tweened as well as any special properties like <code>onComplete</code>, <code>ease</code>, etc. For example, to tween <code>mc.x</code> from 0 to 100 and <code>mc.y</code> from 0 to 200 and then call <code>myFunction</code>, do this: <code>TweenMax.fromTo(mc, 1, {x:0, y:0}, {x:100, y:200, onComplete:myFunction});</code>
		 * @return TweenMax instance
		 * @see #to()
		 * @see #from()
		 * @see #staggerFromTo()
		 * @see com.greensock.TimelineLite#fromTo()
		 * @see com.greensock.TimelineLite#staggerFromTo()
		 */
		public static function fromTo(target:Object, duration:Number, fromVars:Object, toVars:Object):TweenMax {
			toVars = _prepVars(toVars, false);
			fromVars = _prepVars(fromVars, false);
			toVars.startAt = fromVars;
			toVars.immediateRender = (toVars.immediateRender != false && fromVars.immediateRender != false);
			return new TweenMax(target, duration, toVars);
		}
		
		/**
		 * Tweens an array of targets to a common set of destination values, but staggers their
		 * start times by a specified amount of time, creating an evenly-spaced sequence with a
		 * surprisingly small amount of code. For example, let's say you have an array containing
		 * references to a bunch of text fields that you'd like to fall away and fade out in a
		 * staggered fashion with 0.2 seconds between each tween's start time:
		 * 
		 * <listing version="3.0">
var textFields = [tf1, tf2, tf3, tf4, tf5];
TweenMax.staggerTo(textFields, 1, {y:"+150", ease:CubicIn.ease}, 0.2);
</listing>
		 * <p><code>staggerTo()</code> simply loops through the <code>targets</code> array and creates 
		 * a <code>to()</code> tween for each object and then returns an array containing all of
		 * the resulting tweens (one for each object).</p>
		 * 
		 * <p>If you can afford the slight increase in file size, it is usually better to use
		 * TimelineLite's <code>staggerTo()</code> method because it wraps the tweens in a
		 * TimelineLite instead of an array which makes controlling the group as a whole much
		 * easier. That way you could pause(), resume(), reverse(), restart() or change the timeScale
		 * of everything at once.</p>
		 * 
		 * <p>Note that if you define an <code>onComplete</code> (or any callback for that matter)
		 * in the <code>vars</code> parameter, it will be called for each tween rather than the whole 
		 * sequence. This can be very useful, but if you want to call a function after the entire
		 * sequence of tweens has completed, use the <code>onCompleteAll</code> parameter (the 5th parameter).</p>
		 * 
		 * <p><strong>JavaScript and AS2 note:</strong> - Due to the way JavaScript and AS2 don't 
		 * maintain scope (what "<code>this</code>" refers to, or the context) in function calls, 
		 * it can be useful to define the scope specifically. Therefore, in the JavaScript and AS2 
		 * versions accept an extra (7th) parameter for <code>onCompleteAllScope</code>.</p>
		 * 
		 * @param targets An array of target objects whose properties should be affected
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is defined in the <code>vars</code> parameter)
		 * @param vars An object defining the end value for each property that should be tweened as well as any special properties like <code>ease</code>. For example, to tween <code>x</code> to 100 and <code>y</code> to 200 for mc1, mc2, and mc3, staggering their start time by 0.25 seconds and then call <code>myFunction</code> when they last one has finished, do this: <code>TweenMax.staggerTo([mc1, mc2, mc3], 1, {x:100, y:200}, 0.25, myFunction})</code>.
		 * @param stagger Amount of time in seconds (or frames for frames-based tweens) to stagger the start time of each tween. For example, you might want to have 5 objects move down 100 pixels while fading out, and stagger the start times by 0.2 seconds - you could do: <code>TweenMax.staggerTo([mc1, mc2, mc3, mc4, mc5], 1, {y:"+100", alpha:0}, 0.2)</code>.
		 * @param onCompleteAll A function to call as soon as the entire sequence of tweens has completed.
		 * @param onCompleteAllParams An array of parameters to pass the <code>onCompleteAll</code> method.
		 * @return Array of TweenMax tweens (one for each object in the <code>targets</code> array)
		 * @see #staggerFrom()
		 * @see #staggerFromTo()
		 * @see com.greensock.TimelineLite#staggerTo()
		 */
		public static function staggerTo(targets:Array, duration:Number, vars:Object, stagger:Number=0, onCompleteAll:Function=null, onCompleteAllParams:Array=null):Array {
			vars = _prepVars(vars, false);
			var a:Array = [],
				l:int = targets.length,
				delay:Number = vars.delay || 0,
				copy:Object,
				i:int,
				p:String;
			for (i = 0; i < l; i++) {
				copy = {};
				for (p in vars) {
					copy[p] = vars[p];
				}
				copy.delay = delay;
				if (i == l - 1) if (onCompleteAll != null) {
					copy.onComplete = function():void {
						if (vars.onComplete) {
							vars.onComplete.apply(null, arguments);
						}
						onCompleteAll.apply(null, onCompleteAllParams);
					};
				}
				a[i] = new TweenMax(targets[i], duration, copy);
				delay += stagger;
			}
			return a;
		}
		
		/**
		 * Tweens an array of targets from a common set of destination values (using the current
		 * values as the destination), but staggers their start times by a specified amount of time, 
		 * creating an evenly-spaced sequence with a surprisingly small amount of code. For example, 
		 * let's say you have an array containing references to a bunch of text fields that you'd 
		 * like to drop into place while fading in, all in a staggered fashion with 0.2 seconds 
		 * between each tween's start time:
		 * 
		 * <listing version="3.0">
var textFields = [tf1, tf2, tf3, tf4, tf5];
TweenMax.staggerFrom(textFields, 1, {y:"+150"}, 0.2);
</listing>
		 * <p><code>staggerFrom()</code> simply loops through the <code>targets</code> array and creates 
		 * a <code>from()</code> tween for each object and then returns an array containing all of
		 * the resulting tweens (one for each object).</p>
		 * 
		 * <p>If you can afford the slight increase in file size, it is usually better to use
		 * TimelineLite's <code>staggerFrom()</code> method because it wraps the tweens in a
		 * TimelineLite instead of an array which makes controlling the group as a whole much
		 * easier. That way you could pause(), resume(), reverse(), restart() or change the timeScale
		 * of everything at once.</p>
		 * 
		 * <p>Note that if you define an <code>onComplete</code> (or any callback for that matter)
		 * in the <code>vars</code> parameter, it will be called for each tween rather than the whole 
		 * sequence. This can be very useful, but if you want to call a function after the entire
		 * sequence of tweens has completed, use the <code>onCompleteAll</code> parameter (the 5th parameter).</p>
		 * 
		 * <p>By default, <code>immediateRender</code> is <code>true</code> in 
		 * <code>from()</code> tweens, meaning that they immediately render their starting state 
		 * regardless of any delay that is specified. You can override this behavior by passing 
		 * <code>immediateRender:false</code> in the <code>vars</code> parameter so that it will 
		 * wait to render until the tween actually begins.</p>
		 * 
		 * <p><strong>JavaScript and AS2 note:</strong> - Due to the way JavaScript and AS2 don't 
		 * maintain scope (what "<code>this</code>" refers to, or the context) in function calls, 
		 * it can be useful to define the scope specifically. Therefore, in the JavaScript and AS2 
		 * versions accept an extra (7th) parameter for <code>onCompleteAllScope</code>.</p>
		 * 
		 * @param targets An array of target objects whose properties should be affected
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is defined in the <code>vars</code> parameter)
		 * @param vars An object defining the end value for each property that should be tweened as well as any special properties like <code>ease</code>. For example, to tween <code>x</code> to 100 and <code>y</code> to 200 for mc1, mc2, and mc3, staggering their start time by 0.25 seconds and then call <code>myFunction</code> when they last one has finished, do this: <code>TweenMax.staggerTo([mc1, mc2, mc3], 1, {x:100, y:200}, 0.25, myFunction})</code>.
		 * @param stagger Amount of time in seconds (or frames for frames-based tweens) to stagger the start time of each tween. For example, you might want to have 5 objects move down 100 pixels while fading out, and stagger the start times by 0.2 seconds - you could do: <code>TweenMax.staggerTo([mc1, mc2, mc3, mc4, mc5], 1, {y:"+100", alpha:0}, 0.2)</code>.
		 * @param onCompleteAll A function to call as soon as the entire sequence of tweens has completed
		 * @param onCompleteAllParams An array of parameters to pass the <code>onCompleteAll</code> method.
		 * @return An array of TweenMax instances (one for each object in the <code>targets</code> array)
		 * @see #staggerTo()
		 * @see #staggerFromTo()
		 * @see com.greensock.TimelineLite#staggerFrom()
		 */
		public static function staggerFrom(targets:Array, duration:Number, vars:Object, stagger:Number=0, onCompleteAll:Function=null, onCompleteAllParams:Array=null):Array {
			vars = _prepVars(vars, true);
			vars.runBackwards = true;
			if (vars.immediateRender != false) {
				vars.immediateRender = true;
			}
			return staggerTo(targets, duration, vars, stagger, onCompleteAll, onCompleteAllParams);
		}
		
		/**
		 * Tweens an array of targets from and to a common set of values, but staggers their
		 * start times by a specified amount of time, creating an evenly-spaced sequence with a
		 * surprisingly small amount of code. For example, let's say you have an array containing
		 * references to a bunch of text fields that you'd like to fade from alpha:1 to alpha:0 in a
		 * staggered fashion with 0.2 seconds between each tween's start time:
		 * 
		 * <listing version="3.0">
var textFields = [tf1, tf2, tf3, tf4, tf5];
TweenMax.staggerFromTo(textFields, 1, {alpha:1}, {alpha:0}, 0.2);
</listing>
		 * <p><code>staggerFromTo()</code> simply loops through the <code>targets</code> array and creates 
		 * a <code>fromTo()</code> tween for each object and then returns an array containing all of
		 * the resulting tweens (one for each object).</p>
		 * 
		 * <p>If you can afford the slight increase in file size, it is usually better to use
		 * TimelineLite's <code>staggerFromTo()</code> method because it wraps the tweens in a
		 * TimelineLite instead of an array which makes controlling the group as a whole much
		 * easier. That way you could pause(), resume(), reverse(), restart() or change the timeScale
		 * of everything at once.</p>
		 * 
		 * <p>Note that if you define an <code>onComplete</code> (or any callback for that matter)
		 * in the <code>vars</code> parameter, it will be called for each tween rather than the whole 
		 * sequence. This can be very useful, but if you want to call a function after the entire
		 * sequence of tweens has completed, use the <code>onCompleteAll</code> parameter (the 6th parameter).</p>
		 * 
		 * <p>By default, <code>immediateRender</code> is <code>true</code> in 
		 * <code>staggerFromTo()</code> tweens, meaning that they immediately render their starting state 
		 * regardless of any delay that is specified. This is done for convenience because it is 
		 * often the preferred behavior when setting things up on the screen to animate into place, but 
		 * you can override this behavior by passing <code>immediateRender:false</code> in the 
		 * <code>fromVars</code> or <code>toVars</code> parameter so that it will wait to render 
		 * the starting values until the tweens actually begin (often the desired behavior when inserting 
		 * into TimelineLite or TimelineMax instances).</p>
		 * 
		 * <p><strong>JavaScript and AS2 note:</strong> - Due to the way JavaScript and AS2 don't 
		 * maintain scope (what "<code>this</code>" refers to, or the context) in function calls, 
		 * it can be useful to define the scope specifically. Therefore, in the JavaScript and AS2 
		 * versions accept an extra (8th) parameter for <code>onCompleteAllScope</code>.</p>
		 * 
		 * @param targets An array of target objects whose properties should be affected
		 * @param duration Duration in seconds (or frames if <code>useFrames:true</code> is defined in the <code>vars</code> parameter)
		 * @param fromVars An object defining the starting value for each property that should be tweened. For example, to tween <code>x</code> from 100 and <code>y</code> from 200, <code>fromVars</code> would look like this: <code>{x:100, y:200}</code>.
		 * @param toVars An object defining the end value for each property that should be tweened as well as any special properties like <code>ease</code>. For example, to tween <code>x</code> from 0 to 100 and <code>y</code> from 0 to 200, staggering the start times by 0.2 seconds and then call <code>myFunction</code> when they all complete, do this: <code>TweenMax.staggerFromTo([mc1, mc2, mc3], 1, {x:0, y:0}, {x:100, y:200}, 0.2, myFunction});</code>
		 * @param stagger Amount of time in seconds (or frames if the timeline is frames-based) to stagger the start time of each tween. For example, you might want to have 5 objects move down 100 pixels while fading out, and stagger the start times by 0.2 seconds - you could do: <code>TweenMax.staggerTo([mc1, mc2, mc3, mc4, mc5], 1, {y:"+100", alpha:0}, 0.2)</code>.
		 * @param onCompleteAll A function to call as soon as the entire sequence of tweens has completed
		 * @param onCompleteAllParams An array of parameters to pass the <code>onCompleteAll</code> method.
		 * @return An array of TweenMax instances (one for each object in the <code>targets</code> array)
		 * @see #staggerTo()
		 * @see #staggerFrom()
		 * @see com.greensock.TimelineLite#staggerFromTo()
		 */
		public static function staggerFromTo(targets:Array, duration:Number, fromVars:Object, toVars:Object, stagger:Number=0, onCompleteAll:Function=null, onCompleteAllParams:Array=null):Array {
			toVars = _prepVars(toVars, false);
			fromVars = _prepVars(fromVars, false);
			toVars.startAt = fromVars;
			toVars.immediateRender = (toVars.immediateRender != false && fromVars.immediateRender != false);
			return staggerTo(targets, duration, toVars, stagger, onCompleteAll, onCompleteAllParams);
		}
		
		/** @private [deprecated] - included here as an alias for backward compatibility **/
		public static var allTo:Function = staggerTo;
		
		/** @private [deprecated] - included here as an alias for backward compatibility **/
		public static var allFrom:Function = staggerFrom;
		
		/** @private [deprecated] - included here as an alias for backward compatibility **/
		public static var allFromTo:Function = staggerFromTo; 
		
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
		 * <p><code>TweenMax.delayedCall(delay, callback, params, scope, useFrames)</code> <em>[JavaScript and AS2 only]</em></p>
		 * 
		 * <listing version="3.0">
//calls myFunction after 1 second and passes 2 parameters:
TweenMax.delayedCall(1, myFunction, ["param1", 2]);
		 
function myFunction(param1, param2) {
	//do stuff
}
</listing>
		 * 
		 * @param delay Delay in seconds (or frames if <code>useFrames</code> is <code>true</code>) before the function should be called
		 * @param callback Function to call
		 * @param params An Array of parameters to pass the function (optional).
		 * @param useFrames If the delay should be measured in frames instead of seconds, set <code>useFrames</code> to <code>true</code> (default is <code>false</code>)
		 * @return TweenMax instance
		 */
		public static function delayedCall(delay:Number, callback:Function, params:Array=null, useFrames:Boolean=false):TweenMax {
			return new TweenMax(callback, 0, {delay:delay, onComplete:callback, onCompleteParams:params, onReverseComplete:callback, onReverseCompleteParams:params, immediateRender:false, useFrames:useFrames, overwrite:0});
		}
		
		/**
		 * Immediately sets properties of the target accordingly - essentially a zero-duration <code>to()</code> tween with a more 
		 * intuitive name. So the following lines produce identical results:
		 * 
		 * <listing version="3.0">
TweenMax.set(myObject, {x:100, y:50, alpha:0});
TweenMax.to(myObject, 0, {x:100, y:50, alpha:0});
</listing>
		 * 
		 * <p>And of course you can use an array to set the properties of multiple targets at the same time, like:</p>
		 * 
		 * <listing version="3.0">
TweenMax.set([obj1, obj2, obj3], {x:100, y:50, alpha:0});
		 </listing>
		 * 
		 * @param target Target object (or array of objects) whose properties will be affected. 
		 * @param vars An object defining the value for each property that should be set. For example, to set <code>mc.x</code> to 100 and <code>mc.y</code> to 200, do this: <code>TweenMax.set(mc, {x:100, y:200});</code>
		 * @return A TweenMax instance (with a duration of 0) which can optionally be inserted into a TimelineLite/Max instance (although it's typically more concise to just use the timeline's <code>set()</code> method).
		 */
		public static function set(target:Object, vars:Object):TweenMax {
			return new TweenMax(target, 0, vars);
		}
		
		/**
		 * Reports whether or not a particular object is actively tweening. If a tween
		 * is paused, is completed, or hasn't started yet, it isn't considered active.
		 * 
		 * @param target Target object whose tweens you're checking
		 * @return Boolean value indicating whether or not any active tweens were found
		 */
		public static function isTweening(target:Object):Boolean {
			return (TweenLite.getTweensOf(target, true).length > 0);
		}
		
		/**
		 * Returns an array containing all tweens (and optionally timelines too, excluding the root timelines).
		 * If your goal is to affect all of the tweens/timelines/delayedCalls (like to <code>pause()</code> them
		 * or <code>reverse()</code> or alter their <code>timeScale</code>), you might want to consider using the
		 * static <code>TimelineLite.exportRoot()</code> method instead because it provides a single instance
		 * that you can use to control everything. 
		 * 
		 * @param includeTimelines If <code>true</code>, TimelineLite and TimelineMax instances will also be included.
		 * @return Array of tweens/timelines
		 * @see com.greensock.TimelineLite#exportRoot()
		 */
		public static function getAllTweens(includeTimelines:Boolean=false):Array {
			var a:Array = _getChildrenOf(_rootTimeline, includeTimelines);
			return a.concat( _getChildrenOf(_rootFramesTimeline, includeTimelines) );
		}
		
		/** @private **/
		protected static function _getChildrenOf(timeline:SimpleTimeline, includeTimelines:Boolean):Array {
			if (timeline == null) {
				return [];
			}
			var a:Array = [],
				cnt:int = 0,
				tween:Animation = timeline._first;
			while (tween) {
				if (tween is TweenLite) {
					a[cnt++] = tween;
				} else {
					if (includeTimelines) {
						a[cnt++] = tween;
					}
					a = a.concat(_getChildrenOf(SimpleTimeline(tween), includeTimelines));
					cnt = a.length;
				}
				tween = tween._next;
			}
			return a;
		}
		
		/**
		 * Kills all tweens and/or delayedCalls/callbacks, and/or timelines, optionally forcing them to 
		 * completion first. The various parameters provide a way to specify exactly which types you want
		 * to kill
		 * 
		 * <listing version="3.0">
//kill everything
TweenMax.killAll();

//kill only tweens, but not delayedCalls or timelines
TweenMax.killAll(false, true, false, false);

//kill only delayedCalls
TweenMax.killAll(false, false, true, false);
</listing>
		 *  
		 * @param complete Determines whether or not the tweens/delayedCalls/timelines should be forced to completion before being killed.
		 * @param tweens If <code>true</code>, all tweens will be killed (TweenLite and TweenMax instances)
		 * @param delayedCalls If <code>true</code>, all delayedCalls will be killed. TimelineMax callbacks are treated the same as delayedCalls.
		 * @param timelines If <code>true</code>, all TimelineLite and TimelineMax instances will be killed.
		 */
		public static function killAll(complete:Boolean=false, tweens:Boolean=true, delayedCalls:Boolean=true, timelines:Boolean=true):void {
			var a:Array = getAllTweens(timelines),
				l:int = a.length,
				isDC:Boolean,
				allTrue:Boolean = (tweens && delayedCalls && timelines),
				tween:Animation, i:int;
			for (i = 0; i < l; i++) {
				tween = a[i];
				if (allTrue || (tween is SimpleTimeline) || ((isDC = (TweenLite(tween).target == TweenLite(tween).vars.onComplete)) && delayedCalls) || (tweens && !isDC)) {
					if (complete) {
						tween.totalTime(tween._reversed ? 0 : tween.totalDuration());     
					} else {
						tween._enabled(false, false);
					}
				}
			}
		}
		
		/**
		 * [AS3/AS2 only]
		 * Kills all tweens of the children of a particular MovieClip/DisplayObjectContainer, optionally forcing them to completion first.
		 * 
		 * @param parent The parent MovieClip/DisplayObjectContainer whose children's tweens should be killed. 
		 * @param complete If <code>true</code>, the tweens will be forced to completion before being killed.
		 */
		public static function killChildTweensOf(parent:DisplayObjectContainer, complete:Boolean=false):void {
			var a:Array = getAllTweens(false),
				l:int = a.length, i:int;
			for (i = 0; i < l; i++) {
				if (_containsChildOf(parent, a[i].target)) {
					if (complete) {
						a[i].totalTime(a[i].totalDuration());
					} else {
						a[i]._enabled(false, false);
					}
				}
			}
		}
		
		/** @private **/
		private static function _containsChildOf(parent:DisplayObjectContainer, obj:Object):Boolean {
			var i:int, curParent:DisplayObjectContainer;
			if (obj is Array) {
				i = obj.length;
				while (--i > -1) {
					if (_containsChildOf(parent, obj[i])) {
						return true;
					}
				}
			} else if (obj is DisplayObject) {
				curParent = obj.parent;
				while (curParent) {
					if (curParent == parent) {
						return true;
					}
					curParent = curParent.parent;
				}
			}
			return false;
		}
		
		/**
		 * [deprecated] Pauses all tweens and/or delayedCalls/callbacks and/or timelines. This literally
		 * changes the <code>paused</code> state of all affected tweens/delayedCalls/timelines, but a
		 * more flexible way to globally control things is to use the <code>TimelineLite.exportRoot()</code> method
		 * which essentially wraps all of the tweens/timelines/delayedCalls on the root timeline into a
		 * TimelineLite instance so that you can <code>pause(), resume()</code>, or even <code>reverse()</code>
		 * or alter the <code>timeScale</code> without affecting animations that you create after the export.
		 * This also avoids having to alter the <code>paused</code> state of every individual 
		 * tween/delayedCall/timeline - controlling the TimelineLite that contains the exported animations
		 * delivers the same effect visually, but does so in a more elegant and flexible way.
		 * 
		 * @param tweens If <code>true</code>, all tweens will be paused.
		 * @param delayedCalls If <code>true</code>, all delayedCalls will be paused. timeline callbacks are treated the same as delayedCalls.
		 * @param timelines If <code>true</code>, all TimelineLite and TimelineMax instances will be paused (at least the ones who haven't finished and been removed from their parent timeline)
		 * 
		 * @see com.greensock.TimelineLite#exportRoot()
		 */
		public static function pauseAll(tweens:Boolean=true, delayedCalls:Boolean=true, timelines:Boolean=true):void {
			_changePause(true, tweens, delayedCalls, timelines);
		}
		
		/**
		 * [deprecated] Resumes all paused tweens and/or delayedCalls/callbacks and/or timelines. This literally
		 * changes the <code>paused</code> state of all affected tweens/delayedCalls/timelines, but a
		 * more flexible way to globally control things is to use the <code>TimelineLite.exportRoot()</code> method
		 * which essentially wraps all of the tweens/timelines/delayedCalls on the root timeline into a
		 * TimelineLite instance so that you can <code>pause(), resume()</code>, or even <code>reverse()</code>
		 * or alter the <code>timeScale</code> without affecting animations that you create after the export.
		 * This also avoids having to alter the <code>paused</code> state of every individual 
		 * tween/delayedCall/timeline - controlling the TimelineLite that contains the exported animations
		 * delivers the same effect visually, but does so in a more elegant and flexible way.
		 * 
		 * @param tweens If <code>true</code>, all tweens will be resumed.
		 * @param delayedCalls If <code>true</code>, all delayedCalls will be resumed. timeline callbacks are treated the same as delayedCalls.
		 * @param timelines If <code>true</code>, all TimelineLite and TimelineMax instances will be resumed (at least the ones who haven't finished and been removed from their parent timeline)
		 * @see com.greensock.TimelineLite#exportRoot()
		 */
		public static function resumeAll(tweens:Boolean=true, delayedCalls:Boolean=true, timelines:Boolean=true):void {
			_changePause(false, tweens, delayedCalls, timelines);
		}
		
		/**
		 * @private
		 * Changes the paused state of all tweens and/or delayedCalls/callbacks
		 * 
		 * @param pause Desired paused state
		 * @param tweens If true, all tweens will be affected.
		 * @param delayedCalls If true, all delayedCalls will be affected. TimelineMax callbacks are treated the same as delayedCalls.
		 * @param timelines If <code>true</code>, all TimelineLite and TimelineMax instances will be affected (at least the ones who haven't finished and been removed from their parent timeline)
		 */
		private static function _changePause(pause:Boolean, tweens:Boolean=true, delayedCalls:Boolean=false, timelines:Boolean=true):void {
			var a:Array = getAllTweens(timelines),
				isDC:Boolean, 
				tween:Animation,
				allTrue:Boolean = (tweens && delayedCalls && timelines),
				i:int = a.length;
			while (--i > -1) {
				tween = a[i];
				isDC = (tween is TweenLite && TweenLite(tween).target == tween.vars.onComplete);
				if (allTrue || (tween is SimpleTimeline) || (isDC && delayedCalls) || (tweens && !isDC)) {
					tween.paused(pause);
				}
			}
		}
		
	
//---- GETTERS / SETTERS ----------------------------------------------------------------------------------------------------------
		
		
		/** 
		 * Gets or sets the tween's progress which is a value between 0 and 1 indicating the position 
		 * of the virtual playhead (excluding repeats) where 0 is at the beginning, 0.5 is halfway complete, 
		 * and 1 is complete. If the tween has a non-zero <code>repeat</code> defined, <code>progress</code> 
		 * and <code>totalProgress</code> will be different because <code>progress</code> doesn't include any 
		 * repeats or repeatDelays whereas <code>totalProgress</code> does. For example, if a TweenMax instance 
		 * is set to repeat once, at the end of the first cycle <code>totalProgress</code> would only be 0.5 
		 * whereas <code>progress</code> would be 1. If you watched both properties over the course of the entire 
		 * animation, you'd see <code>progress</code> go from 0 to 1 twice (once for each cycle) in the 
		 * same time it takes the <code>totalProgress</code> to go from 0 to 1 once.
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myTween.progress(0.5).play();</code></p>
		 * 
		 * <listing version="3.0">
var progress = myTween.progress(); //gets current progress
myTween.progress( 0.25 ); //sets progress to one quarter finished
</listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @param suppressEvents If <code>true</code>, no events or callbacks will be triggered when the playhead moves to the new position.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #totalProgress()
		 * @see #seek()
		 * @see #time()
		 * @see #totalTime()
		 **/
		override public function progress(value:Number=NaN, suppressEvents:Boolean=false):* {
			return (!arguments.length) ? _time / duration() : totalTime( duration() * ((_yoyo && (_cycle & 1) !== 0) ? 1 - value : value) + (_cycle * (_duration + _repeatDelay)), suppressEvents);
		}
		
		/** 
		 * Gets or sets the tween's totalProgress which is a value between 0 and 1 indicating the position 
		 * of the virtual playhead (including repeats) where 0 is at the beginning, 0.5 is halfway complete, 
		 * and 1 is complete. If the tween has a non-zero <code>repeat</code> defined, <code>progress</code> 
		 * and <code>totalProgress</code> will be different because <code>progress</code> doesn't include 
		 * any repeats or repeatDelays whereas <code>totalProgress</code> does. For example, if a TweenMax 
		 * instance is set to repeat once, at the end of the first cycle <code>totalProgress</code> would 
		 * only be 0.5 whereas <code>progress</code> would be 1. If you watched both properties over the 
		 * course of the entire animation, you'd see <code>progress</code> go from 0 to 1 twice (once for 
		 * each cycle) in the same time it takes the <code>totalProgress</code> to go from 0 to 1 once.
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myTween.totalProgress(0.5).play();</code></p>
		 * 
		 * <listing version="3.0">
var progress = myTween.totalProgress(); //gets total progress
myTween.totalProgress( 0.25 ); //sets total progress to one quarter finished
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
		override public function totalProgress(value:Number=NaN, suppressEvents:Boolean=false):* {
			return (!arguments.length) ? _totalTime / totalDuration() : totalTime( totalDuration() * value, suppressEvents);
		}
		
		/**
		 * Gets or sets the local position of the playhead (essentially the current time), <strong>not</strong> 
		 * including any repeats or repeatDelays. If the tween has a non-zero <code>repeat</code>, its <code>time</code> 
		 * goes back to zero upon repeating even though the <code>totalTime</code> continues forward linearly 
		 * (or if <code>yoyo</code> is <code>true</code>, the <code>time</code> alternates between moving forward 
		 * and backward). <code>time</code> never exceeds the duration whereas the <code>totalTime</code> reflects 
		 * the overall time including any repeats and repeatDelays. 
		 * 
		 * <p>For example, if a TweenMax instance has a <code>duration</code> of 2 and a repeat of 3, 
		 * <code>totalTime</code> will go from 0 to 8 during the course of the tween (plays once then 
		 * repeats 3 times, making 4 total cycles) whereas <code>time</code> would go from 0 to 2 a 
		 * total of 4 times.</p>
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining.</p>
		 * 
		 * <listing version="3.0">
var currentTime = myTween.time(); //gets current time
myTween.time(2); //sets time, jumping to new value just like seek().
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
		override public function time(value:Number=NaN, suppressEvents:Boolean=false):* {
			if (!arguments.length) {
				return _time;
			}
			if (_dirty) {
				totalDuration();
			}
			if (value > _duration) {
				value = _duration;
			}
			if (_yoyo && (_cycle & 1) !== 0) {
				value = (_duration - value) + (_cycle * (_duration + _repeatDelay));
			} else if (_repeat != 0) {
				value += _cycle * (_duration + _repeatDelay);
			}
			return totalTime(value, suppressEvents);
		}
		
		/** @inheritDoc **/
		override public function duration(value:Number=NaN):* {
			if (!arguments.length) {
				return this._duration; //don't set _dirty = false because there could be repeats that haven't been factored into the _totalDuration yet. Otherwise, if you create a repeated TweenMax and then immediately check its duration(), it would cache the value and the totalDuration would not be correct, thus repeats wouldn't take effect.
			}
			return super.duration(value);
		}
		
		/**
		 * Gets or sets the total duration of the tween in seconds (or frames for frames-based tweens) 
		 * <strong>including</strong> any repeats or repeatDelays. <code>duration</code>, by contrast, does 
		 * <strong>NOT</strong> include repeats and repeatDelays. For example, if the tween has a 
		 * <code>duration</code> of 10, a <code>repeat</code> of 1 and a <code>repeatDelay</code> of 2, 
		 * the <code>totalDuration</code> would be 22.
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining.</p>
		 * 
		 * <listing version="3.0">
var total = myTween.totalDuration(); //gets total duration
myTween.totalDuration(10); //sets the total duration
</listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining. Negative values will be interpreted from the <strong>END</strong> of the animation.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #duration()
		 * @see #timeScale()
		 **/
		override public function totalDuration(value:Number=NaN):* {
			if (!arguments.length) {
				if (_dirty) {
					//instead of Infinity, we use 999999999999 so that we can accommodate reverses
					_totalDuration = (_repeat == -1) ? 999999999999 : _duration * (_repeat + 1) + (_repeatDelay * _repeat);
					_dirty = false;
				}
				return _totalDuration;
			}
			return (_repeat == -1) ? this : duration( (value - (_repeat * _repeatDelay)) / (_repeat + 1) );
		}
		
		/** 
		 * Gets or sets the number of times that the tween should repeat after its first iteration. For example, 
		 * if <code>repeat</code> is 1, the tween will play a total of twice (the initial play
		 * plus 1 repeat). To repeat indefinitely, use -1. <code>repeat</code> should always be an integer.
		 * 
		 * <p>To cause the repeats to alternate between forward and backward, set <code>yoyo</code> to 
		 * <code>true</code>. To add a time gap between repeats, use <code>repeatDelay</code>. You can 
		 * set the initial <code>repeat</code> value via the <code>vars</code> parameter, like:</p>
		 * 
		 * <p><code>
		 * TweenMax.to(mc, 1, {x:100, repeat:2});
		 * </code></p>
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myTween.repeat(2).yoyo(true).play();</code></p>
		 * 
		 * <listing version="3.0">
var repeat = myTween.repeat(); //gets current repeat value
myTween.repeat(2); //sets repeat to 2
</listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #repeatDelay()
		 * @see #yoyo()
		 **/
		public function repeat(value:int=0):* {
			if (!arguments.length) {
				return _repeat;
			}
			_repeat = value;
			return _uncache(true);
		}
		
		/**
		 * Gets or sets the amount of time in seconds (or frames for frames-based tweens) between repeats. 
		 * For example, if <code>repeat</code> is 2 and <code>repeatDelay</code> is 1, the tween will 
		 * play initially, then wait for 1 second before it repeats, then play again, then wait 1 second 
		 * again before doing its final repeat. You can set the initial <code>repeatDelay</code> value 
		 * via the <code>vars</code> parameter, like:
		 * 
		 * <p><code>
		 * TweenMax.to(mc, 1, {x:100, repeat:2, repeatDelay:1});
		 * </code></p>
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myTween.repeat(2).yoyo(true).repeatDelay(0.5).play();</code></p>
		 * 
		 * <listing version="3.0">
var repeatDelay = myTween.repeatDelay(); //gets current repeatDelay value
myTween.repeatDelay(2); //sets repeatDelay to 2
</listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #repeat()
		 * @see #yoyo()
		 **/
		public function repeatDelay(value:Number=NaN):* {
			if (!arguments.length) {
				return _repeatDelay;
			}
			_repeatDelay = value;
			return _uncache(true);
		}
		
		/**
		 * Gets or sets the tween's <code>yoyo</code> state, where <code>true</code> causes
		 * the tween to go back and forth, alternating backward and forward on each 
		 * <code>repeat</code>. <code>yoyo</code> works in conjunction with <code>repeat</code>,
		 * where <code>repeat</code> controls how many times the tween repeats, and <code>yoyo</code>
		 * controls whether or not each repeat alternates direction. So in order to make a tween yoyo, 
		 * you must set its <code>repeat</code> to a non-zero value.
		 * Yoyo-ing, has no affect on the tween's "<code>reversed</code>" property. For example, 
		 * if <code>repeat</code> is 2 and <code>yoyo</code> is <code>false</code>, it will look like: 
		 * start - 1 - 2 - 3 - 1 - 2 - 3 - 1 - 2 - 3 - end. But if <code>yoyo</code> is <code>true</code>, 
		 * it will look like: start - 1 - 2 - 3 - 3 - 2 - 1 - 1 - 2 - 3 - end.
		 * 
		 * <p>You can set the <code>yoyo</code> property initially by passing <code>yoyo:true</code>
		 * in the <code>vars</code> parameter, like: <code>TweenMax.to(mc, 1, {x:100, repeat:1, yoyo:true});</code></p>
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myAnimation.yoyo(true).repeat(3).timeScale(2).play(0.5);</code></p>
		 * 
		 * <listing version="3.0">
var yoyo = myAnimation.yoyo(); //gets current yoyo state
myAnimation.yoyo( true ); //sets yoyo to true
</listing>
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #repeat()
		 * @see #repeatDelay()
		 **/
		public function yoyo(value:Boolean=false):* {
			if (!arguments.length) {
				return _yoyo;
			}
			_yoyo = value;
			return this;
		}
		
		/** @private [deprecated] Multiplier describing the speed of the root timelines where 1 is normal speed, 0.5 is half-speed, 2 is double speed, etc. The lowest globalTimeScale possible is 0.0001. Deprecated in favor of <code>TimelineLite.exportRoot()</code> **/
		public static function globalTimeScale(value:Number=NaN):Number {
			if (!arguments.length) {
				return (_rootTimeline == null) ? 1 : _rootTimeline._timeScale;
			}
			value = value || 0.0001; //can't allow zero because it'll throw the math off
			if (_rootTimeline == null) {
				TweenLite.to({}, 0, {}); //forces initialization in case globalTimeScale is set before any tweens are created.
			}
			var tl:SimpleTimeline = _rootTimeline,
				t:Number = (getTimer() / 1000);
			tl._startTime = t - ((t - tl._startTime) * tl._timeScale / value);
			tl = _rootFramesTimeline;
			t = _rootFrame;
			tl._startTime = t - ((t - tl._startTime) * tl._timeScale / value);
			_rootFramesTimeline._timeScale = _rootTimeline._timeScale = value;
			return value;
		}
		
		
	}
}

