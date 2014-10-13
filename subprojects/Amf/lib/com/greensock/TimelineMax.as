/**
 * VERSION: 12.1.5
 * DATE: 2014-07-19
 * AS3 (AS2 version is also available)
 * UPDATES AND DOCS AT: http://www.greensock.com/timelinemax/
 **/
package com.greensock {
	import com.greensock.core.SimpleTimeline;
	import com.greensock.core.Animation;
	import com.greensock.easing.Ease;
	import com.greensock.events.TweenEvent;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
/**
 * TimelineMax extends TimelineLite, offering exactly the same functionality plus useful 
 * (but non-essential) features like repeat, repeatDelay, yoyo, currentLabel(), addCallback(), 
 * removeCallback(), tweenTo(), tweenFromTo(), getLabelAfter(), getLabelBefore(),
 * getActive(), AS3 event dispatching (and probably more in the future). It is the ultimate 
 * sequencing tool that acts like a container for tweens and other timelines, making it 
 * simple to control them as a whole and precisely manage their timing. Without TimelineMax 
 * (or its little brother TimelineLite), building complex sequences would be far more cumbersome 
 * because you'd need to use the <code>delay</code> special property for everything which would 
 * make future edits far more tedius. Here is a basic example: 
 * <listing version="3.0">
TweenLite.to(mc, 1, {x:100});
TweenLite.to(mc, 1, {y:50, delay:1});
TweenLite.to(mc, 1, {alpha:0, delay:2});
</listing>
 * The above code animates <code>mc.x</code> to 100, then <code>mc.y</code> to 50, and finally 
 * <code>mc.alpha</code> to 0 (notice the <code>delay</code> in all but the first tween). But 
 * imagine if you wanted to increase the duration of the first tween to 1.5 - you'd need to
 * adjust every delay thereafter. And what if you want to <code>pause()</code> the whole 
 * sequence or <code>restart()</code> it or <code>reverse()</code> it on-the-fly or repeat
 * it twice? This becomes quite messy (or flat-out impossible), but TimelineMax makes it 
 * incredibly simple:
 * 
 * <listing version="3.0">
var tl = new TimelineMax({repeat:2, repeatDelay:1});
tl.add( TweenLite.to(mc, 1, {x:100}) );
tl.add( TweenLite.to(mc, 1, {y:50}) );
tl.add( TweenLite.to(mc, 1, {alpha:0}) );
 
//then later, control the whole thing...
tl.pause();
tl.resume();
tl.seek(1.5);
tl.reverse();
...
</listing>
 * Or use the convenient <code>to()</code> method and chaining to make it even shorter:
 * <listing version="3.0">
var tl = new TimelineMax();
tl.to(mc, 1, {x:100}).to(mc, 1, {y:50}).to(mc, 1, {alpha:0});
</listing>
 * 
 * <p>Now you can feel free to adjust any of the tweens without worrying about trickle-down
 * changes to delays. Increase the duration of that first tween and everything automatically
 * adjusts.</p>
 * 
 * <p>Here are some other benefits and features of TimelineMax:</p>
 * 
 * 	<ul>
 * 		<li> Things can overlap on the timeline as much as you want. You have complete control 
 * 			over where tweens/timelines are placed. Most other animation tools can only do basic 
 * 			one-after-the-other sequencing but can't allow things to overlap. Imagine appending
 * 			a tween that moves an object and you want it to start fading out 0.5 seconds before the 
 * 			end of that tween? With TimelineMax it's easy.</li>
 * 
 * 		<li> Add labels, callbacks, play(), stop(), seek(), restart(), and even reverse() smoothly anytime.</li>
 * 		
 * 		<li> Nest timelines within timelines as deeply as you want. This means you can modularize
 * 			your code and make it far more efficient. Imagine building your app with common animateIn() 
 * 			and animateOut() methods that return a tween or timeline instance, then you can string 
 * 			things together like 
 * 			<code>myTimeline.add( myObject.animateIn() ).add( myObject.animateOut(), "+=4").add( myObject2.animateIn(), "-=0.5")...</code></li>
 * 		
 * 		<li> Speed up or slow down the entire timeline with its <code>timeScale()</code> method. 
 * 			You can even tween it to gradually speed up or slow down the animation smoothly.</li>
 * 		
 * 		<li> Get or set the progress of the timeline using its <code>progress()</code> or 
 * 			<code>totalProgress()</code> methods. For example, to skip to the halfway point, 
 * 			set <code>myTimeline.progress(0.5);</code></li>
 * 		  
 * 		<li> Tween the <code>time, totalTime, progress,</code> or <code>totalProgress</code> to 
 * 			fastforward/rewind the timeline. You could even attach a slider to one of these to give the 
 * 			user the ability to drag forward/backward through the timeline.</li>
 * 		  
 * 		<li> Add <code>onComplete, onStart, onUpdate, onRepeat</code> and/or <code>onReverseComplete</code> 
 * 			callbacks using the constructor's <code>vars</code> object like
 * 			<code>var tl = new TimelineMax({onComplete:myFunction});</code></li>
 * 
 * 		<li> Kill the tweens of a particular object inside the timeline with <code>kill(null, target)</code> 
 * 			or get the tweens of an object with <code>getTweensOf()</code> or get all the tweens/timelines 
 * 			in the timeline with <code>getChildren()</code></li>
 * 		  
 * 		<li> Set the timeline to repeat any number of times or indefinitely. You can even set a delay
 * 		  	between each repeat cycle and/or cause the repeat cycles to yoyo, appearing to reverse direction
 * 		  	every other cycle. </li>
 * 		
 * 		<li> listen for START, UPDATE, REPEAT, REVERSE_COMPLETE, and COMPLETE events.</li>
 * 		
 * 		<li> get the active tweens in the timeline with getActive().</li>
 * 		  
 * 		<li> By passing <code>useFrames:true</code> in the <code>vars</code> parameter, you can
 * 			base the timing on frames instead of seconds. Please note, however, that
 * 		  	the timeline's timing mode dictates its childrens' timing mode as well. </li>
 * 		
 * 		<li> Get the <code>currentLabel()</code> or find labels at various positions in the timeline
 * 			using <code>getLabelAfter()</code> and <code>getLabelBefore()</code></li>
 * 		
 * 		<li> You can export all the tween/timelines from the root (master) timeline anytime into 
 * 			a TimelineLite instance using <code>TimelineLite.exportRoot()</code> so that
 * 			you can <code>pause()</code> them all or <code>reverse()</code> or alter their 
 * 			<code>timeScale</code>, etc. without affecting tweens/timelines that you create in
 * 			the future. Imagine a game that has all its animation driven by the GreenSock 
 * 			Animation Platform and it needs to pause or slow down while a status screen pops up. 
 * 			Very easy.</li>
 * 		  
 * 	</ul>
 * 
 * 
 * <p><strong>SPECIAL PROPERTIES:</strong></p>
 * <p>You can optionally use the constructor's <code>vars</code> parameter to define any of
 * the special properties below (syntax example: <code>new TimelineMax({onComplete:myFunction, repeat:2, repeatDelay:1, yoyo:true});</code></p>
 * 
 * <ul>
 * 	<li><strong> delay </strong>:<em> Number</em> -
 * 				 Amount of delay in seconds (or frames for frames-based tweens) before the timeline should begin.</li>
 * 
 *  <li><strong> paused </strong>:<em> Boolean</em> -
 * 				 If <code>true</code>, the timeline will pause itself immediately upon creation (by default, 
 * 				 timelines automatically begin playing immediately). If you plan to create a TimelineMax and 
 * 				 then populate it later (after one or more frames elapse), it is typically best to set 
 * 				 <code>paused:true</code> and then <code>play()</code> after you populate it.</li>
 * 	
 * 	<li><strong> onComplete </strong>:<em> Function</em> -
 * 				 A function that should be called when the timeline has completed</li>
 * 	
 * 	<li><strong> onCompleteParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the <code>onComplete</code> function. For example,
 * 				 <code>new TimelineMax({onComplete:myFunction, onCompleteParams:["param1", "param2"]});</code>
 * 				 To self-reference the timeline instance itself in one of the parameters, use <code>"{self}"</code>,
 * 				 like: <code>onCompleteParams:["{self}", "param2"]</code></li>
 * 	
 * 	<li><strong> useFrames </strong>:<em> Boolean</em> -
 * 				 If <code>useFrames</code> is <code>true</code>, the timelines's timing will be 
 * 				 based on frames instead of seconds because it is intially added to the root
 * 				 frames-based timeline. This causes both its <code>duration</code>
 * 				 and <code>delay</code> to be based on frames. An animations's timing mode is 
 * 				 always determined by its parent <code>timeline</code>.</li>
 * 
 *  <li><strong> tweens </strong>:<em> Array</em> -
 * 				 To immediately insert several tweens into the timeline, use the <code>tweens</code> 
 * 				 special property to pass in an Array of TweenLite/TweenMax/TimelineLite/TimelineMax 
 * 				 instances. You can use this in conjunction with the <code>align</code> and 
 * 				 <code>stagger</code> special properties to set up complex sequences with minimal code.
 * 				 These values simply get passed to the <code>add()</code> method.</li>
 * 	
 * 	<li><strong> align </strong>:<em> String</em> -
 * 				 Only used in conjunction with the <code>tweens</code> special property when multiple 
 * 				 tweens are	to be inserted immediately. The value simply gets passed to the 
 * 				 <code>add()</code> method. The default is <code>"normal"</code>. 
 * 				 Options are:
 * 					<ul>
 * 						<li><strong><code>"sequence"</code></strong>: aligns the tweens one-after-the-other in a sequence</li>
 * 						<li><strong><code>"start"</code></strong>: aligns the start times of all of the tweens (ignores delays)</li>
 * 						<li><strong><code>"normal"</code></strong>: aligns the start times of all the tweens (honors delays)</li>
 * 					</ul>
 * 				The <code>align</code> special property does <strong>not</strong> force all child 
 * 				tweens/timelines to maintain relative positioning, so for example, if you use 
 * 				<code>"sequence"</code> and then later change the duration of one of the nested tweens, 
 * 				it does <strong>not</strong> force all subsequent timelines to change their position.
 * 				The <code>align</code> special property only affects the alignment of the tweens that are
 * 				initially placed into the timeline through the <code>tweens</code> special property of 
 * 				the <code>vars</code> object.</li>
 * 										
 * 	<li><strong> stagger </strong>:<em> Number</em> -
 * 				 Only used in conjunction with the <code>tweens</code> special property when multiple 
 * 				 tweens are	to be inserted immediately. It staggers the tweens by a set amount of time 
 * 				 in seconds (or in frames if <code>useFrames</code> is true). For example, if the 
 * 				 stagger value is 0.5 and the "align" property is set to <code>"start"</code>, the 
 * 				 second tween will start 0.5 seconds after the first one starts, then 0.5 seconds 
 * 				 later the third one will start, etc. If the align property is <code>"sequence"</code>,
 * 				 there would be 0.5 seconds added between each tween. This value simply gets 
 * 				 passed to the <code>add()</code> method. Default is 0.</li>
 * 
 *  <li><strong> onStart </strong>:<em> Function</em> -
 * 				 A function that should be called when the timeline begins (when its <code>time</code>
 * 				 changes from 0 to some other value which can happen more than once if the 
 * 				 timeline is restarted multiple times).</li>
 * 	
 * 	<li><strong> onStartParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the <code>onStart</code> function. For example, 
 * 				 <code>new TimelineMax({onStart:myFunction, onStartParams:["param1", "param2"]});</code>
 * 				 To self-reference the timeline instance itself in one of the parameters, use <code>"{self}"</code>,
 * 				 like: <code>onStartParams:["{self}", "param2"]</code></li>
 * 	
 * 	<li><strong> onUpdate </strong>:<em> Function</em> -
 * 				 A function that should be called every time the timeline updates  
 * 				 (on every frame while the timeline is active)</li>
 * 	
 * 	<li><strong> onUpdateParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the <code>onUpdate</code> function. For example,
 * 				 <code>new TimelineMax({onUpdate:myFunction, onUpdateParams:["param1", "param2"]});</code>
 * 				 To self-reference the timeline instance itself in one of the parameters, use <code>"{self}"</code>,
 * 				 like: <code>onUpdateParams:["{self}", "param2"]</code></li>
 * 	
 * 	<li><strong> onReverseComplete </strong>:<em> Function</em> -
 * 				 A function that should be called when the timeline has reached its beginning again from the 
 * 				 reverse direction. For example, if <code>reverse()</code> is called, the timeline will move
 * 				 back towards its beginning and when its <code>time</code> reaches 0, <code>onReverseComplete</code>
 * 				 will be called. This can also happen if the timeline is placed in a TimelineLite or TimelineMax 
 * 				 instance that gets reversed and plays the timeline backwards to (or past) the beginning.</li>
 * 	
 * 	<li><strong> onReverseCompleteParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the <code>onReverseComplete</code> function. For example, 
 * 				 <code>new TimelineMax({onReverseComplete:myFunction, onReverseCompleteParams:["param1", "param2"]});</code>
 * 				 To self-reference the timeline instance itself in one of the parameters, use <code>"{self}"</code>,
 * 				 like: <code>onReverseCompleteParams:["{self}", "param2"]</code></li>
 * 
 *  <li><strong> autoRemoveChildren </strong>:<em> Boolean</em> -
 * 				If <code>autoRemoveChildren</code> is set to <code>true</code>, as soon as child 
 * 				tweens/timelines complete, they will automatically get killed/removed. This is normally 
 * 				undesireable because it prevents going backwards in time (like if you want to 
 * 				<code>reverse()</code> or set the <code>progress</code> lower, etc.). It can, however, 
 * 				improve speed and memory management. The root timelines use <code>autoRemoveChildren:true</code>.</li>
 * 
 *  <li><strong> smoothChildTiming </strong>:<em> Boolean</em> -
 * 				Controls whether or not child tweens/timelines are repositioned automatically 
 * 				(changing their <code>startTime</code>) in order to maintain smooth playback when 
 * 				properties are changed on-the-fly. For example, imagine that the timeline's playhead is 
 * 				on a child tween that is 75% complete, moving mc.x from 0 to 100 and then that tween's 
 * 				<code>reverse()</code> method is called. If <code>smoothChildTiming</code> is <code>false</code> 
 * 				(the default except for the root timelines), the tween would flip in place, keeping its 
 * 				<code>startTime</code> consistent. Therefore the playhead of the timeline would now be 
 * 				at the tween's 25% completion point instead of 75%. Remember, the timeline's playhead 
 * 				position and direction are unaffected by child tween/timeline changes. mc.x would jump 
 * 				from 75 to 25, but the tween's position in the timeline would remain consistent. However, 
 * 				if <code>smoothChildTiming</code> is <code>true</code>, that child tween's 
 * 				<code>startTime</code> would be adjusted so that the timeline's playhead intersects 
 * 				with the same spot on the tween (75% complete) as it had immediately before 
 * 				<code>reverse()</code> was called, thus playback appears perfectly smooth. mc.x 
 * 				would still be 75 and it would continue from there as the playhead moves on, but 
 * 				since the tween is reversed now mc.x will travel back towards 0 instead of 100. 
 * 				Ultimately it's a decision between prioritizing smooth on-the-fly playback 
 * 				(<code>true</code>) or consistent position(s) of child tweens/timelines 
 * 				(<code>false</code>). 
 * 
 * 				Some examples of on-the-fly changes to child tweens/timelines that could cause their 
 * 				<code>startTime</code> to change when <code>smoothChildTiming</code> is <code>true</code> 
 * 				are: <code>reversed, timeScale, progress, totalProgress, time, totalTime, delay, pause, 
 * 				resume, duration,</code> and <code>totalDuration</code>.</li>
 * 	
 *  <li><strong> repeat </strong>:<em> Number</em> -
 * 				 Number of times that the timeline should repeat after its first iteration. For example, 
 * 				 if <code>repeat</code> is 1, the timeline will play a total of twice (the initial play
 * 				 plus 1 repeat). To repeat indefinitely, use -1. <code>repeat</code> should always be an integer.</li>
 * 	
 * 	<li><strong> repeatDelay </strong>:<em> Number</em> -
 * 				 Amount of time in seconds (or frames for frames-based timelines) between repeats. For example,
 * 				 if <code>repeat</code> is 2 and <code>repeatDelay</code> is 1, the timeline will play initially,
 * 				 then wait for 1 second before it repeats, then play again, then wait 1 second again before 
 * 				 doing its final repeat.</li>
 * 	
 * 	<li><strong> yoyo </strong>:<em> Boolean</em> -
 * 				 If <code>true</code>, every other <code>repeat</code> cycle will run in the opposite
 * 				 direction so that the timeline appears to go back and forth (forward then backward).
 * 				 This has no affect on the "<code>reversed</code>" property though. So if <code>repeat</code> 
 * 				 is 2 and <code>yoyo</code> is <code>false</code>, it will look like: 
 * 				 start - 1 - 2 - 3 - 1 - 2 - 3 - 1 - 2 - 3 - end. But if <code>yoyo</code> is <code>true</code>, 
 * 				 it will look like: start - 1 - 2 - 3 - 3 - 2 - 1 - 1 - 2 - 3 - end.</li>
 *  
 * 	<li><strong> onRepeat </strong>:<em> Function</em> -
 * 				 A function that should be called each time the timeline repeats</li>
 * 	
 * 	<li><strong> onRepeatParams </strong>:<em> Array</em> -
 * 				 An Array of parameters to pass the onRepeat function. For example, 
 * 				 <code>new TimelineMax({repeat:3, onRepeat:myFunction, onRepeatParams:[mc, "param2"]});</code>
 * 				 To self-reference the timeline instance itself in one of the parameters, use <code>"{self}"</code>,
 * 				 like: <code>onRepeatParams:["{self}", "param2"]</code></li>
 * 									
 * 	<li><strong> onStartListener </strong>:<em> Function</em> (AS3 only) -
 * 				 A function that should be called (and passed an event parameter) when the timeline begins 
 * 				 (when its <code>totalTime</code> changes from 0 to some other value which can happen more 
 * 				 than once if the timeline is restarted multiple times). Identical to <code>onStart</code> except
 * 				 that the function will always be passed an event parameter whose <code>target</code> property points
 * 				 to the timeline. It's the same as doing <code>myTimeline.addEventListener("start", myFunction);</code>. 
 * 				 Unless you need the event parameter, it's better/faster to use <code>onStart</code>.</li>
 * 	
 * 	<li><strong> onUpdateListener </strong>:<em> Function</em> (AS3 only) -
 * 				 A function that should be called (and passed an event parameter) each time the timeline updates 
 * 				 (on every frame while the timeline is active). Identical to <code>onUpdate</code> except
 * 				 that the function will always be passed an event parameter whose <code>target</code> property points
 * 				 to the timeline. It's the same as doing <code>myTimeline.addEventListener("update", myFunction);</code>. 
 * 				 Unless you need the event parameter, it's better/faster to use <code>onUpdate</code>.</li>
 * 	  
 * 	<li><strong> onCompleteListener </strong>:<em> Function</em> (AS3 only) - 
 * 				 A function that should be called (and passed an event parameter) each time the timeline completes. 
 * 				 Identical to <code>onComplete</code> except that the function will always be passed an event 
 * 				 parameter whose <code>target</code> property points to the timeline. It's the same as doing 
 * 				 <code>myTimeline.addEventListener("complete", myFunction);</code>. 
 * 				 Unless you need the event parameter, it's better/faster to use <code>onComplete</code>.</li>
 * 
 *  <li><strong> onReverseCompleteListener </strong>:<em> Function</em> (AS3 only) -
 * 				 A function that should be called (and passed an event parameter) each time the timeline has reached 
 * 				 its beginning again from the reverse direction. For example, if <code>reverse()</code> is called 
 * 				 the timeline will move back towards its beginning and when its <code>totalTime</code> reaches 0, 
 * 				 <code>onReverseCompleteListener</code> will be called. This can also happen if the timeline is placed 
 * 				 in another TimelineLite or TimelineMax instance that gets reversed and plays the timeline backwards to 
 * 				 (or past) the beginning. Identical to <code>onReverseComplete</code> except that the function 
 * 				 will always be passed an event parameter whose <code>target</code> property points to the timeline. 
 * 				 It's the same as doing <code>myTimeline.addEventListener("reverseComplete", myFunction);</code>. 
 * 				 Unless you need the event parameter, it's better/faster to use <code>onReverseComplete</code>.</li>
 * 
 *  <li><strong> onRepeatListener </strong>:<em> Function</em> (AS3 only) -
 * 				 A function that should be called (and passed an event parameter) each time the timeline repeats. 
 * 				 Identical to <code>onRepeat</code> except that the function will always be passed an event 
 * 				 parameter whose <code>target</code> property points to the timeline. It's the same as doing 
 * 				 <code>myTimeline.addEventListener("repeat", myFunction);</code>. 
 * 				 Unless you need the event parameter, it's better/faster to use <code>onRepeat</code>.</li>
 * 	
 * 	</ul>
 * 
 * @example Sample code:<listing version="3.0">
//create the timeline that repeats 3 times with 1 second between each repeat and then calls myFunction() when it completes
var tl = new TimelineMax({repeat:3, repeatDelay:1, onComplete:myFunction});

//add a tween
tl.add( new TweenLite(mc, 1, {x:200, y:100}) );
		
//add another tween at the end of the timeline (makes sequencing easy)
tl.add( new TweenLite(mc, 0.5, {alpha:0}) );
 
//append a tween using the convenience method (shorter syntax) and offset it by 0.5 seconds
tl.to(mc, 1, {rotation:30}, "+=0.5");
 		
//reverse anytime
tl.reverse();

//Add a "spin" label 3-seconds into the timeline
tl.addLabel("spin", 3);

//insert a rotation tween at the "spin" label (you could also define the insertion point as the time instead of a label)
tl.add( new TweenLite(mc, 2, {rotation:"360"}), "spin");
	
//go to the "spin" label and play the timeline from there
tl.play("spin");

//nest another TimelineMax inside your timeline...
var nested = new TimelineMax();
nested.to(mc2, 1, {x:200}));
tl.add(nested);
</listing>
 * 
 * <p><strong>How do timelines work? What are the mechanics like?</strong></p>
 * <p>Every animation (tween and timeline) is placed on a parent timeline (except the 2 root timelines - there's one for normal tweens and another for "useFrames" ones). 
 * In a sense, they all have their own playheads (that's what its "time" refers to, or "totalTime" which is identical except that it includes repeats and repeatDelays) 
 * but generally they're not independent because they're sitting on a timeline whose playhead moves. 
 * When the parent's playhead moves to a new position, it updates the childrens' too. </p>
 * 
 * <p>When a timeline renders at a particular time, it loops through its children and says "okay, you should render as if your playhead is at ____" and if that child 
 * is a timeline with children, it does the same to its children, right on down the line. </p>
 * 
 * <p>The only exception is when the tween/timeline is paused in which case its internal playhead acts like it's "locked". So in that case, 
 * it's possible (likely in fact) that the child's playhead would <strong>not</strong> be synced with the parent's. 
 * When you unpause it (<code>resume()</code>), it essentially picks it up and moves it so that its internal playhead 
 * is synchronized with wherever the parent's playhead is at that moment, thus things play perfectly smoothly. 
 * That is, unless the timeline's <code>smoothChildTiming</code> is to <code>false</code> in which case it won't move - 
 * its <code>startTime</code> will remain locked to where it was. </p>
 * 
 * <p>So basically, when <code>smoothChildTiming</code> is <code>true</code>, the engine will rearrange things on 
 * the fly to ensure the playheads line up so that playback is seamless and smooth. The same thing happens when you <code>reverse()</code>
 * or alter the <code>timeScale</code>, etc. But sometimes you might not want that behavior - you prefer to have tight 
 * control over exactly where your tweens line up in the timeline - that's when <code>smoothChildTiming:false</code> is handy.</p>
 * 
 * <p>One more example: let's say you've got a 10-second tween that's just sitting on the root timeline and you're 2-seconds into the tween. 
 * Let's assume it started at exactly 0 on the root to make this easy, and then when it's at 2-seconds, you do <code>tween.seek(5)</code>. 
 * The playhead of the root isn't affected - it keeps going exactly as it always did, but in order to make that tween jump to 5 seconds 
 * and play appropriately, the tween's <code>startTime</code> gets changed to -3. That way, the tween's playhead and the root 
 * playhead are perfectly aligned. </p>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 * 
 **/
	public class TimelineMax extends TimelineLite implements IEventDispatcher {
		/** @private **/
		public static const version:String = "12.1.5";
		/** @private **/
		protected static var _listenerLookup:Object = {onCompleteListener:TweenEvent.COMPLETE, onUpdateListener:TweenEvent.UPDATE, onStartListener:TweenEvent.START, onRepeatListener:TweenEvent.REPEAT, onReverseCompleteListener:TweenEvent.REVERSE_COMPLETE};
		/** @private **/
		protected static var _easeNone:Ease = new Ease(null, null, 1, 0);
		
		/** @private **/
		protected var _repeat:int;
		/** @private **/
		protected var _repeatDelay:Number;
		/** @private **/
		protected var _cycle:int = 0;
		/** @private **/
		protected var _locked:Boolean;
		/** @private **/
		protected var _dispatcher:EventDispatcher;
		/** @private **/
		protected var _hasUpdateListener:Boolean;
		
		/** 
		 * @private
		 * Works in conjunction with the repeat property, determining the behavior of each cycle; when <code>yoyo</code> is true, 
		 * the timeline will go back and forth, appearing to reverse every other cycle (this has no affect on the <code>reversed</code> property though). 
		 * So if repeat is 2 and <code>yoyo</code> is false, it will look like: start - 1 - 2 - 3 - 1 - 2 - 3 - 1 - 2 - 3 - end. 
		 * But if repeat is 2 and <code>yoyo</code> is true, it will look like: start - 1 - 2 - 3 - 3 - 2 - 1 - 1 - 2 - 3 - end.  
		 **/
		 protected var _yoyo:Boolean;
		
		/**
		 * Constructor. 
		 * 
		 * <p><strong>SPECIAL PROPERTIES</strong></p>
		 * <p>The following special properties may be passed in via the constructor's vars parameter, like
		 * <code>new TimelineMax({paused:true, onComplete:myFunction, repeat:2, yoyo:true})</code> </p>
		 * 
		 * <ul>
		 * 	<li><strong> delay </strong>:<em> Number</em> -
		 * 				 Amount of delay in seconds (or frames for frames-based tweens) before the timeline should begin.</li>
		 * 
		 *  <li><strong> paused </strong>:<em> Boolean</em> -
		 * 				 If <code>true</code>, the timeline will pause itself immediately upon creation (by default, 
		 * 				 timelines automatically begin playing immediately). If you plan to create a TimelineMax and 
		 * 				 then populate it later (after one or more frames elapse), it is typically best to set 
		 * 				 <code>paused:true</code> and then <code>play()</code> after you populate it.</li>
		 * 	
		 * 	<li><strong> onComplete </strong>:<em> Function</em> -
		 * 				 A function that should be called when the timeline has completed</li>
		 * 	
		 * 	<li><strong> onCompleteParams </strong>:<em> Array</em> -
		 * 				 An Array of parameters to pass the <code>onComplete</code> function. For example,
		 * 				 <code>new TimelineMax({onComplete:myFunction, onCompleteParams:["param1", "param2"]});</code></li>
		 * 	
		 * 	<li><strong> useFrames </strong>:<em> Boolean</em> -
		 * 				 If <code>useFrames</code> is <code>true</code>, the timelines's timing will be 
		 * 				 based on frames instead of seconds because it is intially added to the root
		 * 				 frames-based timeline. This causes both its <code>duration</code>
		 * 				 and <code>delay</code> to be based on frames. An animations's timing mode is 
		 * 				 always determined by its parent <code>timeline</code>.</li>
		 * 
		 *  <li><strong> tweens </strong>:<em> Array</em> -
		 * 				 To immediately insert several tweens into the timeline, use the <code>tweens</code> 
		 * 				 special property to pass in an Array of TweenLite/TweenMax/TimelineLite/TimelineMax 
		 * 				 instances. You can use this in conjunction with the <code>align</code> and 
		 * 				 <code>stagger</code> special properties to set up complex sequences with minimal code.
		 * 				 These values simply get passed to the <code>add()</code> method.</li>
		 * 	
		 * 	<li><strong> align </strong>:<em> String</em> -
		 * 				 Only used in conjunction with the <code>tweens</code> special property when multiple 
		 * 				 tweens are	to be inserted immediately. The value simply gets passed to the 
		 * 				 <code>add()</code> method. The default is <code>"normal"</code>. 
		 * 				 Options are:
		 * 					<ul>
		 * 						<li><strong><code>"sequence"</code></strong>: aligns the tweens one-after-the-other in a sequence</li>
		 * 						<li><strong><code>"start"</code></strong>: aligns the start times of all of the tweens (ignores delays)</li>
		 * 						<li><strong><code>"normal"</code></strong>: aligns the start times of all the tweens (honors delays)</li>
		 * 					</ul>
		 * 				The <code>align</code> special property does <strong>not</strong> force all child 
		 * 				tweens/timelines to maintain relative positioning, so for example, if you use 
		 * 				<code>"sequence"</code> and then later change the duration of one of the nested tweens, 
		 * 				it does <strong>not</strong> force all subsequent timelines to change their position.
		 * 				The <code>align</code> special property only affects the alignment of the tweens that are
		 * 				initially placed into the timeline through the <code>tweens</code> special property of 
		 * 				the <code>vars</code> object.</li>
		 * 										
		 * 	<li><strong> stagger </strong>:<em> Number</em> -
		 * 				 Only used in conjunction with the <code>tweens</code> special property when multiple 
		 * 				 tweens are	to be inserted immediately. It staggers the tweens by a set amount of time 
		 * 				 in seconds (or in frames if <code>useFrames</code> is true). For example, if the 
		 * 				 stagger value is 0.5 and the "align" property is set to <code>"start"</code>, the 
		 * 				 second tween will start 0.5 seconds after the first one starts, then 0.5 seconds 
		 * 				 later the third one will start, etc. If the align property is <code>"sequence"</code>,
		 * 				 there would be 0.5 seconds added between each tween. This value simply gets 
		 * 				 passed to the <code>add()</code> method. Default is 0.</li>
		 * 
		 *  <li><strong> onStart </strong>:<em> Function</em> -
		 * 				 A function that should be called when the timeline begins (when its <code>time</code>
		 * 				 changes from 0 to some other value which can happen more than once if the 
		 * 				 timeline is restarted multiple times).</li>
		 * 	
		 * 	<li><strong> onStartParams </strong>:<em> Array</em> -
		 * 				 An Array of parameters to pass the <code>onStart</code> function. For example, 
		 * 				 <code>new TimelineMax({onStart:myFunction, onStartParams:["param1", "param2"]});</code></li>
		 * 	
		 * 	<li><strong> onUpdate </strong>:<em> Function</em> -
		 * 				 A function that should be called every time the timeline updates  
		 * 				 (on every frame while the timeline is active)</li>
		 * 	
		 * 	<li><strong> onUpdateParams </strong>:<em> Array</em> -
		 * 				 An Array of parameters to pass the <code>onUpdate</code> function. For example,
		 * 				 <code>new TimelineMax({onUpdate:myFunction, onUpdateParams:["param1", "param2"]});</code></li>
		 * 	
		 * 	<li><strong> onReverseComplete </strong>:<em> Function</em> -
		 * 				 A function that should be called when the timeline has reached its beginning again from the 
		 * 				 reverse direction. For example, if <code>reverse()</code> is called, the timeline will move
		 * 				 back towards its beginning and when its <code>time</code> reaches 0, <code>onReverseComplete</code>
		 * 				 will be called. This can also happen if the timeline is placed in a TimelineLite or TimelineMax 
		 * 				 instance that gets reversed and plays the timeline backwards to (or past) the beginning.</li>
		 * 	
		 * 	<li><strong> onReverseCompleteParams </strong>:<em> Array</em> -
		 * 				 An Array of parameters to pass the <code>onReverseComplete</code> function. For example, 
		 * 				 <code>new TimelineMax({onReverseComplete:myFunction, onReverseCompleteParams:["param1", "param2"]});</code></li>
		 * 
		 *  <li><strong> autoRemoveChildren </strong>:<em> Boolean</em> -
		 * 				If <code>autoRemoveChildren</code> is set to <code>true</code>, as soon as child 
		 * 				tweens/timelines complete, they will automatically get killed/removed. This is normally 
		 * 				undesireable because it prevents going backwards in time (like if you want to 
		 * 				<code>reverse()</code> or set the <code>progress</code> lower, etc.). It can, however, 
		 * 				improve speed and memory management. The root timelines use <code>autoRemoveChildren:true</code>.</li>
		 * 
		 *  <li><strong> smoothChildTiming </strong>:<em> Boolean</em> -
		 * 				Controls whether or not child tweens/timelines are repositioned automatically 
		 * 				(changing their <code>startTime</code>) in order to maintain smooth playback when 
		 * 				properties are changed on-the-fly. For example, imagine that the timeline's playhead is 
		 * 				on a child tween that is 75% complete, moving mc.x from 0 to 100 and then that tween's 
		 * 				<code>reverse()</code> method is called. If <code>smoothChildTiming</code> is <code>false</code> 
		 * 				(the default except for the root timelines), the tween would flip in place, keeping its 
		 * 				<code>startTime</code> consistent. Therefore the playhead of the timeline would now be 
		 * 				at the tween's 25% completion point instead of 75%. Remember, the timeline's playhead 
		 * 				position and direction are unaffected by child tween/timeline changes. mc.x would jump 
		 * 				from 75 to 25, but the tween's position in the timeline would remain consistent. However, 
		 * 				if <code>smoothChildTiming</code> is <code>true</code>, that child tween's 
		 * 				<code>startTime</code> would be adjusted so that the timeline's playhead intersects 
		 * 				with the same spot on the tween (75% complete) as it had immediately before 
		 * 				<code>reverse()</code> was called, thus playback appears perfectly smooth. mc.x 
		 * 				would still be 75 and it would continue from there as the playhead moves on, but 
		 * 				since the tween is reversed now mc.x will travel back towards 0 instead of 100. 
		 * 				Ultimately it's a decision between prioritizing smooth on-the-fly playback 
		 * 				(<code>true</code>) or consistent position(s) of child tweens/timelines 
		 * 				(<code>false</code>). 
		 * 
		 * 				Some examples of on-the-fly changes to child tweens/timelines that could cause their 
		 * 				<code>startTime</code> to change when <code>smoothChildTiming</code> is <code>true</code> 
		 * 				are: <code>reversed, timeScale, progress, totalProgress, time, totalTime, delay, pause, 
		 * 				resume, duration,</code> and <code>totalDuration</code>.</li>
		 * 	
		 *  <li><strong> repeat </strong>:<em> Number</em> -
		 * 				 Number of times that the timeline should repeat after its first iteration. For example, 
		 * 				 if <code>repeat</code> is 1, the timeline will play a total of twice (the initial play
		 * 				 plus 1 repeat). To repeat indefinitely, use -1. <code>repeat</code> should always be an integer.</li>
		 * 	
		 * 	<li><strong> repeatDelay </strong>:<em> Number</em> -
		 * 				 Amount of time in seconds (or frames for frames-based timelines) between repeats. For example,
		 * 				 if <code>repeat</code> is 2 and <code>repeatDelay</code> is 1, the timeline will play initially,
		 * 				 then wait for 1 second before it repeats, then play again, then wait 1 second again before 
		 * 				 doing its final repeat.</li>
		 * 	
		 * 	<li><strong> yoyo </strong>:<em> Boolean</em> -
		 * 				 If <code>true</code>, every other <code>repeat</code> cycle will run in the opposite
		 * 				 direction so that the timeline appears to go back and forth (forward then backward).
		 * 				 This has no affect on the "<code>reversed</code>" property though. So if <code>repeat</code> 
		 * 				 is 2 and <code>yoyo</code> is <code>false</code>, it will look like: 
		 * 				 start - 1 - 2 - 3 - 1 - 2 - 3 - 1 - 2 - 3 - end. But if <code>yoyo</code> is <code>true</code>, 
		 * 				 it will look like: start - 1 - 2 - 3 - 3 - 2 - 1 - 1 - 2 - 3 - end.</li>
		 *  
		 * 	<li><strong> onRepeat </strong>:<em> Function</em> -
		 * 				 A function that should be called each time the timeline repeats</li>
		 * 	
		 * 	<li><strong> onRepeatParams </strong>:<em> Array</em> -
		 * 				 An Array of parameters to pass the onRepeat function. For example, 
		 * 				 <code>new TimelineMax({repeat:3, onRepeat:myFunction, onRepeatParams:[mc, "param2"]});</code></li>
		 * 									
		 * 	<li><strong> onStartListener </strong>:<em> Function</em> -
		 * 				 A function that should be called (and passed an event parameter) when the timeline begins 
		 * 				 (when its <code>totalTime</code> changes from 0 to some other value which can happen more 
		 * 				 than once if the timeline is restarted multiple times). Identical to <code>onStart</code> except
		 * 				 that the function will always be passed an event parameter whose <code>target</code> property points
		 * 				 to the timeline. It's the same as doing <code>myTimeline.addEventListener("start", myFunction);</code>. 
		 * 				 Unless you need the event parameter, it's better/faster to use <code>onStart</code>.</li>
		 * 	
		 * 	<li><strong> onUpdateListener </strong>:<em> Function</em> -
		 * 				 A function that should be called (and passed an event parameter) each time the timeline updates 
		 * 				 (on every frame while the timeline is active). Identical to <code>onUpdate</code> except
		 * 				 that the function will always be passed an event parameter whose <code>target</code> property points
		 * 				 to the timeline. It's the same as doing <code>myTimeline.addEventListener("update", myFunction);</code>. 
		 * 				 Unless you need the event parameter, it's better/faster to use <code>onUpdate</code>.</li>
		 * 	  
		 * 	<li><strong> onCompleteListener </strong>:<em> Function</em> - 
		 * 				 A function that should be called (and passed an event parameter) each time the timeline completes. 
		 * 				 Identical to <code>onComplete</code> except that the function will always be passed an event 
		 * 				 parameter whose <code>target</code> property points to the timeline. It's the same as doing 
		 * 				 <code>myTimeline.addEventListener("complete", myFunction);</code>. 
		 * 				 Unless you need the event parameter, it's better/faster to use <code>onComplete</code>.</li>
		 * 
		 *  <li><strong> onReverseCompleteListener </strong>:<em> Function</em> -
		 * 				 A function that should be called (and passed an event parameter) each time the timeline has reached 
		 * 				 its beginning again from the reverse direction. For example, if <code>reverse()</code> is called 
		 * 				 the timeline will move back towards its beginning and when its <code>totalTime</code> reaches 0, 
		 * 				 <code>onReverseCompleteListener</code> will be called. This can also happen if the timeline is placed 
		 * 				 in another TimelineLite or TimelineMax instance that gets reversed and plays the timeline backwards to 
		 * 				 (or past) the beginning. Identical to <code>onReverseComplete</code> except that the function 
		 * 				 will always be passed an event parameter whose <code>target</code> property points to the timeline. 
		 * 				 It's the same as doing <code>myTimeline.addEventListener("reverseComplete", myFunction);</code>. 
		 * 				 Unless you need the event parameter, it's better/faster to use <code>onReverseComplete</code>.</li>
		 * 
		 *  <li><strong> onRepeatListener </strong>:<em> Function</em> -
		 * 				 A function that should be called (and passed an event parameter) each time the timeline repeats. 
		 * 				 Identical to <code>onRepeat</code> except that the function will always be passed an event 
		 * 				 parameter whose <code>target</code> property points to the timeline. It's the same as doing 
		 * 				 <code>myTimeline.addEventListener("repeat", myFunction);</code>. 
		 * 				 Unless you need the event parameter, it's better/faster to use <code>onRepeat</code>.</li>
		 * 	
		 * 	</ul>
		 * 
		 * @param vars optionally pass in special properties like useFrames, onComplete, onCompleteParams, onUpdate, onUpdateParams, onStart, onStartParams, tweens, align, stagger, delay, autoRemoveChildren, onCompleteListener, onStartListener, onUpdateListener, repeat, repeatDelay, and/or yoyo.
		 */
		public function TimelineMax(vars:Object=null) {
			super(vars);
			_repeat = this.vars.repeat || 0;
			_repeatDelay = this.vars.repeatDelay || 0;
			_yoyo = (this.vars.yoyo == true);
			_dirty = true;
			if (this.vars.onCompleteListener || this.vars.onUpdateListener || this.vars.onStartListener || this.vars.onRepeatListener || this.vars.onReverseCompleteListener) {
				_initDispatcher();
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
		 * Inserts a callback at a particular position. The callback is technically considered a 
		 * zero-duration tween, so if you <code>getChildren()</code> there will be a tween returned for each callback. 
		 * You can discern a callback from other tweens by the fact that its target is a function matching
		 * its <code>vars.onComplete</code> and its <code>duration</code> is zero. 
		 * 
		 * <p>If your goal is to append the callback to the end of the timeline, it would be easier
		 * (more concise) to use the <code>call()</code> method. Technically the <code>add()</code> method
		 * can accommodate adding a callback too (like <code>myTimeline.add(myFunction, 2)</code>
		 * or <code>myTimeline.add(myFunction, "+=2")</code>) but <code>add()</code> doesn't accommodate parameters.</p>
		 * 
		 * <p><strong>JavaScript and AS2 note:</strong> - Due to the way JavaScript and AS2 don't 
		 * maintain scope (what "<code>this</code>" refers to, or the context) in function calls, 
		 * it can be useful to define the scope specifically. Therefore, in the JavaScript and AS2 
		 * versions accept an extra (4th) parameter for <code>scope</code>.</p>
		 * 
		 * @param function The function to be called
		 * @param position The time in seconds (or frames for frames-based timelines) or label at which the callback should be inserted. For example, <code>myTimeline.addCallback(myFunction, 3)</code> would call myFunction() 3 seconds into the timeline, and <code>myTimeline.addCallback(myFunction, "myLabel")</code> would call it at the "myLabel" label. <code>myTimeline.addCallback(myFunction, "+=2")</code> would insert the callback 2 seconds after the end of the timeline.
		 * @param params An Array of parameters to pass the callback
		 * @return self (makes chaining easier)
		 * 
		 * @see #call()
		 * @see #add()
		 * @see #removeCallback()
		 */
		public function addCallback(callback:Function, position:*, params:Array=null):TimelineMax {
			return add( TweenLite.delayedCall(0, callback, params), position) as TimelineMax;
		}
		
		/**
		 * Removes a callback. If the <code>position</code> parameter
		 * is null, all callbacks of that function are removed from the timeline.
		 * 
		 * @param function callback function to be removed
		 * @param position the time in seconds (or frames for frames-based timelines) or label from which the callback should be removed. For example, <code>myTimeline.removeCallback(myFunction, 3)</code> would remove the callback from 3-seconds into the timeline, and <code>myTimeline.removeCallback(myFunction, "myLabel")</code> would remove it from the "myLabel" label, and <code>myTimeline.removeCallback(myFunction, null)</code> would remove ALL callbacks of that function regardless of where they are on the timeline.
		 * @return self (makes chaining easier)
		 * 
		 * @see #addCallback()
		 * @see #call()
		 * @see #kill()
		 */
		public function removeCallback(callback:Function, position:*=null):TimelineMax {
			if (callback != null) {
				if (position == null) {
					_kill(null, callback);
				} else {
					var a:Array = getTweensOf(callback, false),
						i:int = a.length,
						time:Number = _parseTimeOrLabel(position);
					while (--i > -1) {
						if (a[i]._startTime === time) {
							a[i]._enabled(false, false);
						}
					}
				}
			}
			return this;
		}
		
		/**
		 * Creates a linear tween that essentially scrubs the playhead to a particular time or label and 
		 * then stops. For example, to make the TimelineMax play to the "myLabel2" label, simply do: 
		 * 
		 * <p><code>
		 * myTimeline.tweenTo("myLabel2"); 
		 * </code></p>
		 * 
		 * <p>If you want advanced control over the tween, like adding an onComplete or changing the ease or 
		 * adding a delay, just pass in a <code>vars</code> object with the appropriate properties. For example, 
		 * to tween to the 5-second point on the timeline and then call a function named <code>myFunction</code> 
		 * and pass in a parameter that's references this TimelineMax and use a <code>Strong.easeOut</code> ease, you'd do:</p>
		 * 
		 * <p><code>
		 * myTimeline.tweenTo(5, {onComplete:myFunction, onCompleteParams:[myTimeline], ease:Strong.easeOut});
		 * </code></p>
		 * 
		 * <p>Remember, this method simply creates a TweenLite instance that pauses the timeline and then tweens 
		 * the <code>time()</code> of the timeline. So you can store a reference to that tween if you want, and 
		 * you can kill() it anytime. Also note that <code>tweenTo()</code> does <b>NOT</b> affect the timeline's 
		 * <code>reversed</code> state. So if your timeline is oriented normally (not reversed) and you tween to 
		 * a time/label that precedes the current time, it will appear to go backwards but the <code>reversed</code> 
		 * state will <b>not</b> change to <code>true</code>. Also note that <code>tweenTo()</code>
		 * pauses the timeline immediately before tweening its <code>time()</code>, and it does not automatically
		 * resume after the tween completes. If you need to resume playback, you could always use an onComplete 
		 * to call the timeline's <code>resume()</code> method.</p>
		 * 
		 * <p>If you plan to sequence multiple playhead tweens one-after-the-other, it is typically better to use 
		 * <code>tweenFromTo()</code> so that you can define the starting point and ending point, allowing the 
		 * duration to be accurately determined immediately.</p>
		 * 
		 * @param position The destination time in seconds (or frame if the timeline is frames-based) or label to which the timeline should play. For example, <code>myTimeline.tweenTo(5)</code> would play from wherever the timeline is currently to the 5-second point whereas <code>myTimeline.tweenTo("myLabel")</code> would play to wherever "myLabel" is on the timeline.
		 * @param vars An optional vars object that will be passed to the TweenLite instance. This allows you to define an onComplete, ease, delay, or any other TweenLite special property.
		 * @return A TweenLite instance that handles tweening the timeline to the desired time/label.
		 * 
		 * @see #tweenFromTo()
		 * @see #seek()
		 */
		public function tweenTo(position:*, vars:Object=null):TweenLite {
			vars = vars || {};
			var copy:Object = {ease:_easeNone, overwrite:(vars.delay ? 2 : 1), useFrames:usesFrames(), immediateRender:false};
			for (var p:String in vars) {
				copy[p] = vars[p];
			}
			copy.time = _parseTimeOrLabel(position);
			var duration:Number = (Math.abs(Number(copy.time) - _time) / _timeScale) || 0.001;
			var t:TweenLite = new TweenLite(this, duration, copy);
			copy.onStart = function():void {
				t.target.paused(true);
				if (t.vars.time != t.target.time() && duration === t.duration()) { //don't make the duration zero - if it's supposed to be zero, don't worry because it's already initting the tween and will complete immediately, effectively making the duration zero anyway. If we make duration zero, the tween won't run at all.
					t.duration( Math.abs( t.vars.time - t.target.time()) / t.target._timeScale );
				}
				if (vars.onStart) { //in case the user had an onStart in the vars - we don't want to overwrite it.
					vars.onStart.apply(null, vars.onStartParams);
				}
			}
			return t;
		}
		
		/**
		 * Creates a linear tween that essentially scrubs the playhead from a particular time or label 
		 * to another time or label and then stops. If you plan to sequence multiple playhead tweens 
		 * one-after-the-other, <code>tweenFromTo()</code> is better to use than <code>tweenTo()</code> 
		 * because it allows the duration to be determined immediately, ensuring that subsequent tweens 
		 * that are appended to a sequence are positioned appropriately. For example, to make the 
		 * TimelineMax play from the label "myLabel1" to the "myLabel2" label, and then from "myLabel2" 
		 * back to the beginning (a time of 0), simply do:
		 * 
		 * <listing version="3.0">
var tl:TimelineMax = new TimelineMax(); 
tl.add( myTimeline.tweenFromTo("myLabel1", "myLabel2") );
tl.add( myTimeline.tweenFromTo("myLabel2", 0) );
</listing>
		 * 
		 * <p>If you want advanced control over the tween, like adding an onComplete or changing the ease 
		 * or adding a delay, just pass in a vars object with the appropriate properties. For example, 
		 * to tween from the start (0) to the 5-second point on the timeline and then call a function 
		 * named <code>myFunction</code> and pass in a parameter that references this TimelineMax and 
		 * use a <code>Strong.easeOut</code> ease, you'd do: </p>
		 * 
		 * <p><code>
		 * myTimeline.tweenFromTo(0, 5, {onComplete:myFunction, onCompleteParams:[myTimeline], ease:Strong.easeOut});
		 * </code></p>
		 * 
		 * <p>Remember, this method simply creates a TweenLite instance that tweens the <code>time()</code> 
		 * of your timeline. So you can store a reference to that tween if you want, and you can <code>kill()</code> 
		 * it anytime. Also note that <code>tweenFromTo()</code> does <b>NOT</b> affect the timeline's 
		 * <code>reversed</code> property. So if your timeline is oriented normally (not reversed) and you
		 * tween to a time/label that precedes the current time, it will appear to go backwards but the 
		 * <code>reversed</code> property will <b>not</b> change to <code>true</code>. Also note that 
		 * <code>tweenFromTo()</code> pauses the timeline immediately before tweening its <code>time()</code>, 
		 * and it does not automatically resume after the tween completes. If you need to resume playback, 
		 * you can always use an onComplete to call the <code>resume()</code> method.</p>
		 * 
		 * <p>Like all from-type methods in GSAP, <code>immediateRender</code> is <code>true</code> by default,
		 * meaning the timeline will immediately jump to the "from" time/label unless you set <code>immediateRender:false</code></p>
		 * 
		 * @param fromPosition The beginning time in seconds (or frame if the timeline is frames-based) or label from which the timeline should play. For example, <code>myTimeline.tweenTo(0, 5)</code> would play from 0 (the beginning) to the 5-second point whereas <code>myTimeline.tweenFromTo("myLabel1", "myLabel2")</code> would play from "myLabel1" to "myLabel2".
		 * @param toPosition The destination time in seconds (or frame if the timeline is frames-based) or label to which the timeline should play. For example, <code>myTimeline.tweenTo(0, 5)</code> would play from 0 (the beginning) to the 5-second point whereas <code>myTimeline.tweenFromTo("myLabel1", "myLabel2")</code> would play from "myLabel1" to "myLabel2".
		 * @param vars An optional vars object that will be passed to the TweenLite instance. This allows you to define an onComplete, ease, delay, or any other TweenLite special property. onInit is the only special property that is not available (<code>tweenFromTo()</code> sets it internally)
		 * @return TweenLite instance that handles tweening the timeline between the desired times/labels.
		 * 
		 * @see #tweenTo()
		 * @see #seek()
		 */
		public function tweenFromTo(fromPosition:*, toPosition:*, vars:Object=null):TweenLite {
			vars = vars || {};
			fromPosition = _parseTimeOrLabel(fromPosition);
			vars.startAt = {onComplete:seek, onCompleteParams:[fromPosition]};
			vars.immediateRender = (vars.immediateRender !== false);
			var t:TweenLite = tweenTo(toPosition, vars);
			return t.duration((Math.abs( t.vars.time - fromPosition) / _timeScale) || 0.001) as TweenLite;
		}
		
		
		/** @private **/
		override public function render(time:Number, suppressEvents:Boolean=false, force:Boolean=false):void {
			if (_gc) {
				_enabled(true, false);
			}
			var totalDur:Number = (!_dirty) ? _totalDuration : totalDuration(), 
				prevTime:Number = _time, 
				prevTotalTime:Number = _totalTime, 
				prevStart:Number = _startTime, 
				prevTimeScale:Number = _timeScale, 
				prevRawPrevTime:Number = _rawPrevTime,
				prevPaused:Boolean = _paused, 
				prevCycle:int = _cycle, 
				tween:Animation, isComplete:Boolean, next:Animation, dur:Number, callback:String, internalForce:Boolean;

			if (time >= totalDur) {
				if (!_locked) {
					_totalTime = totalDur;
					_cycle = _repeat;
				}
				if (!_reversed) if (!_hasPausedChild()) {
					isComplete = true;
					callback = "onComplete";
					if (_duration === 0) if (time === 0 || _rawPrevTime < 0 || _rawPrevTime === _tinyNum) if (_rawPrevTime !== time && _first != null) {
						internalForce = true;
						if (_rawPrevTime > _tinyNum) {
							callback = "onReverseComplete";
						}
					}
				}
				_rawPrevTime = (_duration || !suppressEvents || time !== 0 || _rawPrevTime === time) ? time : _tinyNum; //when the playhead arrives at EXACTLY time 0 (right on top) of a zero-duration timeline or tween, we need to discern if events are suppressed so that when the playhead moves again (next time), it'll trigger the callback. If events are NOT suppressed, obviously the callback would be triggered in this render. Basically, the callback should fire either when the playhead ARRIVES or LEAVES this exact spot, not both. Imagine doing a timeline.seek(0) and there's a callback that sits at 0. Since events are suppressed on that seek() by default, nothing will fire, but when the playhead moves off of that position, the callback should fire. This behavior is what people intuitively expect. We set the _rawPrevTime to be a precise tiny number to indicate this scenario rather than using another property/variable which would increase memory usage. This technique is less readable, but more efficient.
				if (_yoyo && (_cycle & 1) != 0) {
					_time = time = 0;
				} else {
					_time = _duration;
					time = _duration + 0.0001; //to avoid occasional floating point rounding errors in Flash - sometimes child tweens/timelines were not being fully completed (their progress might be 0.999999999999998 instead of 1 because when Flash performed _time - tween._startTime, floating point errors would return a value that was SLIGHTLY off)
				}
				
			} else if (time < 0.0000001) { //to work around occasional floating point math artifacts, round super small values to 0. 
				if (!_locked) {
					_totalTime = _cycle = 0;
				}
				_time = 0;
				if (prevTime !== 0 || (_duration === 0 && _rawPrevTime !== _tinyNum && (_rawPrevTime > 0 || (time < 0 && _rawPrevTime >= 0)) && !_locked)) {
					callback = "onReverseComplete";
					isComplete = _reversed;
				}
				if (time < 0) {
					_active = false;
					if (_rawPrevTime >= 0 && _first) { //zero-duration timelines are tricky because we must discern the momentum/direction of time in order to determine whether the starting values should be rendered or the ending values. If the "playhead" of its timeline goes past the zero-duration tween in the forward direction or lands directly on it, the end values should be rendered, but if the timeline's "playhead" moves past it in the backward direction (from a postitive time to a negative time), the starting values must be rendered.
						internalForce = true;
					}
					_rawPrevTime = time;			
				} else {
					_rawPrevTime = (_duration || !suppressEvents || time !== 0 || _rawPrevTime === time) ? time : _tinyNum; //when the playhead arrives at EXACTLY time 0 (right on top) of a zero-duration timeline or tween, we need to discern if events are suppressed so that when the playhead moves again (next time), it'll trigger the callback. If events are NOT suppressed, obviously the callback would be triggered in this render. Basically, the callback should fire either when the playhead ARRIVES or LEAVES this exact spot, not both. Imagine doing a timeline.seek(0) and there's a callback that sits at 0. Since events are suppressed on that seek() by default, nothing will fire, but when the playhead moves off of that position, the callback should fire. This behavior is what people intuitively expect. We set the _rawPrevTime to be a precise tiny number to indicate this scenario rather than using another property/variable which would increase memory usage. This technique is less readable, but more efficient.
					time = 0; //to avoid occasional floating point rounding errors (could cause problems especially with zero-duration tweens at the very beginning of the timeline)
					if (!_initted) {
						internalForce = true;
					}
				}
				
			} else {
				if (_duration === 0 && _rawPrevTime < 0) { //without this, zero-duration repeating timelines (like with a simple callback nested at the very beginning and a repeatDelay) wouldn't render the first time through.
					internalForce = true;
				}
				_time = _rawPrevTime = time;
				if (!_locked) {
					_totalTime = time;
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
							time = _duration + 0.0001; //to avoid occasional floating point rounding errors in Flash - sometimes child tweens/timelines were not being fully completed (their progress might be 0.999999999999998 instead of 1 because when Flash performed _time - tween._startTime, floating point errors would return a value that was SLIGHTLY off)
						} else if (_time < 0) {
							_time = time = 0;
						} else {
							time = _time;
						}
					}
				}
			}
			
			if (_cycle != prevCycle) if (!_locked) {
				/*
				make sure children at the end/beginning of the timeline are rendered properly. If, for example, 
				a 3-second long timeline rendered at 2.9 seconds previously, and now renders at 3.2 seconds (which
				would get transated to 2.8 seconds if the timeline yoyos or 0.2 seconds if it just repeats), there
				could be a callback or a short tween that's at 2.95 or 3 seconds in which wouldn't render. So 
				we need to push the timeline to the end (and/or beginning depending on its yoyo value). Also we must
				ensure that zero-duration tweens at the very beginning or end of the TimelineMax work. 
				*/
				var backwards:Boolean = (_yoyo && (prevCycle & 1) !== 0),
					wrap:Boolean = (backwards == (_yoyo && (_cycle & 1) !== 0)),
					recTotalTime:Number = _totalTime,
					recCycle:int = _cycle,
					recRawPrevTime:Number = _rawPrevTime,
					recTime:Number = _time;
				
				_totalTime = prevCycle * _duration;
				if (_cycle < prevCycle) {
					backwards = !backwards;
				} else {
					_totalTime += _duration;
				}
				_time = prevTime; //temporarily revert _time so that render() renders the children in the correct order. Without this, tweens won't rewind correctly. We could arhictect things in a "cleaner" way by splitting out the rendering queue into a separate method but for performance reasons, we kept it all inside this method.
				
				_rawPrevTime = prevRawPrevTime;
				_cycle = prevCycle;
				_locked = true; //prevents changes to totalTime and skips repeat/yoyo behavior when we recursively call render()
				prevTime = (backwards) ? 0 : _duration;	
				render(prevTime, suppressEvents, false);
				if (!suppressEvents) if (!_gc) {
					if (vars.onRepeat) {
						vars.onRepeat.apply(null, vars.onRepeatParams);
					}
					if (_dispatcher) {
						_dispatcher.dispatchEvent(new TweenEvent(TweenEvent.REPEAT));
					}
				}
				if (wrap) {
					prevTime = (backwards) ? _duration + 0.0001 : -0.0001;
					render(prevTime, true, false);
				}
				_locked = false;
				if (_paused && !prevPaused) { //if the render() triggered callback that paused this timeline, we should abort (very rare, but possible)
					return;
				}
				_time = recTime;
				_totalTime = recTotalTime;
				_cycle = recCycle;
				_rawPrevTime = recRawPrevTime;
			}

			if ((_time == prevTime || !_first) && !force && !internalForce) {
				if (prevTotalTime !== _totalTime) if (_onUpdate != null) if (!suppressEvents) { //so that onUpdate fires even during the repeatDelay - as long as the totalTime changed, we should trigger onUpdate.
					_onUpdate.apply(vars.onUpdateScope || this, vars.onUpdateParams);
				}
				return;
			} else if (!_initted) {
				_initted = true;
			}
			
			if (!_active) if (!_paused && _totalTime !== prevTotalTime && time > 0) {
				_active = true;  //so that if the user renders the timeline (as opposed to the parent timeline rendering it), it is forced to re-render and align it with the proper time/frame on the next rendering cycle. Maybe the timeline already finished but the user manually re-renders it as halfway done, for example.
			}
			
			if (prevTotalTime == 0) if (_totalTime != 0) if (!suppressEvents) {
				if (vars.onStart) {
					vars.onStart.apply(this, vars.onStartParams);
				}
				if (_dispatcher) {
					_dispatcher.dispatchEvent(new TweenEvent(TweenEvent.START));
				}
			}
			
			if (_time >= prevTime) {
				tween = _first;
				while (tween) {
					next = tween._next; //record it here because the value could change after rendering...
					if (_paused && !prevPaused) { //in case a tween pauses the timeline when rendering
						break;
					} else if (tween._active || (tween._startTime <= _time && !tween._paused && !tween._gc)) {
						
						if (!tween._reversed) {
							tween.render((time - tween._startTime) * tween._timeScale, suppressEvents, force);
						} else {
							tween.render(((!tween._dirty) ? tween._totalDuration : tween.totalDuration()) - ((time - tween._startTime) * tween._timeScale), suppressEvents, force);
						}
						
					}
					tween = next;
				}
			} else {
				tween = _last;
				while (tween) {
					next = tween._prev; //record it here because the value could change after rendering...
					if (_paused && !prevPaused) { //in case a tween pauses the timeline when rendering
						break;
					} else if (tween._active || (tween._startTime <= prevTime && !tween._paused && !tween._gc)) {
						
						if (!tween._reversed) {
							tween.render((time - tween._startTime) * tween._timeScale, suppressEvents, force);
						} else {
							tween.render(((!tween._dirty) ? tween._totalDuration : tween.totalDuration()) - ((time - tween._startTime) * tween._timeScale), suppressEvents, force);
						}
						
					}
					tween = next;
				}
			}
			
			if (_onUpdate != null) if (!suppressEvents) {
				_onUpdate.apply(null, vars.onUpdateParams);
			}
			if (_hasUpdateListener) if (!suppressEvents) {
				_dispatcher.dispatchEvent(new TweenEvent(TweenEvent.UPDATE));
			}
			
			if (callback) if (!_locked) if (!_gc) if (prevStart === _startTime || prevTimeScale !== _timeScale) if (_time === 0 || totalDur >= totalDuration()) { //if one of the tweens that was rendered altered this timeline's startTime (like if an onComplete reversed the timeline), it probably isn't complete. If it is, don't worry, because whatever call altered the startTime would complete if it was necessary at the new time. The only exception is the timeScale property. Also check _gc because there's a chance that kill() could be called in an onUpdate
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
			}
		}
		
		/**
		 * Returns the tweens/timelines that are currently active in the timeline, meaning the timeline's
		 * playhead is positioned on the child tween/timeline and the child isn't paused.
		 * 
		 * @param nested Determines whether or not tweens and/or timelines that are inside nested timelines should be returned. If you only want the "top level" tweens/timelines, set this to <code>false</code>.
		 * @param tweens Determines whether or not tweens (TweenLite and TweenMax instances) should be included in the results
		 * @param timelines Determines whether or not child timelines (TimelineLite and TimelineMax instances) should be included in the results
		 * @return an Array of active tweens/timelines
		 */
		public function getActive(nested:Boolean=true, tweens:Boolean=true, timelines:Boolean=false):Array {
			var a:Array = [], 
				all:Array = getChildren(nested, tweens, timelines), 
				cnt:int = 0, 
				l:int = all.length,
				i:int, tween:Animation;
			for (i = 0; i < l; i++) {
				tween = all[i];
				//note: we cannot just check tween.active because timelines that contain paused children will continue to have "active" set to true even after the playhead passes their end point (technically a timeline can only be considered complete after all of its children have completed too, but paused tweens are...well...just waiting and until they're unpaused we don't know where their end point will be).
				if (!tween._paused) if (tween._timeline._time >= tween._startTime) if (tween._timeline._time < tween._startTime + tween._totalDuration / tween._timeScale) if (!_getGlobalPaused(tween._timeline)) {
					a[cnt++] = tween;
				}
			}
			return a;
		}
		
		/** @private **/
		protected static function _getGlobalPaused(tween:Animation):Boolean {
			while (tween) {
				if (tween._paused) {
					return true;
				}
				tween = tween._timeline;
			}
			return false;
		}
		
		/**
		 * Returns the next label (if any) that occurs <strong>after</strong> the <code>time</code> parameter. 
		 * It makes no difference if the timeline is reversed ("after" means later in the timeline's local time zone). 
		 * A label that is positioned exactly at the same time as the <code>time</code> parameter will be ignored. 
		 * 
		 * <p>You could use <code>getLabelAfter()</code> in conjunction with <code>tweenTo()</code> to make 
		 * the timeline tween to the next label like this:</p>
		 * 
		 * <p><code>
		 * myTimeline.tweenTo( myTimeline.getLabelAfter() );
		 * </code></p>
		 * 
		 * @param time Time after which the label is searched for. If you do not pass a time in, the current time will be used. 
		 * @return Name of the label that is after the time passed to getLabelAfter()
		 * 
		 * @see #getLabelBefore()
		 * @see #currentLabel()
		 */
		public function getLabelAfter(time:Number=NaN):String {
			if (!time) if (time != 0) { //faster than isNan()
				time = _time;
			}
			var labels:Array = getLabelsArray(),
				l:int = labels.length,
				i:int;
			for (i = 0; i < l; i++) {
				if (labels[i].time > time) {
					return labels[i].name;
				}
			}
			return null;
		}
		
		/**
		 * Returns the previous label (if any) that occurs <strong>before</strong> the <code>time</code> parameter. 
		 * It makes no difference if the timeline is reversed ("before" means earlier in the timeline's local time zone). 
		 * A label that is positioned exactly at the same time as the <code>time</code> parameter will be ignored. 
		 * 
		 * <p>You could use <code>getLabelBefore()</code> in conjunction with <code>tweenTo()</code> to make 
		 * the timeline tween back to the previous label like this:</p>
		 * 
		 * <p><code>
		 * myTimeline.tweenTo( myTimeline.getLabelBefore() );
		 * </code></p>
		 * 
		 * @param time Time before which the label is searched for. If you do not pass a time in, the current time will be used. 
		 * @return Name of the label that is before the time passed to getLabelBefore()
		 * 
		 * @see #getLabelBefore()
		 * @see #currentLabel()
		 */
		public function getLabelBefore(time:Number=NaN):String {
			if (!time) if (time != 0) { //faster than isNan()
				time = _time;
			}
			var labels:Array = getLabelsArray(),
				i:int = labels.length;
			while (--i > -1) {
				if (labels[i].time < time) {
					return labels[i].name;
				}
			}
			return null;
		}
		
		/** 
		 * Returns an Array of label objects, each with a "time" and "name" property, in the order that they occur in the timeline.
		 * For example, to loop through all the labels in order and trace() them to the screen (or console.log() in JavaScript):
		 * 
		 * <listing version="3.0">
var labels = myTimeline.getLabelsArray();
for (var i = 0; i &lt; labels.length; i++) {
	trace("label name: " + labels[i].name + ", time: " + labels[i].time); //or in JS, console.log("label name: " + labels[i].name + ", time: " + labels[i].time);
}
</listing>
		 * <p>Note: changing the values in this array will have no effect on the actual labels inside the TimelineMax. To add/remove labels, 
		 * use the corresponding methods (<code>addLabel(), removeLabel()</code>).</p>
		 * 
		 * @return An array of generic objects (one for each label) with a "name" property and a "time" property in the order they occur in the TimelineMax.
		 **/
		public function getLabelsArray():Array {
			var a:Array = [],
				cnt:int = 0,
				p:String;
			for (p in _labels) {
				a[cnt++] = {time:_labels[p], name:p};
			}
			a.sortOn("time", Array.NUMERIC);
			return a;
		}
		

//---- EVENT DISPATCHING ----------------------------------------------------------------------------------------------------------
		
		/** @private **/
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
		 * (AS3 only)
		 * Registers a function that should be called each time a particular type of event occurs, like 
		 * <code>"complete"</code> or <code>"update"</code>. The function will be passed a single "event" 
		 * parameter whose "<code>target</code>" property refers to the timeline. Typically it is more efficient
		 * to use callbacks like <code>onComplete, onUpdate, onStart, onReverseComplete,</code> and <code>onRepeat</code>
		 * unless you need the event parameter or if you need to register more than one listener for the same 
		 * type of event. 
		 * 
		 * If you no longer need an event listener, remove it by calling <code>removeEventListener()</code>, or memory 
		 * problems could result. Event listeners are not automatically removed from memory because the garbage 
		 * collector does not remove the listener as long as the dispatching object exists (unless the 
		 * useWeakReference parameter is set to true).
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
		 * (AS3 only)
		 * Removes a listener from the EventDispatcher object. If there is no matching listener registered 
		 * with the EventDispatcher object, a call to this method has no effect.
		 * 
		 * @param type The type of event
		 * @param listener The listener object to remove. 
		 * @param useCapture Specifies whether the listener was registered for the capture phase or the target and bubbling phases. If the listener was registered for both the capture phase and the target and bubbling phases, two calls to removeEventListener() are required to remove both, one call with useCapture() set to true, and another call with useCapture() set to false.
		 **/
		public function removeEventListener(type:String, listener:Function, useCapture:Boolean=false):void {
			if (_dispatcher != null) {
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
		
		
//---- GETTERS / SETTERS -------------------------------------------------------------------------------------------------------
		
		/** 
		 * Gets or sets the timeline's progress which is a value between 0 and 1 indicating the position 
		 * of the virtual playhead (<strong>excluding</strong> repeats) where 0 is at the beginning, 0.5 is halfway complete, 
		 * and 1 is complete. If the timeline has a non-zero <code>repeat</code> defined, <code>progress</code> 
		 * and <code>totalProgress</code> will be different because <code>progress</code> doesn't include any 
		 * repeats or repeatDelays whereas <code>totalProgress</code> does. For example, if a TimelineMax instance 
		 * is set to repeat once, at the end of the first cycle <code>totalProgress</code> would only be 0.5 
		 * whereas <code>progress</code> would be 1. If you watched both properties over the course of the entire 
		 * animation, you'd see <code>progress</code> go from 0 to 1 twice (once for each cycle) in the 
		 * same time it takes the <code>totalProgress</code> to go from 0 to 1 once.
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myTimeline.progress(0.5).play();</code></p>
		 * 
		 * <listing version="3.0">
var progress = myTimeline.progress(); //gets current progress
myTimeline.progress( 0.25 ); //sets progress to one quarter finished
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
		 * Gets or sets the timeline's total progress which is a value between 0 and 1 indicating the position 
		 * of the virtual playhead (<strong>including</strong> repeats) where 0 is at the beginning, 0.5 is 
		 * at the halfway point, and 1 is at the end (complete). If the timeline has a non-zero <code>repeat</code> defined, 
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
		override public function totalProgress(value:Number=NaN, suppressEvents:Boolean=true):* {
			return (!arguments.length) ? _totalTime / totalDuration() : totalTime( totalDuration() * value, suppressEvents);
		}
		
		/**
		 * Gets or sets the total duration of the timeline in seconds (or frames for frames-based timelines) 
		 * <strong>including</strong> any repeats or repeatDelays. <code>duration</code>, by contrast, does 
		 * <strong>NOT</strong> include repeats and repeatDelays. For example, if the timeline has a 
		 * <code>duration</code> of 10, a <code>repeat</code> of 1 and a <code>repeatDelay</code> of 2, 
		 * the <code>totalDuration</code> would be 22.
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining.</p>
		 * 
		 * <listing version="3.0">
var total = myTimeline.totalDuration(); //gets total duration
myTimeline.totalDuration(10); //sets the total duration
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
					super.totalDuration(); //just forces refresh
					//Instead of Infinity, we use 999999999999 so that we can accommodate reverses.
					_totalDuration = (_repeat == -1) ? 999999999999 : _duration * (_repeat + 1) + (_repeatDelay * _repeat);
				}
				return _totalDuration;
			}
			return (_repeat == -1) ? this : duration( (value - (_repeat * _repeatDelay)) / (_repeat + 1) );
		}
		
		/**
		 * Gets or sets the local position of the playhead (essentially the current time), <strong>not</strong> 
		 * including any repeats or repeatDelays. If the timeline has a non-zero <code>repeat</code>, its <code>time</code> 
		 * goes back to zero upon repeating even though the <code>totalTime</code> continues forward linearly 
		 * (or if <code>yoyo</code> is <code>true</code>, the <code>time</code> alternates between moving forward 
		 * and backward). <code>time</code> never exceeds the duration whereas the <code>totalTime</code> reflects 
		 * the overall time including any repeats and repeatDelays. 
		 * 
		 * <p>For example, if a TimelineMax instance has a <code>duration</code> of 2 and a repeat of 3, 
		 * <code>totalTime</code> will go from 0 to 8 during the course of the timeline (plays once then 
		 * repeats 3 times, making 4 total cycles) whereas <code>time</code> would go from 0 to 2 a 
		 * total of 4 times.</p>
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining.</p>
		 * 
		 * <listing version="3.0">
var currentTime = myTimeline.time(); //gets current time
myTimeline.time(2); //sets time, jumping to new value just like seek().
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
		
		/** 
		 * Gets or sets the number of times that the timeline should repeat after its first iteration. For 
		 * example, if <code>repeat</code> is 1, the timeline will play a total of twice (the initial play
		 * plus 1 repeat). To repeat indefinitely, use -1. <code>repeat</code> should always be an integer.
		 * 
		 * <p>To cause the repeats to alternate between forward and backward, set <code>yoyo</code> to 
		 * <code>true</code>. To add a time gap between repeats, use <code>repeatDelay</code>. You can 
		 * set the initial <code>repeat</code> value via the <code>vars</code> parameter, like:</p>
		 * 
		 * <p><code>
		 * var tl = new TimelineMax({repeat:2});
		 * </code></p>
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myTimeline.repeat(2).yoyo(true).play();</code></p>
		 * 
		 * <listing version="3.0">
var repeat = myTimeline.repeat(); //gets current repeat value
myTimeline.repeat(2); //sets repeat to 2
</listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #repeatDelay()
		 * @see #yoyo()
		 **/
		public function repeat(value:Number=0):* {
			if (!arguments.length) {
				return _repeat;
			}
			_repeat = value;
			return _uncache(true);
		}
		
		/**
		 * Gets or sets the amount of time in seconds (or frames for frames-based timelines) between repeats. 
		 * For example, if <code>repeat</code> is 2 and <code>repeatDelay</code> is 1, the timeline will 
		 * play initially, then wait for 1 second before it repeats, then play again, then wait 1 second 
		 * again before doing its final repeat. You can set the initial <code>repeatDelay</code> value 
		 * via the <code>vars</code> parameter, like:
		 * 
		 * <p><code>
		 * var tl = new TimelineMax({repeat:2, repeatDelay:1});
		 * </code></p>
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myTimeline.repeat(2).yoyo(true).repeatDelay(0.5).play();</code></p>
		 * 
		 * <listing version="3.0">
var repeatDelay = myTimeline.repeatDelay(); //gets current repeatDelay value
myTimeline.repeatDelay(2); //sets repeatDelay to 2
</listing>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #repeat()
		 * @see #yoyo()
		 **/
		public function repeatDelay(value:Number=0):* {
			if (!arguments.length) {
				return _repeatDelay;
			}
			_repeatDelay = value;
			return _uncache(true);
		}
		
		/**
		 * Gets or sets the timeline's <code>yoyo</code> state, where <code>true</code> causes
		 * the timeline to go back and forth, alternating backward and forward on each 
		 * <code>repeat</code>. <code>yoyo</code> works in conjunction with <code>repeat</code>,
		 * where <code>repeat</code> controls how many times the timeline repeats, and <code>yoyo</code>
		 * controls whether or not each repeat alternates direction. So in order to make a timeline yoyo, 
		 * you must set its <code>repeat</code> to a non-zero value.
		 * Yoyo-ing, has no affect on the timeline's "<code>reversed</code>" property. For example, 
		 * if <code>repeat</code> is 2 and <code>yoyo</code> is <code>false</code>, it will look like: 
		 * start - 1 - 2 - 3 - 1 - 2 - 3 - 1 - 2 - 3 - end. But if <code>yoyo</code> is <code>true</code>, 
		 * it will look like: start - 1 - 2 - 3 - 3 - 2 - 1 - 1 - 2 - 3 - end.
		 * 
		 * <p>You can set the <code>yoyo</code> property initially by passing <code>yoyo:true</code>
		 * in the <code>vars</code> parameter, like: <code>new TimelineMax({repeat:1, yoyo:true});</code></p>
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining, like <code>myTimeline.yoyo(true).repeat(3).timeScale(2).play(0.5);</code></p>
		 * 
		 * <listing version="3.0">
var yoyo = myTimeline.yoyo(); //gets current yoyo state
myTimeline.yoyo( true ); //sets yoyo to true
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
		
		/** 
		 * Gets the closest label that is at or before the current time, or jumps to a provided label 
		 * (behavior depends on whether or not you pass a parameter to the method). 
		 * 
		 * <p>This method serves as both a getter and setter. Omitting the parameter returns the current 
		 * value (getter), whereas defining the parameter sets the value (setter) and returns the instance 
		 * itself for easier chaining.</p>
		 * 
		 * @param value Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * @return Omitting the parameter returns the current value (getter), whereas defining the parameter sets the value (setter) and returns the instance itself for easier chaining.
		 * 
		 * @see #getLabelAfter()
		 * @see #getLabelBefore()
		 **/
		public function currentLabel(value:String=null):* {
			if (!arguments.length) {
				return getLabelBefore(_time + 0.00000001);
			}
			return seek(value, true);
		}
		
	}
}