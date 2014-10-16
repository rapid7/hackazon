package {

import flash.display.LoaderInfo;
import flash.display.Sprite;
import flash.events.Event;
import hackazon.image.GifLoader;
import hackazon.slider.Slider;
import org.bytearray.gif.player.GIFPlayer;
import org.silexlabs.amfphp.clientgenerator.NetConnectionSingleton;
import org.silexlabs.amfphp.clientgenerator.ObjectUtil;
import org.silexlabs.amfphp.clientgenerator.generated.service.SliderServiceClient;

/**
 * Main class for banner
 */
[SWF(pageTitle="Slider", width=360, height=290, backgroundColor="#ffffff", frameRate=60)]
public class SliderWidget extends Sprite {
    protected var _slider:Slider;
    protected var _config:Object = {
//        actions: [
//            {image: '/images/banner_01-v3.jpg'},
//            {image: '/images/banner_02-v3.jpg'},
//            {image: '/images/banner_03-v3.jpg'},
//            {image: '/images/banner_04-v3.jpg'}
//        ],
        host: 'http://hackazon.webscantest.com'
    };
    private var _service:SliderServiceClient;
    private var _loaderIcon:GIFPlayer;
    private var _host:String;

    public function SliderWidget() {
        // Get flashVars to access current host value
        var paramObj:Object = LoaderInfo(root.loaderInfo).parameters;
        _host = paramObj.host || _config.host;

        NetConnectionSingleton._host = _host;

        _service = new SliderServiceClient(NetConnectionSingleton.getNetConnection());
        addEventListener(Event.ADDED_TO_STAGE, function (ev:Event):void {
            SliderWidget(ev.target).init();
        });
    }

    public function init():void {
        // Add background
        var bg:Sprite = new Sprite();
        bg.width = stage.stageWidth;
        bg.height = stage.stageHeight;
        bg.x = 0;
        bg.y = 0;
        addChild(bg);

        // Add loader
        _loaderIcon = GifLoader.getLoaderIcon();
        addChild(_loaderIcon);
        _loaderIcon.x = stage.stageWidth / 2 - 10;
        _loaderIcon.y = stage.stageHeight / 2 - 10;
        _service.getSlides(Math.ceil(4 * Math.random())).setResultHandler(getSlidesResultHandler).setErrorHandler(errorHandler);
    }

    protected function buildSlider():void {
        removeChild(_loaderIcon);

        var images:Array = [];
        for (var i:uint = 0; i < _config.actions.length; i++) {
            images[i] = _config.actions[i].image;
        }
        _slider = new Slider(images, stage.stageWidth, stage.stageHeight, _host);
        addChild(_slider);
    }

    private function getSlidesResultHandler(obj:Object):void{
        _config.actions = [];
        for (var i:uint = 0; i < obj.length; i++) {
            _config.actions[i] = {image: obj[i]};
        }

        buildSlider();
    }

    private static function errorHandler(obj:Object):void{
        trace("error :  \n" +  ObjectUtil.deepObjectToString(obj) + "\n");
    }
}
}
