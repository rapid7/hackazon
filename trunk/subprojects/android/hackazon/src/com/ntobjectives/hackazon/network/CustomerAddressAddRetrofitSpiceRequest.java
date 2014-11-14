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
public class CustomerAddressAddRetrofitSpiceRequest extends RetrofitSpiceRequest<CustomerAddress, Hackazon> {
    public static final String TAG = CustomerAddressAddRetrofitSpiceRequest.class.getSimpleName();
    protected CustomerAddress address;

    public CustomerAddressAddRetrofitSpiceRequest(CustomerAddress address) {
        super(CustomerAddress.class, Hackazon.class);
        this.address = address;
    }

    @Override
    public CustomerAddress loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Add customer address on network service.");
        return getService().addCustomerAddress(address);
    }
}
