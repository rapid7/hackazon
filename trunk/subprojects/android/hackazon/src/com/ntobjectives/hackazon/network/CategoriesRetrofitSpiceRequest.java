package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.ntobjectives.hackazon.model.Category;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class CategoriesRetrofitSpiceRequest extends RetrofitSpiceRequest<Category.CategoriesResponse, Hackazon> {
    public static final String TAG = CategoriesRetrofitSpiceRequest.class.getSimpleName();
    protected int page = 1;

    public CategoriesRetrofitSpiceRequest() {
        this(1);
    }

    public CategoriesRetrofitSpiceRequest(int page) {
        super(Category.CategoriesResponse.class, Hackazon.class);
        if (page <= 0) {
            throw new IllegalArgumentException("Page must be greater than 0");
        }
        this.page = page;
    }

    @Override
    public Category.CategoriesResponse loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Load categories from network.");
        return getService().categories(1, 1000);
    }

    public String createCacheKey() {
        return "hackazon.categories.page." + page;
    }
}
