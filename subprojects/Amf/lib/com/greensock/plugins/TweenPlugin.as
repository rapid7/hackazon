/**
 * VERSION: 12.1.5
 * DATE: 2013-07-21
 * AS3
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
	import com.greensock.core.PropTween;
/**
 * TweenPlugin is the base class for all TweenLite and TweenMax plugins, but generally isn't used directly. 
 * 	
 * <p><strong>USAGE:</strong></p>
 * 
 * <p>To create your own plugin, extend TweenPlugin and override whichever methods you need. Typically,
 * you only need to override <code>_onInitTween()</code> (which is called when the tween renders for
 * the first time) and <code>setRatio()</code> (which is called on every update and passes a progress 
 * parameter which is typically a value between 0 and 1, but changes according to the ease). I'd recommend 
 * looking at a simple plugin like ScalePlugin and using it as a template of sorts. There are a few 
 * key concepts to keep in mind:</p>
 * 
 * <ol>
 * 	<li> Pass the TweenPlugin constructor a comma-delimited list of property names that the plugin should
 * 		overwrite, the first of which should be the property name that the plugin intercepts. For example, 
 * 		the ScalePlugin handles any tweens of <code>"scale"</code> and it also overwrites other concurrent 
 * 		tweens that are handling the "scale", "scaleX", and/or "scaleY" properties of the target. Therefore, 
 * 		in ScalePlugin's constructor, we'd call <code>super("scale,scaleX,scaleY")</code>. The first name
 * 		in the list must be unique - two plugins cannot handle the same primary property. </li>
 * 		
 * 	<li> When a tween that uses your plugin initializes its tween values (normally when it renders the 
 * 		first time), a new instance of your plugin will be created and the <code>_onInitTween()</code> method 
 * 		is called. That's where you'll want to record any initial values and prepare for the tween. 
 * 		<code>_onInitTween()</code> should return a Boolean value that essentially indicates whether 
 * 		or not the plugin initted successfully. If you return false, TweenLite/Max  will just use a 
 * 		normal tween for the value, ignoring the plugin for that particular tween. For example,
 * 		maybe your tween only works with MovieClips, so if the target isn't a MovieClip you could 
 * 		return <code>false</code></li>
 * 		  
 * 	<li> The <code>setRatio()</code> method will be called on every frame during the course of the tween 
 * 		and it will be passed a single parameter that's a multiplier (typically between 0 and 1, according
 * 		to the ease) describing the total amount of change from the beginning of the tween (0). It will be 
 * 		zero at the beginning of the tween and 1 at the end, but inbetween it could be any value based on the 
 * 		ease applied (for example, an <code>ElasticOut</code> ease would cause the value to shoot past 1 and 
 * 		back again before the end of the tween). So if the tween uses the <code>Linear.ease</code>, when it's 
 * 		halfway finished, the <code>setRatio()</code> will receive a parameter of 0.5.</li>
 * 		  
 * 	<li> The <code>_overwriteProps</code> is an array that should contain the properties that your 
 * 		plugin should overwrite in <code>"auto"</code> mode. For example, the <code>autoAlpha</code> 
 * 		plugin controls the <code>"visible"</code> and <code>"alpha"</code> properties of an object, 
 * 		so if another tween is created that controls the <code>alpha</code> of the target object, 
 * 		your plugin's <code>_kill()</code> method will be called which should handle killing the 
 * 		<code>"alpha"</code> part of the tween. It is your responsibility to populate (and depopulate) 
 * 		the <code>_overwriteProps</code> Array. Failure to do so properly can cause odd overwriting 
 * 		behavior.</li>
 * 		  
 * 	<li> There's a <code>_roundProps()</code> method that gets called by the RoundPropsPlugin if the
 * 		user requests that certain properties get rounded to the nearest integer. If you use 
 * 		<code>_addTween()</code> method to add property tweens, rounding will happen automatically 
 * 		(if necessary), but if you don't use <code>_addTween()</code> and prefer to manually calculate 
 * 		tween values in your <code>setRatio()</code> method, just remember to override the <code>_roundProps()</code>
 * 		method if that makes sense in your plugin (some plugins wouldn't need to accommodate rounding, like color
 * 		plugins).</li>
 * 
 * 	<li> If you need to run a function when the tween gets disabled, add an <code>_onDisable()</code> method
 * 		(named exactly that) to your plugin. It will automatically be called when the tween gets disabled (typically
 * 		when it finishes and is removed from its parent timeline). Same for <code>_onEnable()</code> if you 
 * 		need to run code when a tween is enabled. These methods should return a Boolean value indicating 
 * 		whether or not they changed any properties on the target becaues if so (<code>true</code>), it helps
 * 		notify any initting tweens of the same target to re-init. It is very rare that an <code>_onDisable()</code>
 * 		or <code>_onEnable()</code> method is necessary, but it can be useful for things like MotionBlurPlugin
 * 		which must do some very advanced things, hiding the target, changing its alpha to almost 0, etc. only
 * 		while the tween occurs. If another alpha tween of that same target overwrites an existing motionBlur
 * 		of the same target, the alpha would be at the wrong value normally, but the if the <code>_onDisable()</code>
 * 		returns <code>true</code>, it would force the new tween to re-init AFTER the alpha was fixed inside
 * 		the <code>_onDisable()</code>. Again, this is VERY rare.</li>
 * 		
 * 	<li> Please use the same naming convention as the rest of the plugins, like MySpecialPropertyNamePlugin.</li>
 * 
 * 	<li> If you are handling more than one property in your plugin (like RoundPropsPlugin or ShortRotationPlugin),
 * 		 make sure you override the <code>_kill()</code> method which will be passed a <code>vars</code> parameter
 * 		with properties that need to be killed (typically for overwriting).</li>
 * 		
 * </ol>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class TweenPlugin {
		/** @private **/
		public static const version:String = "12.1.5";
		
		/** @private If the API/Framework for plugins changes in the future, this number helps determine compatibility **/
		public static const API:Number = 2; 
		
		/** @private Name of the special property that the plugin should intercept/handle **/
		public var _propName:String;
		
		/**
		 * @private 
		 * Array containing the names of the properties that should be overwritten in <code>"auto"</code> mode. 
		 * Typically the only value in this Array is the _propName, but there are cases when it may 
		 * be different. For example, a bezier tween's propName is "bezier" but it can manage many different properties 
		 * like x, y, etc. depending on what's passed in to the tween.
		 */
		public var _overwriteProps:Array;
		
		/** @private Priority level in the render queue **/
		public var _priority:int = 0;
		
		/** @private First property tween in the linked list (if any) **/
		protected var _firstPT:PropTween;		
		
		/**
		 * @private
		 * Constructor
		 * 
		 * @param props A comma-delimited list of properties that will populate the <code>_overwriteProps</code> array, the first of which will be the <code>_propName</code> (the special property that the plugin handles). For example, the ScalePlugin would be <code>"scale,scaleX,scaleY"</code>.
		 * @param priority The priority in the rendering queue (lower priorty renders after higher priority). For example, a motionBlur might need to wait until all other properties like x and y have tweened before it does its magic of figuring out how far things have moved, etc. so motionBlur's priority could be low (like -10). Standard property tweens are always 0. To render before other things, use a high priority.
		 */
		public function TweenPlugin(props:String="", priority:int=0) {
			_overwriteProps = props.split(",");
			_propName = _overwriteProps[0];
			_priority = priority || 0;
		}
		
		/**
		 * @private 
		 * Gets called when any tween of the special property begins. Record any initial values
		 * that will be used in the <code>setRatio()</code> method. 
		 * 
		 * @param target target object that should be affected. This is the same as the tween's target unless the tween's target is an array in which case a different plugin instance is created for each object in the array, so this target would be the object in the array. 
		 * @param value The value that is passed in through the special property in the tween. For example, if this is the ScalePlugin and the tween is <code>TweenLite.to(mc, 1, {scale:2.5})</code>, the <code>value</code> would be 2.5. 
		 * @param tween The TweenLite or TweenMax instance using this plugin.
		 * @return If the initialization failed, it returns false. Otherwise true. It may fail if, for example, the plugin requires that the target be a DisplayObject or has some other unmet criteria in which case the plugin is skipped and a normal property tween is used inside TweenLite/Max
		 */
		public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			return false;
		}
		
		/**
		 * @private 
		 * Offers a simple way to add tweening values to the plugin. You don't need to use this,
		 * but it is convenient because the tweens get updated in the <code>setRatio()</code> method which also 
		 * handles rounding. <code>_kill()</code> nicely integrates with most tweens added via <code>_addTween()</code>
		 * as well, but if you prefer to handle this manually in your plugin, you're welcome to.
		 *  
		 * @param target Target object whose property you'd like to tween. (i.e. myClip)
		 * @param propName The property name that should be tweened. (i.e. "x")
		 * @param start Starting value
		 * @param end End value (can be either numeric or a string value. If it's a string, it will be interpreted as relative to the starting value)
		 * @param overwriteProp Name of the property that should be associated with the tween for overwriting purposes. Normally, it's the same as propName, but not always. For example, you may tween the "setRatio" property of a VisiblePlugin, but the property that it's actually controling in the end is "visible", so if a new overlapping tween of the target object is created that affects its "visible" property, this allows the plugin to kill the appropriate tween(s) when _kill() is called.
		 * @param round If <code>true</code>, the property should be rounded to the closest integer whenever updated 
		 * @return If a PropTween is created (which means a tween was required between the provided start and end values), that PropTween is returned. Otherwise, null is returned. 
		 */
		protected function _addTween(target:Object, propName:String, start:Number, end:*, overwriteProp:String=null, round:Boolean=false):PropTween {
			var c:Number = (end == null) ? 0 : (typeof(end) === "number" || end.charAt(1) !== "=") ? Number(end) - start : int(end.charAt(0) + "1") * Number(end.substr(2));
			if (c !== 0) {
				_firstPT = new PropTween(target, propName, start, c, overwriteProp || propName, false, _firstPT);
				_firstPT.r = round;
				return _firstPT;
			}
			return null;
		}
		
		/**
		 * @private 
		 * In most cases, your custom updating code should go here. The <code>setRatio()</code> value describes the 
		 * amount of change based on how far along the tween is and the ease applied. It will be zero at the beginning
		 * of the tween and 1 at the end, but inbetween it could be any value based on the ease applied (for example, 
		 * an ElasticOut tween would cause the value to shoot past 1 and back again before the end of the tween) 
		 * This value gets updated on every frame during the course of the tween.
		 * 
		 * @param v Multiplier describing the overall amount of change that should be applied since the start. It will be zero at the beginning of the tween and 1 at the end, but inbetween it could be any value based on the ease applied (for example, an ElasticOut tween would cause the value to shoot past 1 and back again before the end of the tween) 
		 */
		public function setRatio(v:Number):void {
			var pt:PropTween = _firstPT, val:Number;
			while (pt) {
				val = pt.c * v + pt.s;
				if (pt.r) {
					val = (val + ((val > 0) ? 0.5 : -0.5)) | 0; //about 4x faster than Math.round()
				}
				if (pt.f) {
					pt.t[pt.p](val);
				} else {
					pt.t[pt.p] = val;
				}
				pt = pt._next;
			}
		}
		
		/**
		 * @private
		 * Used internally by RoundPropsPlugin which passes <code>_round()</code> lookup object with properties 
		 * that should be rounded to the nearest integer during the tween. For example:
		 * 
		 * <p><code>
		 * TweenMax.to(mc, 2, {x:100, y:100, myPlugin:0.5, roundProps:"x,y"});
		 * </code></p>
		 * 
		 * <p>The above tween will result in RoundPropsPlugin passing the a <code>{x:1,y:1}"</code> object to the 
		 * <code>_roundProps()</code> method of the plugin that's managing the <code>myPlugin</code> special property 
		 * (should be named MyPluginPlugin by naming convention). Some plugins manage more than one property, like
		 * BezierPlugin, ShortRotationPlugin, etc. so it's possible that only certain properties should be rounded
		 * inside the plugin. If you're building a plugin that should accommodate rounding and you're not using
		 * the standard <code>_addTween()</code> to handle the property tweens, you should override this method and
		 * run your own logic.</p>
		 * 
		 * @param props A lookup object with property names that should be rounded.
		 */
		public function _roundProps(lookup:Object, value:Boolean=true):void {
			var pt:PropTween = _firstPT;
			while (pt) {
				if ((_propName in lookup) || (pt.n != null && pt.n.split(_propName + "_").join("") in lookup)) { //some properties that are very plugin-specific add a prefix named after the _propName plus an underscore, so we need to ignore that extra stuff here.
					pt.r = value;
				}
				pt = pt._next;
			}
		}
		
		/**
		 * @private 
		 * Gets called on plugins that have multiple overwritable properties in <code>"auto"</code> mode. 
		 * Basically, it instructs the plugin to overwrite certain properties. For example,
		 * if a bezier tween is affecting x, y, and width, and then a new tween is created while the 
		 * bezier tween is in progress, and the new tween affects the "x" property, we need a way
		 * to kill just the "x" part of the bezier tween. 
		 * 
		 * @param lookup An object containing properties that should be overwritten. We don't pass in an Array because looking up properties on the object is usually faster because it gives us random access. So to overwrite the "x" and "y" properties, a {x:true, y:true} object would be passed in. 
		 */
		public function _kill(lookup:Object):Boolean {
			if (_propName in lookup) {
				_overwriteProps = [];
			} else {
				var i:int = _overwriteProps.length;
				while (--i > -1) {
					if (_overwriteProps[i] in lookup) {
						_overwriteProps.splice(i, 1);
					}
				}
			}
			var pt:PropTween = _firstPT;
			while (pt) {
				if (pt.n in lookup) {
					if (pt._next) {
						pt._next._prev = pt._prev;
					}
					if (pt._prev) {
						pt._prev._next = pt._next;
						pt._prev = null;
					} else if (_firstPT == pt) {
						_firstPT = pt._next;
					}
				}
				pt = pt._next;
			}
			return false;
		}
		
		/**
		 * @private
		 * This method is called inside TweenLite after significant events occur, like when a tween
		 * has finished initializing, and (if necessary) when its "enabled" state changes.
		 * For example, the MotionBlurPlugin must run after normal x/y/alpha PropTweens are rendered,
		 * so the "_onInitAllProps" event reorders the PropTweens linked list in order of priority. 
		 * Some plugins need to do things when a tween completes or when it gets disabled. Again, this 
		 * method is only for internal use inside TweenLite. It is separated into
		 * this static method in order to minimize file size inside TweenLite.
		 * 
		 * @param type The type of event "_onInitAllProps", "_onEnable", or "_onDisable"
		 * @param tween The TweenLite/Max instance to which the event pertains
		 * @return A Boolean value indicating whether or not properties of the tween's target may have changed as a result of the event
		 */
		private static function _onTweenEvent(type:String, tween:TweenLite):Boolean {
			var pt:PropTween = tween._firstPT, changed:Boolean;
			if (type == "_onInitAllProps") {
				//sorts the PropTween linked list in order of priority because some plugins need to render earlier/later than others, like MotionBlurPlugin applies its effects after all x/y/alpha tweens have rendered on each frame.
				var pt2:PropTween, first:PropTween, last:PropTween, next:PropTween;
				while (pt) {
					next = pt._next;
					pt2 = first;
					while (pt2 && pt2.pr > pt.pr) {
						pt2 = pt2._next;
					}
					if ((pt._prev = pt2 ? pt2._prev : last)) {
						pt._prev._next = pt;
					} else {
						first = pt;
					}
					if ((pt._next = pt2)) {
						pt2._prev = pt;
					} else {
						last = pt;
					}
					pt = next;
				}
				pt = tween._firstPT = first;
			}
			while (pt) {
				if (pt.pg) if (type in pt.t) if (pt.t[type]()) {
					changed = true;
				}
				pt = pt._next;
			}
			return changed;
		}
		
		/**
		 * Activates one or more plugins so that TweenLite and TweenMax recognize the associated special properties. 
		 * You only need to activate each plugin once in order for it to be used in your project/app. For example, 
		 * the following code activates the ScalePlugin and RoundPropsPlugin:
		 * 
		 * <p><code>
		 * TweenPlugin.activate([ScalePlugin, RoundPropsPlugin]);
		 * </code></p>
		 * 
		 * <p>Thereafter, tweens will recognize the "scale" and "roundProps" special properties associated with
		 * these plugins. Like <code>TweenLite.to(mc, 1, {scale:5, x:300, roundProps:"x"});</code></p>
		 * 
		 * <p>Each plugin must extend TweenPlugin.</p>
		 * 
		 * @param plugins An Array of plugins to be activated. For example, <code>TweenPlugin.activate([FrameLabelPlugin, ShortRotationPlugin, TintPlugin]);</code>
		 */
		public static function activate(plugins:Array):Boolean {
			TweenLite._onPluginEvent = TweenPlugin._onTweenEvent;
			var i:int = plugins.length;
			while (--i > -1) {
				if (plugins[i].API == TweenPlugin.API) {
					TweenLite._plugins[(new (plugins[i] as Class)())._propName] = plugins[i];
				}
			}
			return true
		}
		
	}
}