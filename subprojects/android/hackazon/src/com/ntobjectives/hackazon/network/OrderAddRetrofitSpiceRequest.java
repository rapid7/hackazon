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
public class OrderAddRetrofitSpiceRequest extends RetrofitSpiceRequest<Order, Hackazon> {
    public static final String TAG = OrderAddRetrofitSpiceRequest.class.getSimpleName();
    protected Order order;

    public OrderAddRetrofitSpiceRequest(Order order) {
        super(Order.class, Hackazon.class);
        this.order = order;
    }

    @Override
    public Order loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Add order on network service.");
        return getService().addOrder(order);
    }
}
