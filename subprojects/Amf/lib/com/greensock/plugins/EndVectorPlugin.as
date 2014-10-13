/**
 * VERSION: 12.0
 * DATE: 2012-01-12
 * AS3 
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
	
	import __AS3__.vec.Vector; //due to a bug in Flex Builder, this must be included in order to correctly compile a swc
/**
 * [AS3 only] Tweens numbers in a Vector.&lt;Number&gt;. Remember, Vectors require that you publish to <strong>Flash Player 10</strong> or later.
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.TweenLite; 
import com.greensock.plugins.TweenPlugin; 
import com.greensock.plugins.EndVectorPlugin; 
TweenPlugin.activate([EndVectorPlugin]); //activation is permanent in the SWF, so this line only needs to be run once.

var v:Vector.&lt;Number&gt; = new Vector.&lt;Number&gt;();
	v[0] = 0;
	v[1] = 1;
	v[2] = 2;
var end:Vector.&lt;Number&gt; = new Vector.&lt;Number&gt;();
	end[0] = 100;
	end[1] = 250;
	end[2] = 500;
TweenLite.to(v, 3, {endVector:end, onUpdate:report}); 
function report():void {
	trace(v);
}
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */	
	public class EndVectorPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		
		/** @private **/
		protected var _v:Vector.<Number>;
		/** @private **/
		protected var _info:Vector.<VectorInfo> = new Vector.<VectorInfo>();
		/** @private If the values should be rounded to the nearest integer, <code>_round</code> will be set to <code>true</code>. **/
		public var _round:Boolean;
		
		/** @private **/
		public function EndVectorPlugin() {
			super("endVector");
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			if (!(target is Vector.<Number>) || !(value is Vector.<Number>)) {
				return false;
			}
			_init(target as Vector.<Number>, value as Vector.<Number>);
			return true;
		}
		
		/** @private **/
		public function _init(start:Vector.<Number>, end:Vector.<Number>):void {
			_v = start;
			var i:int = end.length, cnt:uint = 0;
			while (--i > -1) {
				if (_v[i] != end[i]) {
					_info[cnt++] = new VectorInfo(i, _v[i], end[i] - _v[i]);
				}
			}
		}
		
		override public function _roundProps(lookup:Object, value:Boolean=true):void {
			if ("endVector" in lookup) {
				_round = value;
			}
		}
		
		
		/** @private **/
		override public function setRatio(v:Number):void {
			var i:int = _info.length, vi:VectorInfo, val:Number;
			if (_round) {
				while (--i > -1) {
					vi = _info[i];
					_v[vi.i] = ((val = vi.c * v + vi.s) > 0) ? (val + 0.5) >> 0 : (val - 0.5) >> 0;
				}
			} else {
				while (--i > -1) {
					vi = _info[i];
					_v[vi.i] = vi.c * v + vi.s;
				}
			}
		}
		
	}
}

internal class VectorInfo {
	public var i:uint;
	public var s:Number;
	public var c:Number;
	
	public function VectorInfo(index:uint, start:Number, change:Number) {
		this.i = index;
		this.s = start;
		this.c = change;
	}
}