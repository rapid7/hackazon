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
public class CartItemUpdateRetrofitSpiceRequest extends RetrofitSpiceRequest<CartItem, Hackazon> {
    public static final String TAG = CartItemUpdateRetrofitSpiceRequest.class.getSimpleName();
    protected CartItem item;

    public CartItemUpdateRetrofitSpiceRequest(CartItem item) {
        super(CartItem.class, Hackazon.class);
        this.item = item;
    }

    @Override
    public CartItem loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Update cart item " + item.id + " on network service.");
        return getService().updateCartItem(item.id, item);
    }
}
