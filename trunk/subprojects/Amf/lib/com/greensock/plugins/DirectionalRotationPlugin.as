/**
 * VERSION: 12.0.5
 * DATE: 2013-03-26
 * AS3 
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
	import com.greensock.core.PropTween;
/**
 * Tweens any rotation-related property to another value in a particular direction which can be either <strong>clockwise</strong> 
 * (<code>"_cw"</code> suffix), <strong>counter-clockwise</strong> (<code>"_ccw"</code> suffix), or in the shortest direction 
 * (<code>"_short"</code> suffix) in which case the plugin chooses the direction based on the shortest path. For example:
 * 
 * <listing version="3.0">
//obj.rotation starts at 45
var obj:Object = {rotation:45}; 
 
//tweens to the 270 position in a clockwise direction
TweenLite.to(obj, 1, {directionalRotation:"270_cw"}); 
 
//tweens to the 270 position in a counter-clockwise direction
TweenLite.to(obj, 1, {directionalRotation:"270_ccw"});
 
//tweens to the 270 position in the shortest direction (which, in this case, is counter-clockwise)
TweenLite.to(obj, 1, {directionalRotation:"270_short"});
</listing>
 * 
 * <p>Notice that the value is in quotes, thus a string with a particular suffix indicating the direction 
 * ("_cw", "_ccw", or "_short"). You can also use the <code>"+="</code> or <code>"-="</code> prefix to
 * indicate relative values.</p>
 * 
 * <p>By default, directionalRotation assumes you're attempting to tween the <code>"rotation"</code> property 
 * of the target, but you can define any rotational property name (including MULTIPLE properties) by passing an 
 * object instead, like this:</p>
 * 
 * <listing version="3.0">
//animate obj.rotationX and obj.rotationY:
TweenLite.to(obj, 1, {directionalRotation:{rotationX:"-140_cw", rotationY:"70_short"}, ease:Power2.easeIn});
</listing>
 * 
 * <p>If you want to define the values in radians instead of degrees, you can use the special <code>useRadians:true</code> flag, like this:</p>
 * 
 * <listing version="3.0">
TweenLite.to(obj, 1, {directionalRotation:{rotation:"1.5_ccw", useRadians:true}, ease:Power2.easeInOut});
</listing>
 * 
 * <p>And if the value that you want to pass in is a numeric variable, you can easily append the appropriate suffix like this:</p>
 * 
 * <listing version="3.0">
var myValue:Number = -270;
TweenLite.to(obj, 1, {directionalRotation: (myValue + "_short") });
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class DirectionalRotationPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		/** @private **/
		protected var finals:Object;
		
		/** @private **/
		public function DirectionalRotationPlugin() {
			super("directionalRotation");
			_overwriteProps.pop();
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			if (typeof(value) !== "object") {
				value = {rotation:value};
			}
			finals = {};
			var cap:Number = (value.useRadians === true) ? Math.PI * 2 : 360,
				p:String, v:Object, start:Number, end:Number, dif:Number, split:Array, type:String;
			for (p in value) {
				if (p !== "useRadians") {
					split = (value[p] + "").split("_");
					v = split[0];
					type = split[1];
					start = parseFloat( (typeof(target[p]) !== "function") ? target[p] : target[ ((p.indexOf("set") || typeof(target["get" + p.substr(3)]) !== "function") ? p : "get" + p.substr(3)) ]() );
					end = finals[p] = (typeof(v) === "string" && v.charAt(1) === "=") ? start + parseInt(v.charAt(0) + "1", 10) * Number(v.substr(2)) : Number(v) || 0;
					dif = end - start;
					if (type === "short") {
						dif = dif % cap;
						if (dif !== dif % (cap / 2)) {
							dif = (dif < 0) ? dif + cap : dif - cap;
						}
					} else if (type === "cw" && dif < 0) {
						dif = ((dif + cap * 9999999999) % cap) - ((dif / cap) | 0) * cap;
					} else if (type === "ccw" && dif > 0) {
						dif = ((dif - cap * 9999999999) % cap) - ((dif / cap) | 0) * cap;
					}
					_addTween(target, p, start, start + dif, p);
					_overwriteProps.push(p);
				}
			}
			return true;
		}
		
		/** @private **/
		override public function setRatio(v:Number):void {
			var pt:PropTween;
			if (v !== 1) {
				super.setRatio(v);
			} else {
				pt = _firstPT;
				while (pt) {
					if (pt.f) {
						pt.t[pt.p](finals[pt.p]);
					} else {
						pt.t[pt.p] = finals[pt.p];
					}
					pt = pt._next;
				}
			}
		}	

	}
}