/**
 * VERSION: 12.0.5
 * DATE: 2013-03-27
 * AS3
 * UPDATES AND DOCS AT: http://www.greensock.com/roughease/
 **/
package com.greensock.easing {
	import com.greensock.easing.core.EasePoint;
/**
 * Most easing equations give a smooth, gradual transition between the start and end values, but RoughEase provides
 * an easy way to get a rough, jagged effect instead, or you can also get an evenly-spaced back-and-forth movement 
 * if you prefer. Configure the RoughEase by passing an object to the constructor or config() method with any
 * of the following properties (all are optional):
 * 
 * <ul>
 * <li><strong>template</strong> : Ease - an ease that should be used as a template, like a general guide.  
 * 				The RoughEase will plot points that wander from that template. You can use this to influence 
 * 				the general shape of the RoughEase. (Default: <code>Linear.easeNone</code>)</li>
 * 
 * <li><strong>strength</strong> : Number - controls how far from the template ease the points are allowed to wander 
 * 				(a small number like 0.1 keeps it very close to the template ease whereas a larger number like 5 creates 
 * 				much bigger variations). (Default: <code>1</code>)</li>
 * 
 * <li><strong>points</strong> : Number - the number of points to be plotted along the ease, making it jerk more or less
 * 				frequently. (Default: <code>20</code>)</li>
 * 
 * <li><strong>clamp</strong> : Boolean - setting <code>clamp</code> to <code>true</code> will prevent points from 
 * 				exceeding the end value or dropping below the starting value. For example, if you're tweening the x 
 * 				property from 0 to 100, the RoughEase would force all random points to stay between 0 and 100 if 
 * 				<code>clamp</code> is <code>true</code>, but if it is <code>false</code>, x could potentially jump 
 * 				above 100 or below 0 at some point during the tween (it would always end at 100 though in this example)
 * 				(Default: <code>false</code>).</li>
 * 
 * <li><strong>taper</strong> : String (<code>"in" | "out" | "both" | "none"</code>) - to make the strength of the 
 * 				roughness taper towards the end or beginning or both, use <code>"out"</code>, <code>"in"</code>, 
 * 				or <code>"both"</code> respectively. (Default: <code>"none"</code>)</li>
 * 
 * <li><strong>randomize</strong> : Boolean - by default, the placement of points will be randomized (creating the roughness)
 * 				but you can set <code>randomize</code> to <code>false</code> to make the points zig-zag evenly across the ease.
 * 				Using this in conjunction with a <code>taper</code> value can create a nice effect. (Default: <code>true</code>)</li>
 * </ul>
 * 
 * <p>For a visual example and more details, check out <a href="http://www.greensock.com/roughease/">http://www.greensock.com/roughease/</a>.</p>
 * 
 * <p><strong>Example code</strong></p>
 * <listing version="3.0">
import com.greensock.TweenLite;
import com.greensock.easing.~~;
 
//use the default values
TweenLite.from(mc, 3, {alpha:0, ease:RoughEase.ease});
 
//or customize the configuration
TweenLite.to(mc, 3, {y:300, ease:RoughEase.ease.config({strength:3, points:50, template:Strong.easeInOut, taper:"both", randomize:false}) });
 
//or create a RoughEase that we can pass in to multiple tweens later
var rough:RoughEase = new RoughEase({strength:3, points:50, template:Strong.easeInOut, taper:"both", randomize:false});
TweenLite.to(mc, 3, {y:300, ease:rough});
TweenLite.to(mc2, 5, {x:500, ease:rough});
</listing>
 * 
 * <p><strong>Copyright 2010-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */	 
	public class RoughEase extends Ease {
		/** The default ease instance which can be reused many times in various tweens in order to conserve memory and improve performance slightly compared to creating a new instance each time. **/
		public static var ease:RoughEase = new RoughEase();
		/** @private **/
		private static var _lookup:Object = {}; //keeps track of all named instances so we can find them in byName().
		/** @private **/
		private static var _count:int = 0;
		
		/** @private **/
		private var _name:String;
		/** @private **/
		private var _first:EasePoint;
		/** @private **/
		private var _prev:EasePoint;
		
		/**
		 * Constructor
		 * 
		 * @param vars a generic object with any of the following properties (all are completely optional): 
		 * <ul>
		 * <li><strong>template</strong> : Ease - an ease that should be used as a template, like a general guide.  
		 * 				The RoughEase will plot points that wander from that template. You can use this to influence 
		 * 				the general shape of the RoughEase. (Default: <code>Linear.easeNone</code>)</li>
		 * 
		 * <li><strong>strength</strong> : Number - controls how far from the template ease the points are allowed to wander 
		 * 				(a small number like 0.1 keeps it very close to the template ease whereas a larger number like 5 creates 
		 * 				much bigger variations). (Default: <code>1</code>)</li>
		 * 
		 * <li><strong>points</strong> : Number - the number of points to be plotted along the ease, making it jerk more or less
		 * 				frequently. (Default: <code>20</code>)</li>
		 * 
		 * <li><strong>clamp</strong> : Boolean - setting <code>clamp</code> to <code>true</code> will prevent points from 
		 * 				exceeding the end value or dropping below the starting value. For example, if you're tweening the x 
		 * 				property from 0 to 100, the RoughEase would force all random points to stay between 0 and 100 if 
		 * 				<code>clamp</code> is <code>true</code>, but if it is <code>false</code>, x could potentially jump 
		 * 				above 100 or below 0 at some point during the tween (it would always end at 100 though in this example)
		 * 				(Default: <code>false</code>).</li>
		 * 
		 * <li><strong>taper</strong> : String (<code>"in" | "out" | "both" | "none"</code>) - to make the strength of the 
		 * 				roughness taper towards the end or beginning or both, use <code>"out"</code>, <code>"in"</code>, 
		 * 				or <code>"both"</code> respectively. (Default: <code>"none"</code>)</li>
		 * 
		 * <li><strong>randomize</strong> : Boolean - by default, the placement of points will be randomized (creating the roughness)
		 * 				but you can set <code>randomize</code> to <code>false</code> to make the points zig-zag evenly across the ease.
		 * 				Using this in conjunction with a <code>taper</code> value can create a nice effect. (Default: <code>true</code>)</li>
		 * </ul>
		 */
		public function RoughEase(vars:*=null, ...args) {
			if (typeof(vars) !== "object" || vars == null) {
				vars = {strength:vars, points:args[0], clamp:args[1], template:args[2], taper:args[3], randomize:args[4], name:args[5]};
			}
			if (vars.name) {
				_name = vars.name;
				_lookup[vars.name] = this;
			} else {
				_name = "roughEase" + (_count++);
			}
			var taper:String = vars.taper || "none",
				a:Array = [],
				cnt:int = 0,
				points:int = int(vars.points) || 20,
				i:int = points,
				randomize:Boolean = (vars.randomize !== false),
				clamp:Boolean = (vars.clamp === true),
				template:Ease = (vars.template is Ease) ? vars.template : null,
				strength:Number = (typeof(vars.strength) === "number") ? vars.strength * 0.4 : 0.4,
				x:Number, y:Number, bump:Number, invX:Number, obj:Object;		
			while (--i > -1) {
				x = randomize ? Math.random() : (1 / points) * i;
				y = (template != null) ? template.getRatio(x) : x;
				if (taper === "none") {
					bump = strength;
				} else if (taper === "out") {
					invX = 1 - x;
					bump = invX * invX * strength;
				} else if (taper === "in") {
					bump = x * x * strength;
				} else if (x < 0.5) { 	//"both" (start)
					invX = x * 2;
					bump = invX * invX * 0.5 * strength;
				} else {				//"both" (end)
					invX = (1 - x) * 2;
					bump = invX * invX * 0.5 * strength;
				}
				if (randomize) {
					y += (Math.random() * bump) - (bump * 0.5);
				} else if (i % 2) {
					y += bump * 0.5;
				} else {
					y -= bump * 0.5;
				}
				if (clamp) {
					if (y > 1) {
						y = 1;
					} else if (y < 0) {
						y = 0;
					}
				}
				a[cnt++] = {x:x, y:y};
			}
			a.sortOn("x", Array.NUMERIC);
			
			_first = new EasePoint(1, 1, null);
			i = points;
			while (--i > -1) {
				obj = a[i];
				_first = new EasePoint(obj.x, obj.y, _first);
			}
			
			_first = _prev = new EasePoint(0, 0, (_first.time !== 0) ? _first : _first.next);
		}
		
		/**
		 * @private
		 * DEPRECATED
		 * This static function provides a quick way to create a RoughEase and immediately reference its ease function 
		 * in a tween, like:<br /><br /><code>
		 * 
		 * TweenLite.from(mc, 2, {alpha:0, ease:RoughEase.create(1.5, 15)});<br />
		 * </code>
		 * 
		 * @param strength amount of variance from the templateEase (Linear.easeNone by default) that each random point can be placed. A low number like 0.1 will hug very closely to the templateEase whereas a larger number like 2 will allow the values to wander further away from the templateEase.
		 * @param points quantity of random points to plot in the ease. A larger number will cause more (and quicker) flickering.
		 * @param clamp If true, the ease will prevent random points from exceeding the end value or dropping below the starting value. For example, if you're tweening the x property from 0 to 100, the RoughEase would force all random points to stay between 0 and 100 if restrictMaxAndMin is true, but if it is false, a x could potentially jump above 100 or below 0 at some point during the tween (it would always end at 100 though).
		 * @param templateEase an easing equation that should be used as a template or guide. Then random points are plotted at a certain distance away from the templateEase (based on the strength parameter). The default is Linear.easeNone.
		 * @param taper to make the strength of the roughness taper towards the end or beginning or both, use "out", "in", or "both" respectively here (default is "none").
		 * @param randomize to randomize the placement of the points, set randomize to true (otherwise the points will zig-zag evenly across the ease)
		 * @param name a name to associate with the ease so that you can use RoughEase.byName() to look it up later. Of course you should always make sure you use a unique name for each ease (if you leave it blank, a name will automatically be generated). 
		 * @return easing function
		 */
		public static function create(strength:Number=1, points:uint=20, clamp:Boolean=false, templateEase:Ease=null, taper:String="none", randomize:Boolean=true, name:String=""):Ease {
			return new RoughEase(strength, points, clamp, templateEase, taper, randomize, name);
		}
		
		/**
		 * @private
		 * DEPRECATED
		 * Provides a quick way to look up a RoughEase by its name.
		 * 
		 * @param name the name of the RoughEase
		 * @return the RoughEase associated with the name
		 */
		public static function byName(name:String):Ease {
			return _lookup[name];
		}
			
		/**
		 * Translates the tween's progress ratio into the corresponding ease ratio. This is the heart of the Ease, where it does all its work.
		 * 
		 * @param p progress ratio (a value between 0 and 1 indicating the progress of the tween/ease)
		 * @return translated number
		 */
		override public function getRatio(p:Number):Number {
			var pnt:EasePoint = _prev;
			if (p > _prev.time) {
				while (pnt.next && p >= pnt.time) {
					pnt = pnt.next;
				}
				pnt = pnt.prev;
			} else {
				while (pnt.prev && p <= pnt.time) {
					pnt = pnt.prev;
				}
			}
			_prev = pnt;
			return (pnt.value + ((p - pnt.time) / pnt.gap) * pnt.change);
		}
		
		/** @private [DEPRECATED] Disposes the RoughEase so that it is no longer stored for easy lookups by name with <code>byName()</code>, releasing it for garbage collection. **/
		public function dispose():void {
			delete _lookup[_name];
		}
		
		/** @private [DEPRECATED] name of the RoughEase instance **/
		public function get name():String {
			return _name;
		}
		
		/** @private [DEPRECATED] name of the RoughEase instance **/
		public function set name(value:String):void {
			delete _lookup[_name];
			_name = value;
			_lookup[_name] = this;
		}
		
		/**
		 * Permits customization of the ease with various parameters.
		 * 
		 * @param vars a generic object with any of the following properties (all are completely optional): 
		 * <ul>
		 * <li><strong>template</strong> : Ease - an ease that should be used as a template, like a general guide.  
		 * 				The RoughEase will plot points that wander from that template. You can use this to influence 
		 * 				the general shape of the RoughEase. (Default: <code>Linear.easeNone</code>)</li>
		 * 
		 * <li><strong>strength</strong> : Number - controls how far from the template ease the points are allowed to wander 
		 * 				(a small number like 0.1 keeps it very close to the template ease whereas a larger number like 5 creates 
		 * 				much bigger variations). (Default: <code>1</code>)</li>
		 * 
		 * <li><strong>points</strong> : Number - the number of points to be plotted along the ease, making it jerk more or less
		 * 				frequently. (Default: <code>20</code>)</li>
		 * 
		 * <li><strong>clamp</strong> : Boolean - setting <code>clamp</code> to <code>true</code> will prevent points from 
		 * 				exceeding the end value or dropping below the starting value. For example, if you're tweening the x 
		 * 				property from 0 to 100, the RoughEase would force all random points to stay between 0 and 100 if 
		 * 				<code>clamp</code> is <code>true</code>, but if it is <code>false</code>, x could potentially jump 
		 * 				above 100 or below 0 at some point during the tween (it would always end at 100 though in this example)
		 * 				(Default: <code>false</code>).</li>
		 * 
		 * <li><strong>taper</strong> : String (<code>"in" | "out" | "both" | "none"</code>) - to make the strength of the 
		 * 				roughness taper towards the end or beginning or both, use <code>"out"</code>, <code>"in"</code>, 
		 * 				or <code>"both"</code> respectively. (Default: <code>"none"</code>)</li>
		 * 
		 * <li><strong>randomize</strong> : Boolean - by default, the placement of points will be randomized (creating the roughness)
		 * 				but you can set <code>randomize</code> to <code>false</code> to make the points zig-zag evenly across the ease.
		 * 				Using this in conjunction with a <code>taper</code> value can create a nice effect. (Default: <code>true</code>)</li>
		 * </ul>
		 * @return new RoughEase instance that is configured according to the parameters provided
		 */
		public function config(vars:Object=null):RoughEase {
			return new RoughEase(vars);
		}

	}
}