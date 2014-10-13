/**
 * VERSION: 12.0.1
 * DATE: 2013-02-09
 * AS3 
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
	import flash.geom.Matrix;
	import flash.geom.Transform;
/**
 * [AS3/AS2 only] TransformMatrixPlugin allows you to tween a DisplayObject's transform.matrix values directly 
 * (<code>a, b, c, d, tx, and ty</code>) or use common properties like <code>x, y, scaleX, scaleY, 
 * skewX, skewY, rotation</code> and even <code>shortRotation</code>.
 * To skew without adjusting scale visually, use skewX2 and skewY2 instead of skewX and skewY. 
 * 
 * <p>transformMatrix tween will affect all of the DisplayObject's transform properties, so do not use
 * it in conjunction with regular x/y/scaleX/scaleY/rotation tweens concurrently.</p>
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.TweenLite;
import com.greensock.plugins.~~; 
TweenPlugin.activate([TransformMatrixPlugin]); //activation is permanent in the SWF, so this line only needs to be run once.

TweenLite.to(mc, 1, {transformMatrix:{x:50, y:300, scaleX:2, scaleY:2}}); 

//-OR-

TweenLite.to(mc, 1, {transformMatrix:{tx:50, ty:300, a:2, d:2}}); 
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class TransformMatrixPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		/** @private **/
		private static const _DEG2RAD:Number = Math.PI / 180;
		
		/** @private **/
		protected var _transform:Transform;
		/** @private **/
		protected var _matrix:Matrix;
		/** @private **/
		protected var _txStart:Number;
		/** @private **/
		protected var _txChange:Number;
		/** @private **/
		protected var _tyStart:Number;
		/** @private **/
		protected var _tyChange:Number;
		/** @private **/
		protected var _aStart:Number;
		/** @private **/
		protected var _aChange:Number;
		/** @private **/
		protected var _bStart:Number;
		/** @private **/
		protected var _bChange:Number;
		/** @private **/
		protected var _cStart:Number;
		/** @private **/
		protected var _cChange:Number;
		/** @private **/
		protected var _dStart:Number;
		/** @private **/
		protected var _dChange:Number;
		/** @private **/
		protected var _angleChange:Number = 0;
		
		/** @private **/
		public function TransformMatrixPlugin() {
			super("transformMatrix,x,y,scaleX,scaleY,rotation,width,height,transformAroundPoint,transformAroundCenter");
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			_transform = target.transform as Transform;
			_matrix = _transform.matrix;
			var matrix:Matrix = _matrix.clone();
			_txStart = matrix.tx;
			_tyStart = matrix.ty;
			_aStart = matrix.a;
			_bStart = matrix.b;
			_cStart = matrix.c;
			_dStart = matrix.d;
			
			if ("x" in value) {
				_txChange = (typeof(value.x) == "number") ? value.x - _txStart : Number(value.x.split("=").join(""));
			} else if ("tx" in value) {
				_txChange = value.tx - _txStart;
			} else {
				_txChange = 0;
			}
			if ("y" in value) {
				_tyChange = (typeof(value.y) == "number") ? value.y - _tyStart : Number(value.y.split("=").join(""));
			} else if ("ty" in value) {
				_tyChange = value.ty - _tyStart;
			} else {
				_tyChange = 0;
			}
			_aChange = ("a" in value) ? value.a - _aStart : 0;
			_bChange = ("b" in value) ? value.b - _bStart : 0;
			_cChange = ("c" in value) ? value.c - _cStart : 0;
			_dChange = ("d" in value) ? value.d - _dStart : 0;
			
			if (("rotation" in value) || ("shortRotation" in value) || ("scale" in value && !(value is Matrix)) || ("scaleX" in value) || ("scaleY" in value) || ("skewX" in value) || ("skewY" in value) || ("skewX2" in value) || ("skewY2" in value)) {
				var ratioX:Number, ratioY:Number;
				var scaleX:Number = Math.sqrt(matrix.a * matrix.a + matrix.b * matrix.b); //Bugs in the Flex framework prevent DisplayObject.scaleX from working consistently, so we must determine it using the matrix.
				if (scaleX == 0) {
					matrix.a = scaleX = 0.0001;
				} else if (matrix.a < 0 && matrix.d > 0) {
					scaleX = -scaleX;
				}
				var scaleY:Number = Math.sqrt(matrix.c * matrix.c + matrix.d * matrix.d); //Bugs in the Flex framework prevent DisplayObject.scaleY from working consistently, so we must determine it using the matrix.
				if (scaleY == 0) { 
					matrix.d = scaleY = 0.0001;
				} else if (matrix.d < 0 && matrix.a > 0) {
					scaleY = -scaleY;
				}
				var angle:Number = Math.atan2(matrix.b, matrix.a); //Bugs in the Flex framework prevent DisplayObject.rotation from working consistently, so we must determine it using the matrix
				if (matrix.a < 0 && matrix.d >= 0) {
					angle += (angle <= 0) ? Math.PI : -Math.PI;
				}
				var skewX:Number = Math.atan2(-_matrix.c, _matrix.d) - angle;
				var finalAngle:Number = angle;
				if ("shortRotation" in value) {
					var dif:Number = ((value.shortRotation * _DEG2RAD) - angle) % (Math.PI * 2);
					if (dif > Math.PI) {
						dif -= Math.PI * 2;
					} else if (dif < -Math.PI) {
						dif += Math.PI * 2;
					}
					finalAngle += dif;
				} else if ("rotation" in value) {
					finalAngle = (typeof(value.rotation) == "number") ? value.rotation * _DEG2RAD : Number(value.rotation.split("=").join("")) * _DEG2RAD + angle;
				}
				
				var finalSkewX:Number = ("skewX" in value) ? (typeof(value.skewX) == "number") ? Number(value.skewX) * _DEG2RAD : Number(value.skewX.split("=").join("")) * _DEG2RAD + skewX : 0;
				
				if ("skewY" in value) { //skewY is just a combination of rotation and skewX
					var skewY:Number = (typeof(value.skewY) == "number") ? value.skewY * _DEG2RAD : Number(value.skewY.split("=").join("")) * _DEG2RAD - skewX;
					finalAngle += skewY + skewX;
					finalSkewX -= skewY;
				}
				
				if (finalAngle != angle) {
					if (("rotation" in value) || ("shortRotation" in value)) {
						_angleChange = finalAngle - angle;
						finalAngle = angle; //to correctly affect the skewX calculations below
					} else {
						matrix.rotate(finalAngle - angle);
					}
				}
				
				if ("scale" in value) {
					ratioX = Number(value.scale) / scaleX;
					ratioY = Number(value.scale) / scaleY;
					if (typeof(value.scale) != "number") { //relative value
						ratioX += 1;
						ratioY += 1;
					}
				} else {
					if ("scaleX" in value) {
						ratioX = Number(value.scaleX) / scaleX;
						if (typeof(value.scaleX) != "number") { //relative value
							ratioX += 1;
						}
					}
					if ("scaleY" in value) {
						ratioY = Number(value.scaleY) / scaleY;
						if (typeof(value.scaleY) != "number") { //relative value
							ratioY += 1;
						}
					}
				}
				
				if (finalSkewX != skewX) {
					matrix.c = -scaleY * Math.sin(finalSkewX + finalAngle);
					matrix.d = scaleY * Math.cos(finalSkewX + finalAngle);
				}
				
				if ("skewX2" in value) {
					if (typeof(value.skewX2) == "number") {
						matrix.c = Math.tan(0 - (value.skewX2 * _DEG2RAD));
					} else {
						matrix.c += Math.tan(0 - (Number(value.skewX2) * _DEG2RAD));
					}
				}
				if ("skewY2" in value) {
					if (typeof(value.skewY2) == "number") {
						matrix.b = Math.tan(value.skewY2 * _DEG2RAD);
					} else {
						matrix.b += Math.tan(Number(value.skewY2) * _DEG2RAD);
					}
				}
				
				if (ratioX || ratioX == 0) { //faster than isNaN()
					matrix.a *= ratioX;
					matrix.b *= ratioX;
				}
				if (ratioY || ratioY == 0) {
					matrix.c *= ratioY;
					matrix.d *= ratioY;
				}
				_aChange = matrix.a - _aStart;
				_bChange = matrix.b - _bStart;
				_cChange = matrix.c - _cStart;
				_dChange = matrix.d - _dStart;
			}
			return true;
		}
		
		/** @private **/
		override public function setRatio(v:Number):void {
			_matrix.a = _aStart + (v * _aChange);
			_matrix.b = _bStart + (v * _bChange);
			_matrix.c = _cStart + (v * _cChange);
			_matrix.d = _dStart + (v * _dChange);
			if (_angleChange) {
				//about 3-4 times faster than _matrix.rotate(_angleChange * n);
				var cos:Number = Math.cos(_angleChange * v),
					sin:Number = Math.sin(_angleChange * v),
					a:Number = _matrix.a,
					c:Number = _matrix.c;
				_matrix.a = a * cos - _matrix.b * sin;
				_matrix.b = a * sin + _matrix.b * cos;
				_matrix.c = c * cos - _matrix.d * sin;
				_matrix.d = c * sin + _matrix.d * cos;
			}
			_matrix.tx = _txStart + (v * _txChange);
			_matrix.ty = _tyStart + (v * _tyChange);
			_transform.matrix = _matrix;
		}

	}
}