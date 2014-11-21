package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class CartItemDeleteRetrofitSpiceRequest extends RetrofitSpiceRequest<String, Hackazon> {
    public static final String TAG = CartItemDeleteRetrofitSpiceRequest.class.getSimpleName();
    protected int id;

    public CartItemDeleteRetrofitSpiceRequest(int id) {
        super(String.class, Hackazon.class);
        this.id = id;
    }

    @Override
    public String loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Delete cart item.");
        getService().deleteCartItem(id);
        return "";
    }
}
