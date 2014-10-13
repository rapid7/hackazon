/**
 * VERSION: 12.0.1
 * DATE: 2013-05-21
 * AS3 
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.plugins {
	import com.greensock.TweenLite;
	import flash.filters.BitmapFilter;
	import flash.filters.BlurFilter;
/**
 * @private
 * Base class for all filter plugins (like blurFilter, colorMatrixFilter, glowFilter, etc.). Handles common routines. 
 * There is no reason to use this class directly.<br /><br />
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class FilterPlugin extends TweenPlugin {
		/** @private **/
		public static const API:Number = 2; //If the API/Framework for plugins changes in the future, this number helps determine compatibility
		
		/** @private **/
		protected var _target:Object;
		/** @private **/
		protected var _type:Class;
		/** @private **/
		protected var _filter:BitmapFilter;
		/** @private **/
		protected var _index:int;
		/** @private **/
		protected var _remove:Boolean;
		/** @private **/
		private var _tween:TweenLite;
		
		/** @private **/
		public function FilterPlugin(props:String="", priority:Number=0) {
			super(props, priority);
		}
		
		/** @private **/
		protected function _initFilter(target:*, props:Object, tween:TweenLite, type:Class, defaultFilter:BitmapFilter, propNames:Array):Boolean {
			_target = target;
			_tween = tween;
			_type = type;
			var filters:Array = _target.filters, p:String, i:int, colorTween:HexColorsPlugin;
			var extras:Object = (props is BitmapFilter) ? {} : props;
			if (extras.index != null) {
				_index = extras.index;
			} else {
				_index = filters.length;
				if (extras.addFilter != true) {
					while (--_index > -1 && !(filters[_index] is _type)) { };
				}
			}
			if (_index < 0 || !(filters[_index] is _type)) {
				if (_index < 0) {
					_index = filters.length;
				}
				if (_index > filters.length) { //in case the requested index is too high, pad the lower elements with BlurFilters that have a blur of 0. 
					i = filters.length - 1;
					while (++i < _index) {
						filters[i] = new BlurFilter(0, 0, 1);
					}
				}
				filters[_index] = defaultFilter;
				_target.filters = filters;
			}
			_filter = filters[_index];
			_remove = (extras.remove == true);
			i = propNames.length;
			while (--i > -1) {
				p = propNames[i];
				if (p in props && _filter[p] != props[p]) {
					if (p == "color" || p == "highlightColor" || p == "shadowColor") {
						colorTween = new HexColorsPlugin();
						colorTween._initColor(_filter, p, props[p]);
						_addTween(colorTween, "setRatio", 0, 1, _propName);
					} else if (p == "quality" || p == "inner" || p == "knockout" || p == "hideObject") {
						_filter[p] = props[p];
					} else {
						_addTween(_filter, p, _filter[p], props[p], _propName);
					}
				}
			}
			return true;
		}
		
		/** @private **/
		override public function setRatio(v:Number):void {
			super.setRatio(v);
			var filters:Array = _target.filters;
			if (!(filters[_index] is _type)) { //a filter may have been added or removed since the tween began, changing the index.
				_index = filters.length; //default (in case it was removed)
				while (--_index > -1 && !(filters[_index] is _type)) { };
				if (_index == -1) {
					_index = filters.length;
				}
			}
			if (v == 1 && _remove && _tween._time == _tween._duration && _tween.data != "isFromStart") {
				if (_index < filters.length) {
					filters.splice(_index, 1);
				}
			} else {
				filters[_index] = _filter;
			}
			_target.filters = filters;
		}
		
	}
}