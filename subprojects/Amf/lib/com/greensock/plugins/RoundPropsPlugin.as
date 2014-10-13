/**
 * VERSION: 12.0
 * DATE: 2012-01-14
 * AS3
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
	import com.greensock.core.PropTween;
/**
 * If you'd like the inbetween values in a tween to always get rounded to the nearest integer, use the roundProps
 * special property. Just pass in a comma-delimited String containing the property names that you'd like rounded. For example,
 * if you're tweening the x, y, and alpha properties of mc and you want to round the x and y values (not alpha)
 * every time the tween is rendered, you'd do: <br /><br /><code>
 * 	
 * 	TweenMax.to(mc, 2, {x:300, y:200, alpha:0.5, roundProps:"x,y"});<br /><br /></code>
 * 
 * <p><b>USAGE:</b></p>
 * <listing version="3.0">
import com.greensock.TweenMax; 
import com.greensock.plugins.RoundPropsPlugin;
TweenPlugin.activate([RoundPropsPlugin]); //activation is permanent in the SWF, so this line only needs to be run once.

TweenMax.to(mc, 2, {x:300, y:200, alpha:0.5, roundProps:"x,y"}); 
</listing>
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class RoundPropsPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		
		/** @private **/
		protected var _tween:TweenLite;
		
		/** @private **/
		public function RoundPropsPlugin() {
			super("roundProps", -1);
			_overwriteProps.length = 0;
		}
		
		/** @private **/
		override public function _onInitTween(target:Object, value:*, tween:TweenLite):Boolean {
			_tween = tween;
			return true;
		}
		
		/** @private **/
		public function _onInitAllProps():Boolean {
			var rp:Array = (_tween.vars.roundProps is Array) ? _tween.vars.roundProps : _tween.vars.roundProps.split(","),
				i:int = rp.length, 
				lookup:Object = {},
				rpt:PropTween = _tween._propLookup.roundProps,
				prop:String, pt:PropTween, next:PropTween;
			while (--i > -1) {
				lookup[rp[i]] = 1;
			}
			i = rp.length;
			while (--i > -1) {
				prop = rp[i];
				pt = _tween._firstPT;
				while (pt) {
					next = pt._next; //record here, because it may get removed
					if (pt.pg) {
						pt.t._roundProps(lookup, true);
					} else if (pt.n == prop) {
						_add(pt.t, prop, pt.s, pt.c);
						//remove from linked list
						if (next) {
							next._prev = pt._prev;
						}
						if (pt._prev) {
							pt._prev._next = next;
						} else if (_tween._firstPT == pt) {
							_tween._firstPT = next;
						}
						pt._next = pt._prev = null;
						_tween._propLookup[prop] = rpt;
					}
					pt = next;
				}
			}
			return false;
		}
		
		/** @private **/
		public function _add(target:Object, p:String, s:Number, c:Number):void {
			_addTween(target, p, s, s + c, p, true);
			_overwriteProps[_overwriteProps.length] = p;
		}

	}
}