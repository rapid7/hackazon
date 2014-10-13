/**
 * Created by Николай_2 on 07.10.2014.
 */
package hackazon.slider {

import com.greensock.TweenLite;
import flash.display.Graphics;
import flash.display.MovieClip;
import flash.display.Sprite;
import flash.events.Event;
import flash.events.MouseEvent;
import flash.events.TimerEvent;
import flash.utils.Timer;
import mx.effects.easing.Quartic;

public class Slider extends MovieClip {
    protected var _slides:Vector.<Slide> = new Vector.<Slide>();
    protected var _host:String;
    protected var _width:int = 100;
    protected var _height:int = 100;
    protected var _container:Sprite;
    protected var _run:Boolean = false;
    protected var _timer:Timer = new Timer(5000);
    protected var _currentSlide:int = 0;
    protected var _slideMask:Sprite;
    protected var xOffset:int = 10;
    protected var yOffset:int = 10;
    protected var rightButton:SquareButton;
    protected var leftButton:SquareButton;
    protected var slideButtons:Sprite = new Sprite();
    private var _slideTween:TweenLite;

    public function Slider(slides:Array, width:int = 100, height:int = 100, host:String = '') {
        _host = host;
        _width = width;
        _height = height;

        _timer.addEventListener(TimerEvent.TIMER, onTimer, false, 0, true);

        _container = new Sprite();
        addChild(_container);
        _container.y = yOffset;
        _container.x = xOffset;

        for (var i:int = 0; i < slides.length; i++) {
            addSlide(new Slide(_host + slides[i]));
        }

        // Add white mask around slides
        _slideMask = new Sprite();
        addChild(_slideMask);

        // Start sliding timer
        addEventListener(Event.ADDED_TO_STAGE, onAddToStage, false, 0, true);
    }

    /**
     * Slider timer callback
     * @param event
     */
    public function onTimer(event:TimerEvent):void {
        if (_run) {
            goToNextSlide();
        }
    }

    /**
     * Go to the next slide
     */
    public function goToNextSlide():void {
        goToSlide(_currentSlide + 1);
    }

    /**
     * Go to given slide
     * @param num
     */
    public function goToSlide(num:int):void {
        if (num < 0) {
            num = _slides.length - 2;
            _container.x = -(num + 1) * (_width - 2 * xOffset) + xOffset;
        }

        var oldCurrent:int = _currentSlide;
        _currentSlide = num;
        var curSlide:int = _currentSlide,
            needRewind:Boolean = curSlide + 1 == _slides.length,
            fixedCurSlide:int = needRewind ? 0 : curSlide;



        if (slideButtons.numChildren > fixedCurSlide) {
            SlideButton(slideButtons.getChildAt(oldCurrent)).active = false;
            SlideButton(slideButtons.getChildAt(fixedCurSlide)).active = true;
        }

        _currentSlide = fixedCurSlide;

        _slideTween = TweenLite.to(_container, 2, {
            x: -curSlide * (_width - 2 * xOffset) + xOffset,
            ease: Quartic.easeInOut,
            onComplete: function ():void {
                if (needRewind && _container.x < 0) {
                    _container.x = xOffset;
                }
            }
        });
    }

    public function addSlide(slide:Slide):Slider {
        var last:Slide;
        if (_slides.length) {
            last = _slides.pop();
        } else {
            last = slide.clone();
            _container.addChild(last);
        }
        _slides[_slides.length] = slide;
        _container.addChild(slide);
        _slides[_slides.length] = last;
        return this;
    }

    /**
     * All actions after adding slider to stage
     * @param ev
     */
    public function onAddToStage(ev:Event):void {
        var sw:int = stage.stageWidth,
            sh:int = stage.stageHeight;

        // Outer part
        var ox:int = xOffset * 2;
        var oy:int = yOffset * 2;

        var g:Graphics = _slideMask.graphics;
        g.beginFill(0xffffff);
        g.drawRect(-ox, -oy, sw + ox + ox, yOffset + oy);
        g.endFill();

        g.beginFill(0xffffff);
        g.drawRect(-ox, -oy, xOffset + ox, sh + oy + oy);
        g.endFill();

        g.beginFill(0xffffff);
        g.drawRect(sw - xOffset, -oy, xOffset + ox, sh + oy + oy);
        g.endFill();

        g.beginFill(0xffffff);
        g.drawRect(-ox, sh - 40, sw + ox + ox, 40 + oy);
        g.endFill();


        for (var i:int = 0; i < _slides.length; i++) {
            _slides[i].Width = sw - ox;
            _slides[i].Height = sh - 30 - oy;

            _slides[i].x = i * _slides[i].Width;
            _slides[i].y = 0;
        }

        // Add buttons
        addChild(slideButtons);
        slideButtons.x = xOffset * 1.5;
        slideButtons.y = sh - yOffset * 2.5;
        slideButtons.addEventListener(MouseEvent.CLICK, onSlideButtonClick);

        var b:SlideButton;
        for (i = 0; i < _slides.length - 1; i++) {
            b = new SlideButton();
            b.number = i;
            slideButtons.addChild(b);
            b.x = i * xOffset * 1.5;
            b.y = 0;
        }

        if (slideButtons.numChildren > 0) {
            SlideButton(slideButtons.getChildAt(0)).active = true;
        }

        leftButton = new SquareButton(SquareButton.TYPE_LEFT);
        addChild(leftButton);
        leftButton.x = sw - 80;
        leftButton.y = sh - 35;

        rightButton = new SquareButton();
        addChild(rightButton);
        rightButton.x = sw - 40;
        rightButton.y = sh - 35;

        rightButton.addEventListener(MouseEvent.CLICK, onRightButtonClick);
        leftButton.addEventListener(MouseEvent.CLICK, onLeftButtonClick);

        start();
    }

    /**
     * Starts slider
     */
    public function start():void {
        _run = true;
        _timer.start();
    }

//    /**
//     * Temporarily stops the slider
//     */
//    public function suspend():void {
//        _run = false;
//        _timer.stop();
//    }

    /**
     * Slide button (round) handler
     * @param event
     */
    protected function onSlideButtonClick(event:MouseEvent):void {
        var button:* = event.target;
        if (button is SlideButton) {
            forceGoToSlide(button.number);
        }
    }

    /**
     * Stops current animation, resets timer, and goes to desired slide
     * @param num
     */
    public function forceGoToSlide(num:int):void {
        if (_slideTween) {
            _slideTween.kill();
            _slideTween = null;
        }

        _timer.reset();
        _timer.start();

        goToSlide(num);
    }

    protected function onRightButtonClick(event:MouseEvent):void {
        forceGoToSlide(_currentSlide + 1);
    }

    protected function onLeftButtonClick(event:MouseEvent):void {
        forceGoToSlide(_currentSlide - 1);
    }
}
}
