package org.silexlabs.amfphp.clientgenerator.generated.service{
    import flash.net.NetConnection;
    import flash.net.Responder;
    import org.silexlabs.amfphp.clientgenerator.AMFPHPServiceClient;
    import org.silexlabs.amfphp.clientgenerator.IResponderSignal;

    /** 
*   Coupon service. Provides method for operating coupons
*   @package AmfphpModule\Services
*   */
    public class CouponServiceClient extends AMFPHPServiceClient {
        /**
        * constructor.
        * @param NetConnection nc. instantiate the nc, call the connect() method on it with the amfPHP server url, .
        * maybe add some event listeners for error events, then pass it here. 
        **/
        public function CouponServiceClient(nc:NetConnection){
			super(nc, "CouponService");
        }
		

/** 
*   Set discount on cart if correct coupon code is entered
*   @param string $couponCode
*   @throws \App\Exception\NotFoundException
*   @throws \App\Exception\HttpException
*   @return array Coupon code and respective discount
*   */
        public function useCoupon(couponCode:String):IResponderSignal{ 
			return callService("useCoupon" , couponCode);
        }

/** 
*   @return void
*   */
        public function unsetCoupon():IResponderSignal{ 
			return callService("unsetCoupon" );
        }

                
        
    }
}
