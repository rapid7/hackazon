/**
 * Created by Николай_2 on 08.10.2014.
 */
package hackazon.slider {

import flash.display.DisplayObject;
import flash.display.SimpleButton;

/**
 * Round button mainly used to change slides
 */
public class SlideButton extends SimpleButton {
    private var _number:int;
    private var _active:Boolean = false;
    protected var defaultUpState:DisplayObject = new Resources.ROUND_BUTTON_IMAGE_GRAY;
    protected var defaultOverState:DisplayObject = new Resources.ROUND_BUTTON_IMAGE_BLUE;
    protected var defaultDownState:DisplayObject = new Resources.ROUND_BUTTON_IMAGE_DARK_BLUE;
    protected var defaultHitState:DisplayObject = new Resources.ROUND_BUTTON_IMAGE_DARK_BLUE;


    public function SlideButton() {
        super(
            defaultUpState,
            defaultOverState,
            defaultDownState,
            defaultHitState
        );
    }

    public function get number():int {
        return _number;
    }

    public function set number(value:int):void {
        _number = value;
    }

    public function get active():Boolean {
        return _active;
    }

    public function set active(value:Boolean):void {
        _active = value;
        if (_active) {
            upState = new Resources.ROUND_BUTTON_IMAGE_BLUE;
        } else {
            upState = new Resources.ROUND_BUTTON_IMAGE_GRAY;
        }
    }
}
}
