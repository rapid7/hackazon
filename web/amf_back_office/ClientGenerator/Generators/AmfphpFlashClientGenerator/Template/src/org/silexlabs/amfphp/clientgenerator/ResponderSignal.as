package org.silexlabs.amfphp.clientgenerator
{
	public class ResponderSignal implements IResponderSignal {
		
		private var _onResult:Function;
		
		private var _onError:Function;
		
		private var _received:Boolean = false
		
		private var _isError:Boolean = false;
		
		private var _data:Object = null;
		
		public function setResultHandler(callback:Function):IResponderSignal{
			this._onResult = callback;
			if (_received && !_isError && null != this._onResult)  {
				this._onResult(_data);
			}
			return this;
		}
		
		public function setErrorHandler(callback:Function):IResponderSignal{
			this._onError = callback;
			if (_received && _isError && null != this._onError)  {
				this._onError(_data);
			}
			return this;
		}
		
		public function handleResult(result:Object):void {
			_data= result;
			_isError = false;
			_received = true;
			if (null != _onResult) {
				_onResult(result);
			}
		}
		
		public function handleError(error:Object):void {
			_data= error;
			_isError = true;
			_received = true;
			if (null != _onError) {
				_onError(error);
			}
		}
		
	}
}