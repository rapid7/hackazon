package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.ntobjectives.hackazon.model.CartItem;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class CartItemAddRetrofitSpiceRequest extends RetrofitSpiceRequest<CartItem, Hackazon> {
    public static final String TAG = CartItemAddRetrofitSpiceRequest.class.getSimpleName();
    protected CartItem item;

    public CartItemAddRetrofitSpiceRequest(CartItem item) {
        super(CartItem.class, Hackazon.class);
        this.item = item;
    }

    @Override
    public CartItem loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Add cart item on network service.");
        return getService().addCartItem(item);
    }
}
