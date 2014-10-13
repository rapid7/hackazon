/**
 * VERSION: 1.11
 * DATE: 2012-06-06
 * AS3 (AS2 and JS versions are also available)
 * UPDATES AND DOCS AT: http://www.greensock.com
 **/
package com.greensock.easing {
	import com.greensock.easing.Ease;
/**
 * SlowMo is a configurable ease that produces a slow-motion effect that decelerates initially, then moves 
 * linearly for a certain portion of the ease (which you can choose) and then accelerates again at the end;
 * it's great for effects like zooming text onto the screen, smoothly moving it long enough for people to 
 * read it, and then zooming it off the screen. Without SlowMo, animators would often try to get the same 
 * effect by sequencing 3 tweens, one with an easeOut, then another with a Linear.easeNone, and finally 
 * an easeIn but the problem was that the eases didn't smoothly transition into one another, so you'd see 
 * sudden shifts in velocity at the joints. SlowMo solves this problem and gives you complete control over 
 * how strong the eases are on each end and what portion of the movement in the middle is linear.
 * 
 * <p>The first parameter, <code>linearRatio</code>, determines the proportion of the ease during which 
 * the rate of change will be linear (steady pace). This should be a number between 0 and 1. For example, 
 * 0.5 would be half, so the first 25% of the ease would be easing out (decelerating), then 50% would be 
 * linear, then the final 25% would be easing in (accelerating). If you choose 0.8, that would mean 80% 
 * of the ease would be linear, leaving 10% on each end to ease. The default is 0.7.</p>
 * 
 * <p>The second parameter, <code>power</code>, determines the strength of the ease at each end. 
 * If you define a value greater than 1, it will actually reverse the linear portion in the middle 
 * which can create interesting effects. The default is 0.7.</p>
 * 
 * <p>The third parameter, <code>yoyoMode</code>, provides an easy way to create companion tweens that
 * sync with normal SlowMo tweens. For example, let's say you have a SlowMo tween that is zooming some
 * text onto the screen and moving it linearly for a while and then zooming off, but you want to 
 * tween that alpha of the text at the beginning and end of the positional tween. Normally, you'd need
 * to create 2 separate alpha tweens, 1 for the fade-in at the beginning and 1 for the fade-out at the
 * end and you'd need to calculate their durations manually to ensure that they finish fading in
 * by the time the linear motion begins and then they start fading out at the end right when the linear
 * motion completes. But to make this whole process much easier, all you'd need to do is create a separate
 * tween for the alpha and use the same duration but a SlowMo ease that has its <code>yoyoMode</code>
 * parameter set to <code>true</code>.</p>
 * 
 * @example Example AS3 example:<listing version="3.0">
import com.greensock.~~;
import com.greensock.easing.~~;

//use the default SlowMo ease (linearRatio of 0.7 and power of 0.7)
TweenLite.to(myText, 5, {x:600, ease:SlowMo.ease});

//use a new SlowMo ease with 50% of the tween being linear (2.5 seconds) and a power of 0.8
TweenLite.to(myText, 5, {x:600, ease:new SlowMo(0.5, 0.8)});
 
//this gives the exact same effect as the line above, but uses a different syntax
TweenLite.to(myText, 5, {x:600, ease:SlowMo.ease.config(0.5, 0.8)});
 
//now let's create an alpha tween that syncs with the above positional tween, fading it in at the beginning and out at the end
myText.alpha = 0;
TweenLite.to(myText, 5, {alpha:1, ease:SlowMo.ease.config(0.5, 0.8, true)});
</listing>
 * 
 * <p><strong>Copyright 2014, GreenSock. All rights reserved.</strong> This work is subject to the terms in <a href="http://www.greensock.com/terms_of_use.html">http://www.greensock.com/terms_of_use.html</a> or for <a href="http://www.greensock.com/club/">Club GreenSock</a> members, the software agreement that was issued with the membership.</p>
 * 
 * @author Jack Doyle, jack@greensock.com
 **/
	public class SlowMo extends Ease {
		
		/** The default ease instance which can be reused many times in various tweens in order to conserve memory and improve performance slightly compared to creating a new instance each time. **/
		public static var ease:SlowMo = new SlowMo();
		
		/** @private **/
		private var _p:Number;
		
		/**
		 * Constructor
		 * 
		 * @param linearRatio the proportion of the ease during which the rate of change will be linear (steady pace). This should be a number between 0 and 1. For example, 0.5 would be half, so the first 25% of the ease would be easing out (decelerating), then 50% would be linear, then the final 25% would be easing in (accelerating). If you choose 0.8, that would mean 80% of the ease would be linear, leaving 10% on each end to ease. The default is 0.7.
		 * @param power The strength of the ease at each end. If you define a value above 1, it will actually reverse the linear portion in the middle which can create interesting effects. The default is 0.7.
		 * @param yoyoMode If <code>true</code>, the ease will reach its destination value mid-tween and maintain it during the entire linear mode and then go back to the original value at the end (like a yoyo of sorts). This can be very useful if, for example, you want the alpha (or some other property) of some text to fade at the front end of a SlowMo positional ease and then back down again at the end of that positional SlowMo tween. Otherwise you would need to create separate tweens for the beginning and ending fades that match up with that positional tween. Example: <code>TweenLite.to(myText, 5, {x:600, ease:SlowMo.ease.config(0.7, 0.7, false)}); TweenLite.from(myText, 5, {alpha:0, ease:SlowMo.ease.config(0.7, 0.7, true)});</code>
		 */
		public function SlowMo(linearRatio:Number=0.7, power:Number=0.7, yoyoMode:Boolean=false) {
			if (linearRatio > 1) {
				linearRatio = 1;
			}
			_p = (linearRatio != 1) ? power : 0;
			_p1 = (1 - linearRatio) / 2;
			_p2 = linearRatio;
			_p3 = _p1 + _p2;
			_calcEnd = yoyoMode;
		}
		
		/** @inheritDoc **/
		override public function getRatio(p:Number):Number {
			var r:Number = p + (0.5 - p) * _p;
			if (p < _p1) {
				return _calcEnd ? 1 - ((p = 1 - (p / _p1)) * p) : r - ((p = 1 - (p / _p1)) * p * p * p * r);
			} else if (p > _p3) {
				return _calcEnd ? 1 - (p = (p - _p3) / _p1) * p : r + ((p - r) * (p = (p - _p3) / _p1) * p * p * p);
			}
			return _calcEnd ? 1 : r;
		}
		
		/**
		 * Permits customization of the ease with various parameters.
		 * 
		 * @param linearRatio the proportion of the ease during which the rate of change will be linear (steady pace). This should be a number between 0 and 1. For example, 0.5 would be half, so the first 25% of the ease would be easing out (decelerating), then 50% would be linear, then the final 25% would be easing in (accelerating). If you choose 0.8, that would mean 80% of the ease would be linear, leaving 10% on each end to ease. The default is 0.7.
		 * @param power The strength of the ease at each end. If you define a value above 1, it will actually reverse the linear portion in the middle which can create interesting effects. The default is 0.7.
		 * @param yoyoMode If <code>true</code>, the ease will reach its destination value mid-tween and maintain it during the entire linear mode and then go back to the original value at the end (like a yoyo of sorts). This can be very useful if, for example, you want the alpha (or some other property) of some text to fade at the front end of a SlowMo positional ease and then back down again at the end of that positional SlowMo tween. Otherwise you would need to create separate tweens for the beginning and ending fades that match up with that positional tween. Example: <code>TweenLite.to(myText, 5, {x:600, ease:SlowMo.ease.config(0.7, 0.7, false)}); TweenLite.from(myText, 5, {alpha:0, ease:SlowMo.ease.config(0.7, 0.7, true)});</code> 
		 * @return new SlowMo instance that is configured according to the parameters provided
		 */
		public function config(linearRatio:Number=0.7, power:Number=0.7, yoyoMode:Boolean=false):SlowMo {
			return new SlowMo(linearRatio, power, yoyoMode);
		}
		
	}
	
}
