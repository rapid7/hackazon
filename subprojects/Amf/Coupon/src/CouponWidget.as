/**
 * Created by Николай_2 on 10.10.2014.
 */
package {

import flash.display.LoaderInfo;
import flash.display.Sprite;
import flash.events.Event;
import flash.external.ExternalInterface;
import flash.system.Security;

import org.silexlabs.amfphp.clientgenerator.NetConnectionSingleton;
import org.silexlabs.amfphp.clientgenerator.ObjectUtil;
import org.silexlabs.amfphp.clientgenerator.generated.service.CouponServiceClient;

[SWF(pageTitle="Coupon", width=0, height=0, backgroundColor="#ffffff", frameRate=10)]
public class CouponWidget extends Sprite {
    private var _service:CouponServiceClient;
    private var _host:String = 'http://hackazon.webscantest.com';

    public function CouponWidget() {
        Security.allowDomain('*');

        var paramObj:Object = LoaderInfo(root.loaderInfo).parameters;
        _host = paramObj.host || _host;

        NetConnectionSingleton._host = _host;

        _service = new CouponServiceClient(NetConnectionSingleton.getNetConnection());
        addEventListener(Event.ADDED_TO_STAGE, function (ev:Event):void {
            CouponWidget(ev.target).init();
        });
    }

    protected function init():void {
        if (ExternalInterface.available) {
            try{
                ExternalInterface.addCallback('useCoupon', useCoupon);
                ExternalInterface.addCallback('unsetCoupon', unsetCoupon);

            }catch (error:Error) {
                trace(error)
            }
        }
    }

    public function useCoupon(coupon:String):void {
        if (!coupon) {
            trace('Missing coupon code.');
            if (ExternalInterface.available) {
                try{
                    trace("invalidCouponCallback");
                    ExternalInterface.call("invalidCouponCallback", "Please provide coupon code.");
                }catch (error:Error) {
                    //trace(error)
                }
            }
        } else {
            trace("Start request \"useCoupon\"");
            _service.useCoupon(coupon).setResultHandler(onSuccessUseCoupon).setErrorHandler(errorHandler);
        }
    }

    private function onSuccessUseCoupon(result:Object, ...arg):void {
        trace("Successful useCoupon");
        trace(result is Object ? ObjectUtil.deepObjectToString(result) : result);
        trace(arg is Object ? ObjectUtil.deepObjectToString(arg): arg);

        if (ExternalInterface.available) {
            try{
                trace("successCouponCallback");
                ExternalInterface.call("successCouponCallback", result);
            }catch (error:Error) {
                trace("Cannot call JS");
                trace(error)
            }
        }
    }

    public function unsetCoupon():void {
        trace("Start request \"unsetCoupon\"");
        _service.unsetCoupon().setResultHandler(onSuccessUnsetCoupon).setErrorHandler(errorHandler);
    }

    private function onSuccessUnsetCoupon(result:Object, ...arg):void {
        trace("Successful unsetCoupon");
        trace(result is Object ? ObjectUtil.deepObjectToString(result) : result);
        trace(arg is Object ? ObjectUtil.deepObjectToString(arg): arg);

        if (ExternalInterface.available) {
            try{
                trace("successCouponCallback");
                ExternalInterface.call("successCouponUnsetCallback", result);
            }catch (error:Error) {
                trace("Cannot call JS");
                trace(error)
            }
        }
    }

    private function errorHandler(error:Object):void {
        trace(ObjectUtil.deepObjectToString(error));

        if (ExternalInterface.available) {
            try{
                trace("invalidCouponCallback");
                ExternalInterface.call("invalidCouponCallback", error);
            }catch (error:Error) {
                trace("Cannot call JS");
                trace(error)
            }
        }
    }
}
}
