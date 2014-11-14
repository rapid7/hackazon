package com.ntobjectives.hackazon.adapter;

import android.content.Context;
import android.view.ViewGroup;
import com.ntobjectives.hackazon.model.Category;
import com.ntobjectives.hackazon.view.CategoryListItemView;
import com.octo.android.robospice.request.simple.IBitmapRequest;
import com.octo.android.robospice.spicelist.SpiceListItemView;
import com.octo.android.robospice.spicelist.okhttp.OkHttpBitmapSpiceManager;
import com.octo.android.robospice.spicelist.okhttp.OkHttpSpiceArrayAdapter;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 25.10.2014
 * Time: 17:16
 */
public class CategoryListAdapter extends OkHttpSpiceArrayAdapter<Category> {
    public CategoryListAdapter(Context context, OkHttpBitmapSpiceManager spiceManagerBinary, Category.List objects) {
        super(context, spiceManagerBinary, objects);
    }

    @Override
    public SpiceListItemView<Category> createView(Context context, ViewGroup viewGroup) {
        return new CategoryListItemView(getContext());
    }

    @Override
    public IBitmapRequest createRequest(Category category, int i, int i2, int i3) {
        return null;
    }
}
