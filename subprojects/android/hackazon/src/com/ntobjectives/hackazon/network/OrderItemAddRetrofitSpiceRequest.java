package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.ntobjectives.hackazon.model.OrderItem;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class OrderItemAddRetrofitSpiceRequest extends RetrofitSpiceRequest<OrderItem, Hackazon> {
    public static final String TAG = OrderItemAddRetrofitSpiceRequest.class.getSimpleName();
    protected OrderItem item;

    public OrderItemAddRetrofitSpiceRequest(OrderItem item) {
        super(OrderItem.class, Hackazon.class);
        this.item = item;
    }

    @Override
    public OrderItem loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Add order item on network service.");
        return getService().addOrderItem(item);
    }
}
