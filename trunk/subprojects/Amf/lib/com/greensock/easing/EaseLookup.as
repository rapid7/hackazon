/**
 * VERSION: 1.0
 * DATE: 2012-03-22
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
/**
 * EaseLookup enables you to find the easing function associated with a particular name (String), 
 * like "strongEaseOut" which can be useful when loading in XML data that comes in as Strings but 
 * needs to be translated to native function references.
 * 
 * <p><strong>Copyright 2008-2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 */
	public class EaseLookup {
		/** @private **/
		private static var _lookup:Object;
		
		/**
		 * Finds the easing function associated with a particular name (String), like "strongEaseOut". This can be useful when
		 * loading in XML data that comes in as Strings but needs to be translated to native function references. You can pass in
		 * the name with or without the period, and it is case insensitive, so any of the following will find the Strong.easeOut function:
		 * 
		 * <p><code>EaseLookup.find("Strong.easeOut") </code></p>
		 * <p><code>EaseLookup.find("strongEaseOut") </code></p>
		 * <p><code>EaseLookup.find("strongeaseout") </code></p>
		 * 
		 * <p>You can translate strings directly when tweening, like this:</p>
		 * 
		 * <p><code>
		 * TweenLite.to(mc, 1, {x:100, ease:EaseLookup.find(myString)});
		 * </code></p>
		 * 
		 * @param name The name of the easing function, with or without the period and case insensitive (i.e. "Strong.easeOut" or "strongEaseOut")
		 * @return The easing function associated with the name
		 */
		public static function find(name:String):Ease {
			if (_lookup == null) {
				_lookup = {};
				
				_addInOut(Back, ["back"]);
				_addInOut(Bounce, ["bounce"]);
				_addInOut(Circ, ["circ", "circular"]);
				_addInOut(Cubic, ["cubic","power2"]);
				_addInOut(Elastic, ["elastic"]);
				_addInOut(Expo, ["expo", "exponential"]);
				_addInOut(Power0, ["linear","power0"]);
				_addInOut(Quad, ["quad", "quadratic","power1"]);
				_addInOut(Quart, ["quart","quartic","power3"]);
				_addInOut(Quint, ["quint", "quintic", "strong","power4"]);
				_addInOut(Sine, ["sine"]);
				
				_lookup["linear.easenone"] = _lookup["lineareasenone"] = Linear.easeNone;
				_lookup.slowmo = _lookup["slowmo.ease"] = SlowMo.ease;
			}
			return _lookup[name.toLowerCase()];
		}
		
		/** @private **/
		private static function _addInOut(easeClass:Class, names:Array):void {
			var name:String, i:int = names.length;
			while (--i > -1) {
				name = names[i].toLowerCase();
				_lookup[name + ".easein"] = _lookup[name + "easein"] = easeClass.easeIn;
				_lookup[name + ".easeout"] = _lookup[name + "easeout"] = easeClass.easeOut;
				_lookup[name + ".easeinout"] = _lookup[name + "easeinout"] = easeClass.easeInOut;
			}
		}
		
	}
}