/**
 * Created by Николай_2 on 08.10.2014.
 */
package hackazon.slider.button {
import flash.display.DisplayObject;
import flash.display.Graphics;
import flash.display.Sprite;

import hackazon.slider.Resources;
import hackazon.slider.SquareButton;

public class SquareSprite extends Sprite {
    protected var _width:int;
    protected var _height:int;
    protected var _color:int;

    public function SquareSprite(width:int, height:int, color:int, buttonType:String) {
        super();
        _width = width;
        _height = height;
        _color = color;

        var g:Graphics = graphics;
        g.beginFill(_color);
        g.drawRoundRect(0, 0, _width, _height, 8, 8);
        g.endFill();

        var arrow:DisplayObject;

        if (buttonType == SquareButton.TYPE_RIGHT) {
            arrow = new Resources.ARROW_RIGHT;
        } else {
            arrow = new Resources.ARROW_LEFT;
        }
        addChild(arrow);
        arrow.x = 12;
        arrow.y = 12;
    }
}
}
