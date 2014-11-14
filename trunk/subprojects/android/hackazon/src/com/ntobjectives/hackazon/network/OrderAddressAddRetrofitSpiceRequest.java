package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.ntobjectives.hackazon.model.OrderAddress;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class OrderAddressAddRetrofitSpiceRequest extends RetrofitSpiceRequest<OrderAddress, Hackazon> {
    public static final String TAG = OrderAddressAddRetrofitSpiceRequest.class.getSimpleName();
    protected OrderAddress address;

    public OrderAddressAddRetrofitSpiceRequest(OrderAddress address) {
        super(OrderAddress.class, Hackazon.class);
        this.address = address;
    }

    @Override
    public OrderAddress loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Add order address on network service.");
        return getService().addOrderAddress(address);
    }
}
