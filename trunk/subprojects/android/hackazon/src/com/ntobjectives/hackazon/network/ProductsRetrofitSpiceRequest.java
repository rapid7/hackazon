package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.ntobjectives.hackazon.model.Product;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;
import retrofit.RetrofitError;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class ProductsRetrofitSpiceRequest extends RetrofitSpiceRequest<Product.ProductsResponse, Hackazon> {
    public static final String TAG = ProductsRetrofitSpiceRequest.class.getSimpleName();
    protected int page = 1;
    protected int categoryId = 0;
    boolean needAuth = false;

    public ProductsRetrofitSpiceRequest() {
        this(1);
    }

    public ProductsRetrofitSpiceRequest(int page) {
        this(page, 0);
    }

    public ProductsRetrofitSpiceRequest(int page, int categoryId) {
        super(Product.ProductsResponse.class, Hackazon.class);
        if (page <= 0) {
            throw new IllegalArgumentException("Page must be greater than 0");
        }
        this.page = page;
        this.categoryId = categoryId;
    }

    @Override
    public Product.ProductsResponse loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Load products from network.");
        try {
            if (categoryId > 0) {
                return getService().products(page, categoryId);
            } else {
                return getService().products(page);
            }
        } catch (RetrofitError e) {
            if (e.getResponse().getStatus() == 401) {
                needAuth = true;
            }
            throw e;
        }
    }

    public String createCacheKey() {
        return "hackazon.products.category." + categoryId + ".page." + page;
    }

    public boolean isNeedAuth() {
        return needAuth;
    }
}
