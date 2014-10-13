/**
 * Created by Николай_2 on 07.10.2014.
 */
package hackazon.slider {
import flash.display.Bitmap;
import flash.display.Graphics;
import flash.display.Loader;
import flash.display.Sprite;
import flash.events.Event;
import flash.events.IOErrorEvent;
import flash.net.URLRequest;
import flash.text.TextField;
import flash.text.TextFieldAutoSize;
import hackazon.image.GifLoader;
import org.bytearray.gif.player.GIFPlayer;

public class Slide extends Sprite {
    private var _url:String;
    private var _width:int = 100;
    private var _height:int = 100;
    private var _loader:Loader;
    protected var _content:Bitmap = null;
    protected var loaderNotice:TextField;
    protected var _loaderIcon:GIFPlayer;

    public function Slide(url:String ) {
        _url = url;
        _loader = new Loader;
        _loader.contentLoaderInfo.addEventListener(Event.COMPLETE, onImageLoaded, false, 0, true);
        _loader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, onImageLoadingError, false, 0, true);
        _loader.load(new URLRequest(_url));
        addChild(_loader);

        loaderNotice = new TextField();
        loaderNotice.text = "";
        loaderNotice.width = 1;
        loaderNotice.autoSize = TextFieldAutoSize.CENTER;
        addChild(loaderNotice);

        _loaderIcon = GifLoader.getLoaderIcon();
        addChild(_loaderIcon);

        addEventListener(Event.ADDED_TO_STAGE, onAddToStage, false, 0, true);

    }

    public function onAddToStage(ev:Event):void {
        var g:Graphics = graphics;
        g.beginFill(0xffffff);
        g.drawRect(0, 0, _width, _height);
        g.endFill();

        loaderNotice.x = _width / 2;
        loaderNotice.y = _height / 2 - 10;

        if (!loaderNotice.text) {
            loaderNotice.text = "Loading...";
        }
        loaderNotice.visible = false;

        _loaderIcon.x = _width / 2 - 10;
        _loaderIcon.y = _height / 2 - 10;

        removeEventListener(Event.ADDED_TO_STAGE, onAddToStage);
    }

    public function onImageLoaded(ev:Event):void {
        removeChild(_loaderIcon);
        loaderNotice.visible = false;
        var b:Bitmap = Bitmap(_loader.content);
        b.smoothing = true;
        b.width = _width;
        b.height = _height;
        _content = b;
    }

    protected function onImageLoadingError(event:IOErrorEvent):void {
        removeChild(_loaderIcon);
        loaderNotice.visible = true;
        loaderNotice.text = 'Image is unavailable';
    }

    public function get url():String {
        return _url;
    }

    public function get Width():int {
        return _width;
    }

    public function set Width(value:int):void {
        _width = value;
    }

    public function get Height():int {
        return _height;
    }

    public function set Height(value:int):void {
        _height = value;
    }

    public function clone():Slide {
        var s:Slide = new Slide(_url);
        return s;
    }
}
}
