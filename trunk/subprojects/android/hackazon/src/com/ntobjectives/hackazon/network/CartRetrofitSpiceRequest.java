package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.ntobjectives.hackazon.model.Cart;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class CartRetrofitSpiceRequest extends RetrofitSpiceRequest<Cart, Hackazon> {
    public static final String TAG = CartRetrofitSpiceRequest.class.getSimpleName();
    protected String uid;

    public CartRetrofitSpiceRequest() {
        this("");
    }

    public CartRetrofitSpiceRequest(String uid) {
        super(Cart.class, Hackazon.class);
        this.uid = uid;
    }

    @Override
    public Cart loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Load cart from network.");
        return getService().myCart(uid);
    }

    public String createCacheKey() {
        return "hackazon.cart." + uid;
    }
}
