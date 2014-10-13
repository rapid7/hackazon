/**
 * VERSION: 12.0
 * DATE: 2012-01-12
 * AS3 
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
	import com.greensock.motionPaths.CirclePath2D;
	import com.greensock.motionPaths.PathFollower;
	import flash.geom.Matrix;
/**
 * [AS3 only] Tweens an object along a CirclePath2D motion path in any direction (clockwise, counter-clockwise, or shortest).
 * The plugin recognizes the following properties:
 * <ul>
 * 		<li><b>path</b> : CirclePath2D -  The CirclePath2D instance to follow (com.greensock.motionPaths.CirclePath2D)</li>
 * 		<li><b>startAngle</b> : Number - The position at which the target should begin its rotation (described 
 * 							   in degrees unless useRadians is true in which case it is described in radians). 
 * 							   For example, to begin at the top of the circle, use 270 or -90 as the startAngle.</li>
 * 		<li><b>endAngle</b> : Number - The position at which the target should end its rotation (described in
 * 							 degrees unless useRadians is true in which case it is described in radians).
 * 							 For example, to end at the bottom of the circle, use 90 as the endAngle</li>
 * 		<li><b>autoRotate</b> : Boolean - When <code>autoRotate</code> is <code>true</code>, the target will automatically 
 * 							be rotated so that it is oriented to the angle of the path. To offset this value (like to always add 
 * 							90 degrees for example), use the <code>rotationOffset</code> property.</li>
 * 		<li><b>rotationOffset</b> : Number - When <code>autoRotate</code> is <code>true</code>, this value will always 
 * 							be added to the resulting <code>rotation</code> of the target.</li>
 * 		<li><b>direction</b> : String - The direction in which the target should travel around the path. Options are
 * 							  <code>Direction.CLOCKWISE</code> ("clockwise"), <code>Direction.COUNTER_CLOCKWISE</code>
 * 							 ("counterClockwise"), or <code>Direction.SHORTEST</code> ("shortest").</li>
 * 		<li><b>extraRevolutions</b> : uint - If instead of going directly to the endAngle, you want the target to
 * 									 travel one or more extra revolutions around the path before going to the endAngle, 
 * 									 define that number of revolutions here. </li>
 * 		<li><b>useRadians</b> : Boolean - If you prefer to define values in radians instead of degrees, set useRadians to true.</li>
 * </ul>
 * 
 * 
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.~~; 
import com.greensock.plugins.~~;
import com.greensock.motionPaths.~~;
TweenPlugin.activate([CirclePath2DPlugin]); //activation is permanent in the SWF, so this line only needs to be run once.

var circle:CirclePath2D = new CirclePath2D(150, 150, 100);
TweenLite.to(mc, 2, {circlePath2D:{path:circle, startAngle:90, endAngle:270, direction:Direction.CLOCKWISE, extraRevolutions:2}});
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class CirclePath2DPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		/** @private **/
		private static const _2PI:Number = Math.PI * 2;
		/** @private **/
		private static const _RAD2DEG:Number = 180 / Math.PI;
		
		/** @private **/
		protected var _target:Object;
		/** @private **/
		protected var _autoRemove:Boolean;
		/** @private **/
		protected var _start:Number;
		/** @private **/
		protected var _change:Number;
		/** @private **/
		protected var _circle:CirclePath2D;
		/** @private **/
		protected var _autoRotate:Boolean;
		/** @private **/
		protected var _rotationOffset:Number;
		
		/** @private **/
		public function CirclePath2DPlugin() {
			super("circlePath2D,x,y");
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			if (!("path" in value) || !(value.path is CirclePath2D)) {
				trace("CirclePath2DPlugin error: invalid 'path' property. Please define a CirclePath2D instance.");
				return false;
			}
			_target = target;
			_circle = value.path as CirclePath2D;
			_autoRotate = Boolean(value.autoRotate == true);
			_rotationOffset = value.rotationOffset || 0;
			
			var f:PathFollower = _circle.getFollower(target);
			if (f != null && !("startAngle" in value)) {
				_start = f.progress;
			} else {
				_start = _circle.angleToProgress(value.startAngle || 0, value.useRadians);
				_circle.renderObjectAt(_target, _start);
			}
			_change = Number(_circle.anglesToProgressChange(_circle.progressToAngle(_start), value.endAngle || 0, value.direction || "clockwise", value.extraRevolutions || 0, Boolean(value.useRadians)));
			return true;
		}
		
		/** @private **/
		override public function _kill(lookup:Object):Boolean {
			if (("x" in lookup) || ("y" in lookup)) {
				_overwriteProps = [];
			}
			return super._kill(lookup);
		}
		
		/** @private **/
		override public function setRatio(v:Number):void {
			var angle:Number = (_start + (_change * v)) * _2PI,
				radius:Number = _circle.radius,
				m:Matrix = _circle.transform.matrix,
				px:Number = Math.cos(angle) * radius,
				py:Number = Math.sin(angle) * radius;
			
			_target.x = px * m.a + py * m.c + m.tx;
			_target.y = px * m.b + py * m.d + m.ty;
			
			if (_autoRotate) {
				angle += Math.PI / 2;
				px = Math.cos(angle) * _circle.radius;
				py = Math.sin(angle) * _circle.radius;
				_target.rotation = Math.atan2(px * m.b + py * m.d, px * m.a + py * m.c) * _RAD2DEG + _rotationOffset;
			}
		}

	}
}