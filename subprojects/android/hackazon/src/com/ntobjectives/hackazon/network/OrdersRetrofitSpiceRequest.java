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
public class OrdersRetrofitSpiceRequest extends RetrofitSpiceRequest<Order.OrdersResponse, Hackazon>{
    public static final String TAG = OrdersRetrofitSpiceRequest.class.getSimpleName();
    protected int page = 1;

    public OrdersRetrofitSpiceRequest() {
        this(1);
    }

    public OrdersRetrofitSpiceRequest(int page) {
        super(Order.OrdersResponse.class, Hackazon.class);
        if (page <= 0) {
            throw new IllegalArgumentException("Page must be greater than 0");
        }
        this.page = page;

    }

    @Override
    public Order.OrdersResponse loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Load orders from network.");
        return getService().orders(page, 100);
    }

    public String createCacheKey() {
        return "hackazon.orders.page." + page;
    }
}
