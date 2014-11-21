package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.ntobjectives.hackazon.model.CustomerAddress;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class CustomerAddressesRetrofitSpiceRequest extends RetrofitSpiceRequest<CustomerAddress.CustomerAddressesResponse, Hackazon>{
    public static final String TAG = CustomerAddressesRetrofitSpiceRequest.class.getSimpleName();
    protected int page = 1;

    public CustomerAddressesRetrofitSpiceRequest() {
        this(1);
    }

    public CustomerAddressesRetrofitSpiceRequest(int page) {
        super(CustomerAddress.CustomerAddressesResponse.class, Hackazon.class);
        if (page <= 0) {
            throw new IllegalArgumentException("Page must be greater than 0");
        }
        this.page = page;
    }

    @Override
    public CustomerAddress.CustomerAddressesResponse loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Load customer addresses from network.");
        return getService().customerAddresses(page, 100);
    }

    public String createCacheKey() {
        return "hackazon.customerAddresses.page." + page;
    }
}
