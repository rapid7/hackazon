package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.ntobjectives.hackazon.model.Order;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class OrderRetrofitSpiceRequest extends RetrofitSpiceRequest<Order, Hackazon> {
    public static final String TAG = OrderRetrofitSpiceRequest.class.getSimpleName();
    protected int id = 1;

    public OrderRetrofitSpiceRequest(int id) {
        super(Order.class, Hackazon.class);
        if (id <= 0) {
            throw new IllegalArgumentException("Id must be greater than 0");
        }
        this.id = id;
    }

    @Override
    public Order loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Load order " + id + " from network.");
        return getService().order(Integer.toString(id));
    }

    public String createCacheKey() {
        return "hackazon.orders." + id;
    }
}
