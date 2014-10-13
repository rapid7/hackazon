/**
 * Created by Николай_2 on 08.10.2014.
 */
package hackazon.slider {

import flash.display.SimpleButton;
import flash.display.Sprite;
import hackazon.slider.button.SquareSprite;

/**
 * Class mainly used for next- and prev- square buttons with arrows
 */
public class SquareButton extends SimpleButton {
    public static const TYPE_LEFT:String = 'left';
    public static const TYPE_RIGHT:String = 'right';
    protected var buttonType:String;

    public function SquareButton(buttonType:String = TYPE_RIGHT) {
        var square:Sprite = new SquareSprite(30, 30, 0xffffff, buttonType);
        var squareHover:Sprite = new SquareSprite(30, 30, 0xf6f6f6, buttonType);
        var squareDown:Sprite = new SquareSprite(30, 30, 0xeeeeee, buttonType);
        this.buttonType = buttonType;

        super(square, squareHover, squareDown, squareDown);
    }
}
}
