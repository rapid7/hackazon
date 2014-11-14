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
public class CartUpdateRetrofitSpiceRequest extends RetrofitSpiceRequest<Cart, Hackazon> {
    public static final String TAG = CartUpdateRetrofitSpiceRequest.class.getSimpleName();
    protected Cart item;

    public CartUpdateRetrofitSpiceRequest(Cart item) {
        super(Cart.class, Hackazon.class);
        this.item = item;
    }

    @Override
    public Cart loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Update cart " + item.id + " on network service.");
        return getService().updateCart(item.id, item);
    }
}
