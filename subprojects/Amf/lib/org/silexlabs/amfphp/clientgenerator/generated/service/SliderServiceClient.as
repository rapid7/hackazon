package org.silexlabs.amfphp.clientgenerator.generated.service{
    import flash.net.NetConnection;
    import flash.net.Responder;
    import org.silexlabs.amfphp.clientgenerator.AMFPHPServiceClient;
    import org.silexlabs.amfphp.clientgenerator.IResponderSignal;

    /**  */
    public class SliderServiceClient extends AMFPHPServiceClient {
        /**
        * constructor.
        * @param NetConnection nc. instantiate the nc, call the connect() method on it with the amfPHP server url, .
        * maybe add some event listeners for error events, then pass it here. 
        **/
        public function SliderServiceClient(nc:NetConnection){
			super(nc, "SliderService");
        }
		

/** 
*   @param int $num
*   @return array
*   @throws \InvalidArgumentException
*   */
        public function getSlides(num:int):IResponderSignal{ 
			return callService("getSlides" , num);
        }

                
        
    }
}
