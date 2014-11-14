package com.ntobjectives.hackazon.adapter;

import android.content.Context;
import android.view.ViewGroup;
import com.ntobjectives.hackazon.model.CartItem;
import com.ntobjectives.hackazon.view.CartItemListItemView;
import com.octo.android.robospice.request.simple.IBitmapRequest;
import com.octo.android.robospice.spicelist.SpiceListItemView;
import com.octo.android.robospice.spicelist.okhttp.OkHttpBitmapSpiceManager;
import com.octo.android.robospice.spicelist.okhttp.OkHttpSpiceArrayAdapter;

import java.util.ArrayList;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 25.10.2014
 * Time: 17:16
 */
public class CartItemsListAdapter extends OkHttpSpiceArrayAdapter<CartItem> {
    public CartItemsListAdapter(Context context, OkHttpBitmapSpiceManager spiceManagerBinary, ArrayList<CartItem> objects) {
        super(context, spiceManagerBinary, objects);
    }

    @Override
    public SpiceListItemView<CartItem> createView(Context context, ViewGroup viewGroup) {
        return new CartItemListItemView(getContext());
    }

    @Override
    public IBitmapRequest createRequest(CartItem item, int i, int i2, int i3) {
        return null;
    }
}
