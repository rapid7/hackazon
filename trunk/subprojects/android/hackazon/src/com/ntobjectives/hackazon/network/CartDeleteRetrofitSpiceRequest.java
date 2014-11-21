package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class CartDeleteRetrofitSpiceRequest extends RetrofitSpiceRequest<Void, Hackazon> {
    public static final String TAG = CartDeleteRetrofitSpiceRequest.class.getSimpleName();
    protected int id;

    public CartDeleteRetrofitSpiceRequest(int id) {
        super(Void.class, Hackazon.class);
        this.id = id;
    }

    @Override
    public Void loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Delete cart.");
        return getService().deleteCart(id);
    }
}
