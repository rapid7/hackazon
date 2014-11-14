package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.ntobjectives.hackazon.model.Product;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class ProductRetrofitSpiceRequest extends RetrofitSpiceRequest<Product, Hackazon> {
    public static final String TAG = ProductRetrofitSpiceRequest.class.getSimpleName();
    protected int id = 1;

    public ProductRetrofitSpiceRequest(int id) {
        super(Product.class, Hackazon.class);
        if (id <= 0) {
            throw new IllegalArgumentException("Id must be greater than 0");
        }
        this.id = id;
    }

    @Override
    public Product loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Load product " + id + " from network.");
        return getService().product(Integer.toString(id));
    }

    public String createCacheKey() {
        return "hackazon.product." + id;
    }
}
