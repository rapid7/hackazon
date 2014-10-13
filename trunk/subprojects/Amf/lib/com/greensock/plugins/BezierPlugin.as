/**
 * VERSION: 12.14
 * DATE: 2014-03-12
 * AS3 (AS2 and JavaScript versions also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
	
	import flash.geom.Point;

/**
 * Animate virtually any property (or properties) along a Bezier (curved) path which you define
 * as an array of points/values that can be interpreted 4 different ways (described as the Bezier's "type", like <code>type:"quadratic"</code>):
 * 
 * <ul>
 * 		<li><code>"thru"</code> (the default) - the plugin figures out how to draw the Bezier naturally through
 * 				the supplied values using a proprietary GreenSock algorithm. The values you provide in the array are essentially
 * 				treated as anchors on the Bezier and the plugin calculates the control points. The target's current/starting
 * 				values are used as the initial anchor. You can define a	<code>curviness</code> special property that 
 * 				allows you to adjust the tension on the Bezier where 0 has no curviness (straight lines), 1 is normal 
 * 				curviness, 2 is twice the normal curviness, etc. Since "thru" is the default Bezier type, you don't 
 * 				need to define a <code>type</code> at all if this is the one you want.</li>
 * 
 * 		<li><code>"soft"</code> - the values that you provide in the array act almost like magnets that attract the 
 * 				curve towards them, but the Bezier doesn't typically travel through them. They are treated 
 * 				as control points on a Quadratic Bezier and the plugin creates the necessary intermediate anchors.
 * 				The target's current/starting values are used as the initial anchor.</li>
 * 
 * 		<li><code>"quadratic"</code> - allows you to define standard Quadratic Bezier data (Quadratic Beziers have
 * 				1 control point between each anchor). The array should start with the first anchor, then control point, 
 * 				then anchor, control point, etc. for as many iterations as you want, but obviously make sure that it 
 * 				starts and ends with anchors.</li>
 * 
 * 		<li><code>"cubic"</code> - allows you to define standard Cubic Bezier data (Cubic Beziers have
 * 				2 control points between each anchor). The array should start with the first anchor, then 2 control points, 
 * 				then anchor, 2 control points, anchor, etc. for as many iterations as you want, but obviously make sure that it 
 * 				starts and ends with anchors.</li>
 * 
 * 		<li><code>"thruBasic"</code> - the same as <code>"thru"</code> except that it uses a less complex 
 * 				algorithm for the initial plotting of the Bezier through the supplied values. The "thruBasic"
 * 				algorithm is a slightly enhanced version of a somewhat common method that does a decent job
 * 				but it is more prone to having kinks or harsh angles when there is a very large segment right 
 * 				next to a very short one or when two anchors are very close and the one inbetween them is very distant. 
 * 				The proprietary GreenSock <code>"thru"</code> algorithm almost always delivers more natural curves 
 * 				than <code>"thruBasic"</code>. In terms of calculation expense, "thruBasic" is only about 15-20% faster 
 * 				on the initial setup (when the tween begins), but then every update during the tween the speed is 
 * 				identical, so overall improvement is negligible (probably less than 1%). The primary reason the 
 * 				<code>"thruBasic"</code> option is available is to offer a different style for drawing the Bezier 
 * 				through the supplied values. If decreasing load on the CPU is your goal, you'd get better
 * 				results by decreasing the <code>timeResolution</code>, particularly to 0.</li>
 * </ul>
 * 
 * <p>While it is most common to use <code>x</code> and <code>y</code> (and sometimes <code>z</code>) properties for 
 * Bezier tweens, you can use any properties (even ones that are function-based getters/setters). </p>
 * 
 * <p>Inside the <code>bezier</code> object, you must define at least a <code>values</code> property, and there are 
 * several other optional special properties that the BezierPlugin will recognize. Here is a list of them all:</p>
 * 
 * <ul>
 * 		<li><strong>values</strong> : Array <i>[REQUIRED]</i> - the array of your Bezier values as generic objects 
 * 				(or <code>Point</code> instances). Each object in the array should have matching property names 
 * 				(like "x" and "y"). For example, the array might look like:
 * 				<code>[{x:100, y:250}, {x:300, y:0}, {x:500, y:400}]</code></li>
 * 
 * 		<li><strong>type</strong> : String (default:<code>"thru"</code>) - Either <code>"thru", "thruBasic", "soft", "quadratic",</code>
 * 				or <code>"cubic"</code> as described above, indicating how the <code>values</code> should be interpreted.</li>
 * 
 * 		<li><strong>timeResolution</strong> : int (default:6) - due to the nature of Beziers, plotting the progression
 * 				of an object on the path over time can make it appear to speed up or slow down based on the placement
 * 				of the control points and the length of each successive segment on the path, so BezierPlugin implements
 * 				a technique that reduces or eliminates that variance, but it involves breaking the segments down into 
 * 				a certain number of pieces which is what <code>timeResolution</code> controls. The greater the number,
 * 				the more accurate the time remapping but there is a processing price to pay for greater precision.
 * 				The default value of 6 is typically fine, but if you notice slight pace changes on the path you can increase
 * 				the <code>timeResolution</code> value. Or, if you want to prioritize speed you could reduce the number. 
 * 				If you use a <code>timeResolution</code> value of 0, no length measurements will take place internally which
 * 				delivers maximum processing speed, but you may notice changes in speed during the animation.</li>
 * 
 * 		<li><strong>curviness</strong> : Number (default:1) (only applies to <code>type:"thru"</code>) - allows you to adjust the 
 * 				tension on the Bezier where 0 has no curviness (straight lines), 1 is normal curviness, 2 is twice 
 * 				the normal curviness, etc.</li>
 * 
 * 		<li><strong>autoRotate</strong> : Boolean or Array (default:false) - to automatically rotate the target according
 * 				to its position on the Bezier path, you can use the <code>autoRotate</code> feature (previously called 
 * 				<code>orientToBezier</code>). If your Bezier is affecting the "x" and "y" properties of your target
 * 				and you don't need to offset the rotation by a certain amount more than normal, then you can simply 
 * 				set <code>autoRotate:true</code>. Or if you want to offset the rotation by a certain amount (in degrees), 
 * 				you can define a number like <code>autoRotate:90</code> (adding 90 degrees in this example). Or for more 
 * 				advanced controls, you can define <code>autoRotate</code> as an array. In order to adjust a rotation 
 * 				property accurately, the plugin needs 5 pieces of information:
 * 				<ol>
 * 					<li> Position property 1 (typically <code>"x"</code>)</li>
 * 					<li> Position property 2 (typically <code>"y"</code>)</li>
 * 					<li> Rotational property (typically <code>"rotation"</code>)</li>
 * 					<li> Number of degrees (or radians) to add to the new rotation (optional - makes it easy to orient your target properly)</li>
 * 					<li> Boolean value indicating whether or not the rotational property should be defined in radians rather than degrees (default is <code>false</code> which results in degrees)</li>
 * 				</ol>
 * 				The <code>autoRotate</code> property should be an Array containing these values, like 
 * 				<code>["x","y","rotation",90,false]</code>. And if you need to affect multiple rotational
 * 				properties (like in 3D tweens where the Bezier is going through x,y,z points which could affect rotationX, rotationY, and rotationZ), 
 * 				you can use an array of arrays, like 
 * 				<code>[["x","y","rotationZ",0,false], ["z","x","rotationY",0,false], ["z","y","rotationX",0,false]]</code>.</li>
 * 
 * 		<li><strong>correlate</strong> : String (default:"x,y,z") (only applies to <code>type:"thru"</code>) - 
 * 				a comma-delimited list of property names whose relative distances should be correlated when calculating 
 * 				the Bezier that travels through the points. Since x, y, and z are all spacial, it is almost always good
 * 				to correlate them, but properties like scaleX, scaleY, etc. don't typically need to be correlated.
 * 				It is rarely necessary to alter the default <code>correlate</code> value.</li>
 * </ul>
 * 
 * 
 * <strong>SYNTAX</strong>
 * <listing version="3.0">
//animate obj through the points in the array (notice we're passing the array directly to the bezier rather than creating an object with "values" because we're accepting the defaults)
TweenMax.to(obj, 5, {bezier:[{x:100, y:250}, {x:300, y:0}, {x:500, y:400}], ease:Power1.easeInOut});
 
//if we want to customize things, like the curviness and setting autoRotate:true, we need to define the bezier as an object instead, and pass our array as the "values" property
TweenMax.to(obj, 5, {bezier:{curviness:1.25, values:[{x:100, y:250}, {x:300, y:0}, {x:500, y:400}], autoRotate:true}, ease:Power1.easeInOut});

//let's define the type as "soft" instead of using the default "thru"
TweenMax.to(obj, 5, {bezier:{type:"soft", values:[{x:100, y:250}, {x:300, y:0}, {x:500, y:400}], autoRotate:true}, ease:Power1.easeInOut});
 
//now we'll do a cubic Bezier and make our target auto rotate but add 45 degrees to the rotation
TweenMax.to(obj, 5, {bezier:{type:"cubic", values:[{x:100, y:250}, {x:150, y:100}, {x:300, y:500}, {x:500, y:400}], autoRotate:["x","y","rotation",45,false]}, ease:Power1.easeInOut});
</listing>
 * 
 * <p>You can tap into BezierPlugin's Bezier drawing algorithm by passing its <code>bezierThrough()</code> method your
 * array of points/objects and it will spit back and object with all the necessary data, either in Cubic Bezier 
 * form or in Quadratic Bezier form so that you could, for example, draw the path using Flash's curveTo() functionality.
 * It also has some useful static <code>cubicToQuadratic()</code> and <code>quadraticToCubic()</code> conversion methods.</p>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class BezierPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		
		/** @private precalculated for speed **/
		protected static const _RAD2DEG:Number = 180 / Math.PI; 
		/** @private temporary storage for bezierThrough calculations. ratio 1 (p1) **/
		protected static var _r1:Array = []; 
		/** @private temporary storage for bezierThrough calculations. ratio 2 (p1 + p2) **/
		protected static var _r2:Array = []; 
		/** @private temporary storage for bezierThrough calculations. **/
		protected static var _r3:Array = []; 
		/** @private used to store a boolean value indicating whether or not a particular property should be correlated. Basically a lookup table to speed things up. This allows us to avoid garbage collection headaches because bezierThrough() might be called a LOT in an app, thus creating a temporary local variable each time in the method would be more problematic. **/
		protected static var _corProps:Object = {};
		
		/** @private **/
		protected var _target:Object;
		/** @private **/
		protected var _autoRotate:Array;
		/** @private If the values should be rounded to the nearest integer, <code>_round</code> will be set to <code>true</code>. **/
		protected var _round:Object;
		/** @private array containing the numeric length of each segment, like [3, 5, 19, 2] **/
		protected var _lengths:Array;
		/** @private array containing arrays of length values for each segment, like [[2,4,12,56], [3,6,23,45,3]] (all arrays will contain the same number of elements, determined by "precision") **/
		protected var _segments:Array;
		/** @private approximate total length of all Bezier segments combined **/
		protected var _length:Number;
		/** @private a lookup table to figure out if a property is a function or not **/
		protected var _func:Object;
		/** @private array of properties that are being tweened, like ["x","y"] **/
		protected var _props:Array;
		/** @private the lower (minimum) threshold that still applies to the current segment. Like if the entire group of Beziers is 100 long, the first one might be from 0 to 50, the next 50 to 70, and the last 70 to 100, so _l1 would be 0, 50, or 70 depending on which segment was rendered last (this is like a caching mechanism that enhances performance and avoids lookups in many situations) **/
		protected var _l1:Number; //length 1 (lower)
		/** @private the upper (maximum) threshold that still applies to the current segment. Like if the entire group of Beziers is 100 long, the first one might be from 0 to 50, the next 50 to 70, and the last 70 to 100, so _l2 would be 50, 70, or 100 depending on which segment was rendered last (this is like a caching mechanism that enhances performance and avoids lookups in many situations) **/
		protected var _l2:Number; //length 2 (upper)
		/** @private the index number of the current segment (from the _lengths array) **/
		protected var _li:Number; //length index
		/** @private the current array of segment lengths from the _segments array. **/
		protected var _curSeg:Array; //segment array
		/** @private the lower (minimum) threshold that still applies to the current segment length from inside the _curSeg array. Like if the current segment is 100 long, the first measurement might be from 0 to 50, the next 50 to 70, and the last 70 to 100, so _s1 would be 0, 50, or 70 depending on which segment piece was rendered last (this is like a caching mechanism that enhances performance and avoids lookups in many situations) **/
		protected var _s1:Number; //segment 1 (lower)
		/** @private the upper (maximum) threshold that still applies to the current segment length from inside the _curSeg array. Like if the current segment is 100 long, the first measurement might be from 0 to 50, the next 50 to 70, and the last 70 to 100, so _s2 would be 50, 70, or 100 depending on which segment piece was rendered last (this is like a caching mechanism that enhances performance and avoids lookups in many situations) **/
		protected var _s2:Number; //segment 2 (upper)
		/** @private  the index number of the current segment length from _curSeg **/
		protected var _si:Number; //segment index;
		/** @private **/
		protected var _beziers:Object;
		/** @private total number of segments **/
		protected var _segCount:int;
		/** @private 1 / precision (precalculated for speed) **/
		protected var _prec:Number; //precision
		/** @private **/
		protected var _timeRes:int;
		/** @private we need to store the initial rotation for autoRotate tweens so that if/when the tween is rewound completely, the original value gets re-applied. **/
		protected var _initialRotations:Array;
		/** @private we determine the starting ratio when the tween inits which is always 0 unless the tween has runBackwards:true (which indicates it's a from() tween) in which case it's 1. **/
		protected var _startRatio:int;
		
		
		/** @private **/
		public function BezierPlugin() {
			super("bezier");
			this._overwriteProps.pop();
			this._func = {};
			this._round = {};
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			this._target = target;
			var vars:Object = (value is Array) ? {values:value} : value;
			this._props = [];
			this._timeRes = (vars.timeResolution == null) ? 6 : int(vars.timeResolution);
			var values:Array = vars.values || [],
				first:Object = {},
				second:Object = values[0],
				autoRotate:Object = vars.autoRotate || tween.vars.orientToBezier,
				p:String, isFunc:Boolean, i:int, j:int, ar:Array, prepend:Object;
			
			this._autoRotate = autoRotate ? (autoRotate is Array) ? autoRotate as Array : [["x","y","rotation",((autoRotate === true) ? 0 : Number(autoRotate))]] : null;
			
			if (second is Point) {
				this._props = ["x","y"];
			} else {
				for (p in second) {
					this._props.push(p);
				}
			}
			
			i = this._props.length;
			while (--i > -1) {
				p = this._props[i];
				this._overwriteProps.push(p);
				isFunc = this._func[p] = (target[p] is Function);
				first[p] = (!isFunc) ? target[p] : target[ ((p.indexOf("set") || !("get" + p.substr(3) in target)) ? p : "get" + p.substr(3)) ]();
				if (!prepend) if (first[p] !== values[0][p]) {
					prepend = first;
				}
			}
			this._beziers = (vars.type !== "cubic" && vars.type !== "quadratic" && vars.type !== "soft") ? bezierThrough(values, isNaN(vars.curviness) ? 1 : vars.curviness, false, (vars.type === "thruBasic"), vars.correlate || "x,y,z", prepend) : _parseBezierData(values, vars.type, first);
			this._segCount = this._beziers[p].length;
			
			if (this._timeRes) {
				var ld:Object = _parseLengthData(this._beziers, this._timeRes);
				this._length = ld.length;
				this._lengths = ld.lengths;
				this._segments = ld.segments;
				this._l1 = this._li = this._s1 = this._si = 0;
				this._l2 = this._lengths[0];
				this._curSeg = this._segments[0];
				this._s2 = this._curSeg[0];
				this._prec = 1 / this._curSeg.length;
			}
			
			if ((ar = this._autoRotate)) {
				this._initialRotations = [];
				if (!(ar[0] is Array)) {
					this._autoRotate = ar = [ar];
				}
				i = ar.length;
				while (--i > -1) {
					for (j = 0; j < 3; j++) {
						p = ar[i][j];
						this._func[p] = (target[p] is Function) ? target[ ((p.indexOf("set") || !("get" + p.substr(3) in target)) ? p : "get" + p.substr(3)) ] : false;
					}
					p = ar[i][2];
					this._initialRotations[i] = this._func[p] ? this._func[p]() : this._target[p];
				}
			}
			_startRatio = tween.vars.runBackwards ? 1 : 0;
			return true;
		}
		
		/**
		 * Takes an array that contains objects (could be Points, could be generic objects with
		 * any number of properties but they should all match in terms of the names of properties like
		 * <code>[{x:0, y:0, scaleX:0.5}, {x:100, y:-200, scaleX:1.2}, {x:300, y:20, scaleX:0.8}]</code>) and plots Bezier 
		 * segments THROUGH those values and returns an array containing a generic object for each Bezier segment. By default
		 * Cubic Beziers (which use 2 control points per segment) are used but you can optionally request Quadratic Beziers (1 control
		 * point per segment) instead using the <code>quadratic</code> parameter.
		 * 
		 * <p>For Cubic Beziers (the default), each segment object will have <code>a, b, c,</code> and <code>d</code> properties:</p>
		 * 
		 * <ul>
		 * 		<li><strong>a</strong> - the starting anchor value of the Cubic Bezier segment. For example, 
		 * 					<code>bezierThrough([{x:0, y:0, scaleX:0.5}, {x:100, y:-200, scaleX:1.2}, {x:300, y:20, scaleX:0.8}]);</code>
		 * 					would return an object with "x", "y", and "scaleX" properties, each containing an array of objects, one per Bezier segment and you could
		 * 					access the first Bezier's initial anchor values like:
		 * 					<code>myReturnedObject.x[0].a, myReturnedObject.y[0].a</code>, and <code>myReturnedObject.scaleX[0].a</code></li>
		 * 		<li><strong>b</strong> - the first control point value of the Cubic Bezier segment. For example, 
		 * 					<code>bezierThrough([{x:0, y:0, scaleX:0.5}, {x:100, y:-200, scaleX:1.2}, {x:300, y:20, scaleX:0.8}]);</code>
		 * 					would return an object with "x", "y", and "scaleX" properties, each containing an array of objects, one per Bezier segment and you could
		 * 					access the first Bezier's first control point values like:
		 * 					<code>myReturnedObject.x[0].b, myReturnedObject.y[0].b</code>, and <code>myReturnedObject.scaleX[0].b</code></li>
		 * 		<li><strong>c</strong> - the second control point value of the Cubic Bezier segment. For example, 
		 * 					<code>bezierThrough([{x:0, y:0, scaleX:0.5}, {x:100, y:-200, scaleX:1.2}, {x:300, y:20, scaleX:0.8}]);</code>
		 * 					would return an object with "x", "y", and "scaleX" properties, each containing an array of objects, one per Bezier segment and you could
		 * 					access the first Bezier's second control point values like:
		 * 					<code>myReturnedObject.x[0].c, myReturnedObject.y[0].c</code>, and <code>myReturnedObject.scaleX[0].c</code></li>
		 * 		<li><strong>d</strong> - the final anchor value of the Cubic Bezier segment. For example, 
		 * 					<code>bezierThrough([{x:0, y:0, scaleX:0.5}, {x:100, y:-200, scaleX:1.2}, {x:300, y:20, scaleX:0.8}]);</code>
		 * 					would return an object with "x", "y", and "scaleX" properties, each containing an array of objects, one per Bezier segment and you could
		 * 					access the first Bezier's final anchor values like:
		 * 					<code>myReturnedObject.x[0].d, myReturnedObject.y[0].d</code>, and <code>myReturnedObject.scaleX[0].d</code></li>
		 * </ul>
		 * 
		 * 
		 * <p>If you set the <code>quadratic</code> parameter to <code>true</code>, all of the Bezier segments will contain <code>a, b,</code>
		 * and <code>c</code> properties (<strong>NOT</strong> <code>d</code>) where <code>b</code> is the only control point. This can be 
		 * very useful because some drawing APIs only understand Quadratic Beziers. There are 4 times as many Quadratic Beziers returned as
		 * Cubic Beziers, though, due to the fact that the internal algorithm uses Cubic Beziers to plot the points (they're much more flexible) 
		 * and then splits each into 4 Quadratic ones.</p>
		 * 
		 * <listing version="3.0">
 //input:
 var beziers:Object = BezierPlugin.bezierThrough([{x:0, y:0}, {x:250, y:400}, {x:500, y:0}]);
 
 //output:
 {
 	x:[{a:0, b:0, c:125, d:250}, {a:250, b:375, c:500, d:500}],
 	y:[{a:0, b:0, c:400, d:400}, {a:400, b:400, c:0, d:0}]
 }</listing>
		 * 
		 * 
		 * <listing version="3.0">
 //get quadratic beziers so that we can use Flash's drawing API...
 var beziers:Object = BezierPlugin.bezierThrough([{x:0, y:0}, {x:250, y:400}, {x:500, y:0}], 1, true);
 
 var bx:Array = beziers.x; //the "x" Beziers
 var by:Array = beziers.y; //the "y" Beziers
 
 //draw the curve in Flash using AS3:
 var g:Graphics = this.graphics;
 g.moveTo(bx[0].a, by[0].a);
 for (var i:int = 0; i &lt; bx.length; i++) {
 	g.curveTo(bx[i].b, by[i].b, bx[i].c, by[i].c);
 }
</listing>
		 * 
		 * 
		 * @param values An array containing generic objects with matching properties (or Point instances) through which the Beziers should be plotted, like <code>[{x:0, y:0, scaleX:0.5}, {x:100, y:-200, scaleX:1.2}, {x:300, y:20, scaleX:0.8}]</code>
		 * @param curviness A number (default: 1) that controls the strength of the curves that are plotted through the values. A curviness of 0 would be result in straight lines, 1 is normal curviness, and 2 would be extreme curves. Use any value.
		 * @param quadratic if <code>true</code>, quadratic Bezier information will be returned instead of cubic Bezier data, thus each object in the returned array will only contain a, b, and c properties where b is the control point.
		 * @param basic if <code>true</code>, a faster algorithm will be used for calculating the control points which can be less aesthetically pleasing in situations where there are large differences in the spaces or angles between the values provided (the curves are more likely to get kinks or harsh angles)
		 * @param correlate [optional] a comma-delimited list of property names whose relative distances should be correlated with each other when calculating the curvature of the Bezier through the values (the default is <code>"x,y,z"</code> because those are almost always properties that should be correlated).
		 * @param prepend [optional] an object to treat as though it is the first element in the <code>values</code> array (typically only used internally for adding a tween's starting values)
		 * @return An object with properties matching those from the objects in the <code>values</code> array, with an array assigned to each property populated with an object for each Bezier. The Bezier objects will contain <code>a, b, c</code> (and <code>d</code> if <code>quadratic</code> is not <code>true</code>) properties for the anchors and control points.
		 */
		public static function bezierThrough(values:Array, curviness:Number=1, quadratic:Boolean=false, basic:Boolean=false, correlate:String="x,y,z", prepend:Object=null):Object {
			var obj:Object = {},
				first:Object = prepend || values[0],
				props:Array, i:int, p:String, j:int, a:Array, l:int, r:Number, seamless:Boolean, last:Object;
			correlate = ","+correlate+",";
			if (first is Point) {
				props = ["x","y"];
			} else {
				props = [];
				for (p in first) {
					props.push(p);
				}
			}
			//check to see if the last and first values are identical (well, within 0.05). If so, make seamless by appending the second element to the very end of the values array and the 2nd-to-last element to the very beginning (we'll remove those segments later)
			if (values.length > 1) {
				last = values[values.length - 1];
				seamless = true;
				i = props.length;
				while (--i > -1) {
					p = props[i];
					if (Math.abs(first[p] - last[p]) > 0.05) { //build in a tolerance of +/-0.05 to accommodate rounding errors. For example, if you set an object's position to 4.945, Flash will make it 4.9
						seamless = false;
						break;
					}
				}
				if (seamless) {
					values = values.concat(); //duplicate the array to avoid contaminating the original which the user may be reusing for other tweens
					if (prepend) {
						values.unshift(prepend);
					}
					values.push(values[1]);
					prepend = values[values.length - 3];
				}
			}
			_r1.length = _r2.length = _r3.length = 0;
			i = props.length;
			while (--i > -1) {
				p = props[i];
				_corProps[p] = (correlate.indexOf(","+p+",") !== -1);
				obj[p] = _parseAnchors(values, p, _corProps[p], prepend);
			}
			i = _r1.length;
			while (--i > -1) {
				_r1[i] = Math.sqrt(_r1[i]);
				_r2[i] = Math.sqrt(_r2[i]);
			}
			if (!basic) {
				i = props.length;
				while (--i > -1) {
					if (_corProps[p]) {
						a = obj[props[i]];
						l = a.length - 1;
						for (j = 0; j < l; j++) {
							r = a[j+1].da / _r2[j] + a[j].da / _r1[j]; 
							_r3[j] = (_r3[j] || 0) + r * r;
						}
					}
				}
				i = _r3.length;
				while (--i > -1) {
					_r3[i] = Math.sqrt(_r3[i]);
				}
			}
			i = props.length;
			j = quadratic ? 4 : 1;
			while (--i > -1) {
				p = props[i];
				a = obj[p];
				_calculateControlPoints(a, curviness, quadratic, basic, _corProps[p]); //this method requires that _parseAnchors() and _setSegmentRatios() ran first so that _r1, _r2, and _r3 values are populated for all properties
				if (seamless) {
					a.splice(0, j);
					a.splice(a.length - j, j);
				}
			}
			return obj;
		}
		
		/** @private parses the bezier data passed into the tween and organizes it into the appropriate format with an array for each property. **/
		public static function _parseBezierData(values:Array, type:String, prepend:Object=null):Object {
			type = type || "soft";
			var obj:Object = {},
				inc:int = (type === "cubic") ? 3 : 2,
				soft:Boolean = (type === "soft"),
				a:Number, b:Number, c:Number, d:Number, cur:Array, props:Array, i:int, j:int, l:int, p:String, cnt:int, tmp:Object;
			if (soft && prepend) {
				values = [prepend].concat(values);
			}
			if (values == null || values.length < inc + 1) { throw new Error("invalid Bezier data"); }
			if (values[1] is Point) {
				props = ["x","y"];
			} else {
				props = [];
				for (p in values[0]) {
					props.push(p);
				}
			}
			
			i = props.length;
			while (--i > -1) {
				p = props[i];
				obj[p] = cur = [];
				cnt = 0;
				l = values.length;
				for (j = 0; j < l; j++) {
					a = (prepend == null) ? values[j][p] : (typeof( (tmp = values[j][p]) ) === "string" && tmp.charAt(1) === "=") ? prepend[p] + Number(tmp.charAt(0) + tmp.substr(2)) : Number(tmp);
					if (soft) if (j > 1) if (j < l - 1) {
						cur[cnt++] = (a + cur[cnt-2]) / 2;
					}
					cur[cnt++] = a;
				}
				l = cnt - inc + 1;
				cnt = 0;
				for (j = 0; j < l; j += inc) {
					a = cur[j];
					b = cur[j+1];
					c = cur[j+2];
					d = (inc === 2) ? 0 : cur[j+3];
					cur[cnt++] = (inc === 3) ? new Segment(a, b, c, d) : new Segment(a, (2 * b + a) / 3, (2 * b + c) / 3, c);
				}
				cur.length = cnt;
			}
			return obj;
		}
		
		/**
		 * @private 
		 * Takes a "values" array that contains objects (could be Points, could be generic objects with
		 * any number of properties but they should all match in terms of the names of properties like
		 * <code>[{x:100, y:200, rotation:20},{x:30, y:10, rotation:290}]</code>) and populates an array
		 * with a generic object for each cubic Bezier segment, adding only the <code>a</code> and <code>d</code>
		 * properties (which are the beginning and ending anchors). We don't populate the control points yet because
		 * we must first loop through all of the properties for each segment so that we can determine the relative
		 * distances between each point which will determine the ratios we use to correctly weight the control 
		 * points on each side. The goal is to use an algorithm that keeps the handle tighter/shorter the closer 
		 * it is to the next control point. Imagine an anchor where on one side there's a very short segment and on
		 * the other side a very long one - we must determine all the relative changes for the properties (like
		 * x and y rather than only x or only y) and then leverage that to get the totals and assign the correct
		 * ratio. 
		 * 
		 * @param values An array containing generic objects with matching properties (or Point instances) through which the Beziers should be plotted, like <code>[{x:0, y:0, scaleX:0.5}, {x:100, y:-200, scaleX:1.2}, {x:300, y:20, scaleX:0.8}]</code>
		 * @param p Property name that the method should focus on (like "x" or "y" or "scaleX" or whatever)
		 * @param correlate if <code>true</code>, this property's relative distances will be recorded internally so that they can be correlated with others when calculating the curvature of the Bezier through the values (typically x, y, and z properties should be correlated).
		 * @param prepend An object to treat as though it is the first element in the <code>values</code> array (typically only used internally for adding a tween's starting values)
		 * @return An array of partially populated Bezier data (only "a" and "d" properties)
		 */
		protected static function _parseAnchors(values:Array, p:String, correlate:Boolean, prepend:Object):Array {
			var a:Array = [],
				l:int, i:int, p1:Number, p2:Number, p3:Number, tmp:Object;
			if (prepend) {
				values = [prepend].concat(values);
				i = values.length;
				while (--i > -1) {
					if (typeof( (tmp = values[i][p]) ) === "string") if (tmp.charAt(1) === "=") {
						values[i][p] = prepend[p] + Number(tmp.charAt(0) + tmp.substr(2)); //accommodate relative values. Do it inline instead of breaking it out into a function for speed reasons
					}
				}
			}
			
			l = values.length - 2;
			if (l < 0) {
				a[0] = new Segment(values[0][p], 0, 0, values[(l < -1) ? 0 : 1][p]);
				return a;
			}
			
			for (i = 0; i < l; i++) {
				p1 = values[i][p];
				p2 = values[i+1][p];
				a[i] = new Segment(p1, 0, 0, p2);
				if (correlate) {
					p3 = values[i+2][p];
					_r1[i] = (_r1[i] || 0) + (p2 - p1) * (p2 - p1);
					_r2[i] = (_r2[i] || 0) + (p3 - p2) * (p3 - p2); 
				}
			}
			a[i] = new Segment(values[i][p], 0, 0, values[i+1][p]);
			return a;
		}
		
		/**
		 * @private 
		 * [Note: must run <code>_parseAnchors()</code> on all properties first to generate the <code>a</code> array with the start/end anchors and assign the r1 and r2 ratio values]
		 * Iterates through an array of cubic Bezier-related data generated by <code>_parseAnchors()</code> and assigns the control
		 * point values (b and c) for them according to a particular "curviness" amount.
		 * 
		 * @param a An array that has already been populated by <code>_parseAnchors()</code> with start/end anchors and r1/r2 ratio values for all properties. Each object in the array should have a, b, c, d, r1, and r2 properties.
		 * @param curviness A number (typically between 0 and 1, and by default 0.5) that controls the strength of the curves that are plotted.
		 * @param quad If <code>true</code>, Quadratic Beziers will be used instead of Cubic Beziers.
		 * @param basic if <code>true</code>, a faster algorithm will be used for calculating the control points which can be less aesthetically pleasing in situations where there are large differences in the spaces or angles between the values provided (the curves are more likely to get kinks or harsh angles)
		 * @param correlate if <code>true</code>, this property's relative distances will be correlated with others when calculating the curvature of the Bezier through the values (typically x, y, and z properties should be correlated).
		 */
		protected static function _calculateControlPoints(a:Array, curviness:Number=1, quad:Boolean=false, basic:Boolean=false, correlate:Boolean=false):void {
			var l:int = a.length - 1,
				ii:int = 0,
				cp1:Number = a[0].a,
				i:int, p1:Number, p2:Number, p3:Number, seg:Segment, m1:Number, m2:Number, mm:Number, cp2:Number, qb:Array, r1:Number, r2:Number, tl:Number;
			for (i = 0; i < l; i++) {
				seg = a[ii];
				p1 = seg.a;
				p2 = seg.d;
				p3 = a[ii+1].d;
				
				if (correlate) {
					r1 = _r1[i];
					r2 = _r2[i];
					tl = ((r2 + r1) * curviness * 0.25) / (basic ? 0.5 : _r3[i] || 0.5);
					m1 = p2 - (p2 - p1) * (basic ? curviness * 0.5 : (r1 !== 0 ? tl / r1 : 0));
					m2 = p2 + (p3 - p2) * (basic ? curviness * 0.5 : (r2 !== 0 ? tl / r2 : 0));
					mm = p2 - (m1 + (((m2 - m1) * ((r1 * 3 / (r1 + r2)) + 0.5) / 4) || 0));
				} else {
					m1 = p2 - (p2 - p1) * curviness * 0.5;
					m2 = p2 + (p3 - p2) * curviness * 0.5;
					mm = p2 - (m1 + m2) / 2;
				}
				m1 += mm;
				m2 += mm;
				
				seg.c = cp2 = m1; 
				if (i != 0) {
					seg.b = cp1;
				} else {
					seg.b = cp1 = seg.a + (seg.c - seg.a) * 0.6; //instead of placing b on a exactly, we move it inline with c so that if the user specifies an ease like Back.easeIn or Elastic.easeIn which goes BEYOND the beginning, it will do so smoothly.
				}
				
				seg.da = p2 - p1;
				seg.ca = cp2 - p1;
				seg.ba = cp1 - p1;
				
				if (quad) {
					qb = cubicToQuadratic(p1, cp1, cp2, p2);
					a.splice(ii, 1, qb[0], qb[1], qb[2], qb[3]);
					ii += 4;
				} else {
					ii++;
				}
				
				cp1 = m2;
			}
			seg = a[ii];
			seg.b = cp1;
			seg.c = cp1 + (seg.d - cp1) * 0.4; //instead of placing c on d exactly, we move it inline with b so that if the user specifies an ease like Back.easeOut or Elastic.easeOut which goes BEYOND the end, it will do so smoothly.
			seg.da = seg.d - seg.a;
			seg.ca = seg.c - seg.a;
			seg.ba = cp1 - seg.a;
			if (quad) {
				qb = cubicToQuadratic(seg.a, cp1, seg.c, seg.d);
				a.splice(ii, 1, qb[0], qb[1], qb[2], qb[3]);
			}
		}
		
		/**
		 * Using the fixed midpoint approach, we return an array of 4 quadratic Beziers that 
		 * closely approximates the cubic Bezier data provided. Each quadratic Bezier object contains 
		 * <code>a, b,</code> and <code>c</code> properties where <code>a</code> is the starting anchor value, 
		 * <code>b</code> is the control point, and <code>c</code> is the ending anchor value.
		 * 
		 * @param a starting anchor of the cubic Bezier
		 * @param b first control point of the cubic Bezier
		 * @param c second control point of the cubic Bezier
		 * @param d final anchor of the cubic Bezier
		 * @return an array of 4 objects, one for each quadratic Bezier with a, b, and c properties
		 */
		public static function cubicToQuadratic(a:Number, b:Number, c:Number, d:Number):Array {
			var q1:Object = {a:a},
				q2:Object = {},
				q3:Object = {},
				q4:Object = {c:d},
				mab:Number = (a + b) / 2, 
				mbc:Number = (b + c) / 2, 
				mcd:Number = (c + d) / 2, 
				mabc:Number = (mab + mbc) / 2,
				mbcd:Number = (mbc + mcd) / 2,
				m8:Number = (mbcd - mabc) / 8;
			q1.b = mab + (a - mab) / 4;	
			q2.b = mabc + m8;
			q1.c = q2.a = (q1.b + q2.b) / 2;
			q2.c = q3.a = (mabc + mbcd) / 2;
			q3.b = mbcd - m8;
			q4.b = mcd + (d - mcd) / 4;
			q3.c = q4.a = (q3.b + q4.b) / 2;
			return [q1, q2, q3, q4];
		}
		
		/**
		 * Returns the Cubic equivalent of a Quadratic Bezier. This method returns an object with a, b, c, and d properties 
		 * representing the starting anchor value (a), first control point (b), second control point (c), and ending anchor value (d) 
		 * of a Cubic Bezier matching the Quadratic Bezier data passed in.
		 * 
		 * @param a The starting anchor value
		 * @param b The control point value
		 * @param c The ending anchor value
		 * @return An object with a, b, c, and d properties representing the starting anchor value (a), first control point (b), second control point (c), and ending anchor value (d) of a Cubic Bezier matching the Quadratic Bezier data passed in.
		 */
		public static function quadraticToCubic(a:Number, b:Number, c:Number):Object {
			return new Segment(a, (2 * b + a) / 3, (2 * b + c) / 3, c);
		}
		
		/**
		 * @private 
		 * Analyzes the object in the form of <code>{x:[...bezier segments...], y:[...bezier segments...]}</code> (with any
		 * properties, not limited to only "x" and "y") and approximates the lengths of the segments, returning an object
		 * with "length" (total length of all segments), "lengths" (an array of the individual lengths of each segment), 
		 * and "segments" (an array containing an array for each segment - each of those arrays has the same number of elements
		 * as the <code>precision</code> parameter, each indicating how long the segment is up to that point) properties. 
		 * For example:
		 * 
		 * <p><code>{length:348.214, lengths:[20, 328.14], segments:[[2,4,8,10,14.14], [1,3,4.5,6,8.9]]}</code></p>
		 * 
		 * <p>The purpose of the arrays is to allow more accurate calculation of where progress points should land
		 * such that [almost] perfectly linear easing is possible on the Bezier(s). </p>
		 * 
		 * @param obj The object containing the arrays of Bezier data like <code>{x:[{a:1, b:2, c:3, d:4}], y:[{a:3, b:2, c:4, d:3}]}</code>
		 * @param precision Number of segments to use when determining the length of each Bezier segment (default 6)
		 * @return An object with "length", "lengths", and "segements" properties where "length" is the total length of all Bezier segments
		 */
		protected static function _parseLengthData(obj:Object, precision:uint=6):Object {
			var a:Array = [],
				lengths:Array = [],
				d:Number = 0,
				total:Number = 0,
				threshold:int = precision - 1,
				segments:Array = [],
				curLS:Array = [], //current length segments array
				p:String, i:int, l:int, index:Number;
			for (p in obj) {
				_addCubicLengths(obj[p], a, precision);
			}
			l = a.length;
			for (i = 0; i < l; i++) {
				d += Math.sqrt(a[i]);
				index = i % precision;
				curLS[index] = d;
				if (index == threshold) {
					total += d;
					index = (i / precision) >> 0;
					segments[index] = curLS;
					lengths[index] = total;
					d = 0;
					curLS = [];
				}
			}
			return {length:total, lengths:lengths, segments:segments};
		}
		
		/** @private Used by <code>_parseLengthData()</code> **/
		private static function _addCubicLengths(a:Array, steps:Array, precision:uint=6):void {
			var inc:Number = 1 / precision,
				j:int = a.length,
				d:Number, d1:Number, s:Number, da:Number, ca:Number, ba:Number, p:Number, i:int, inv:Number, bez:Segment, index:int;
			while (--j > -1) {
				bez = a[j];
				s = bez.a;
				da = bez.d - s;
				ca = bez.c - s;
				ba = bez.b - s;
				d = d1 = 0;
				for (i = 1; i <= precision; i++) {
					p = inc * i;
					inv = 1 - p;
					d = d1 - (d1 = (p * p * da + 3 * inv * (p * ca + inv * ba)) * p);
					index = j * precision + i - 1;
					steps[index] = (steps[index] || 0) + d * d;
				}
			}
		}
		
		/** @private **/
		override public function _kill(lookup:Object):Boolean {
			var a:Array = this._props, 
				p:String, i:int;
			for (p in _beziers) {
				if (p in lookup) {
					delete _beziers[p];
					delete _func[p];
					i = a.length;
					while (--i > -1) {
						if (a[i] === p) {
							a.splice(i, 1);
						}
					}
				}
			}
			return super._kill(lookup);
		}
		
		/** @private **/
		override public function _roundProps(lookup:Object, value:Boolean=true):void {
			var op:Array = this._overwriteProps,
				i:int = op.length;
			while (--i > -1) {
				if ((op[i] in lookup) || ("bezier" in lookup) || ("bezierThrough" in lookup)) {
					this._round[op[i]] = value;
				}
			}
		}
		
		
		/** @private **/
		override public function setRatio(v:Number):void {
			var segments:int = this._segCount,
				func:Object = this._func,
				target:Object = this._target,
				notStart:Boolean = (v !== this._startRatio),
				curIndex:int, inv:Number, i:int, p:String, b:Segment, t:Number, val:Number, l:int, lengths:Array, curSeg:Array;
			if (this._timeRes == 0) {
				curIndex = (v < 0) ? 0 : (v >= 1) ? segments - 1 : (segments * v) >> 0;
				t = (v - (curIndex * (1 / segments))) * segments;
			} else {
				lengths = this._lengths;
				curSeg = this._curSeg;
				v *= this._length;
				i = this._li;
				//find the appropriate segment (if the currently cached one isn't correct)
				if (v > this._l2 && i < segments - 1) {
					l = segments - 1;
					while (i < l && (this._l2 = lengths[++i]) <= v) {	}
					this._l1 = lengths[i-1];
					this._li = i;
					this._curSeg = curSeg = this._segments[i];
					this._s2 = curSeg[(this._s1 = this._si = 0)];
				} else if (v < this._l1 && i > 0) {
					while (i > 0 && (this._l1 = lengths[--i]) >= v) { 	}
					if (i === 0 && v < this._l1) {
						this._l1 = 0;
					} else {
						i++;
					}
					this._l2 = lengths[i];
					this._li = i;
					this._curSeg = curSeg = this._segments[i];
					this._s1 = curSeg[(this._si = curSeg.length - 1) - 1] || 0;
					this._s2 = curSeg[this._si];
				}
				curIndex = i;
				//now find the appropriate sub-segment (we split it into the number of pieces that was defined by "precision" and measured each one)
				v -= this._l1;
				i = this._si;
				if (v > this._s2 && i < curSeg.length - 1) {
					l = curSeg.length - 1;
					while (i < l && (this._s2 = curSeg[++i]) <= v) {	}
					this._s1 = curSeg[i-1];
					this._si = i;
				} else if (v < this._s1 && i > 0) {
					while (i > 0 && (this._s1 = curSeg[--i]) >= v) {	}
					if (i === 0 && v < this._s1) {
						this._s1 = 0;
					} else {
						i++;
					}
					this._s2 = curSeg[i];
					this._si = i;
				}
				t = (i + (v - this._s1) / (this._s2 - this._s1)) * this._prec;
			}
			inv = 1 - t;
			
			i = this._props.length;
			while (--i > -1) {
				p = this._props[i];
				b = this._beziers[p][curIndex];
				val = (t * t * b.da + 3 * inv * (t * b.ca + inv * b.ba)) * t + b.a;
				if (this._round[p]) {
					val = (val + ((val > 0) ? 0.5 : -0.5)) >> 0;
				}
				if (func[p]) {
					target[p](val);
				} else {
					target[p] = val;
				}
			}
			
			if (this._autoRotate != null) {
				var ar:Array = this._autoRotate,
					b2:Segment, x1:Number, y1:Number, x2:Number, y2:Number, add:Number, conv:Number;
				i = ar.length;
				while (--i > -1) {
					p = ar[i][2];
					add = ar[i][3] || 0;
					conv = (ar[i][4] == true) ? 1 : _RAD2DEG;
					b = this._beziers[ar[i][0]][curIndex];
					b2 = this._beziers[ar[i][1]][curIndex];
					
					x1 = b.a + (b.b - b.a) * t;
					x2 = b.b + (b.c - b.b) * t;
					x1 += (x2 - x1) * t;
					x2 += ((b.c + (b.d - b.c) * t) - x2) * t;
					
					y1 = b2.a + (b2.b - b2.a) * t;
					y2 = b2.b + (b2.c - b2.b) * t;
					y1 += (y2 - y1) * t;
					y2 += ((b2.c + (b2.d - b2.c) * t) - y2) * t;
					
					val = notStart ? Math.atan2(y2 - y1, x2 - x1) * conv + add : this._initialRotations[i];
					
					if (func[p]) {
						target[p](val);
					} else {
						target[p] = val;
					}
				}
			}
			
		}
		
	}
}

internal class Segment {
	public var a:Number;
	public var b:Number;
	public var c:Number;
	public var d:Number;
	public var da:Number;
	public var ca:Number;
	public var ba:Number;
	
	public function Segment(a:Number, b:Number, c:Number, d:Number) {
		this.a = a;
		this.b = b;
		this.c = c;
		this.d = d;
		this.da = d - a;
		this.ca = c - a;
		this.ba = b - a;
	}
	
}