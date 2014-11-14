package com.ntobjectives.hackazon.adapter;

import android.content.Context;
import android.view.ViewGroup;
import com.ntobjectives.hackazon.model.OrderItem;
import com.ntobjectives.hackazon.view.OrderItemListItemView;
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
public class OrderItemsListAdapter extends OkHttpSpiceArrayAdapter<OrderItem> {
    public OrderItemsListAdapter(Context context, OkHttpBitmapSpiceManager spiceManagerBinary, ArrayList<OrderItem> objects) {
        super(context, spiceManagerBinary, objects);
    }

    @Override
    public SpiceListItemView<OrderItem> createView(Context context, ViewGroup viewGroup) {
        return new OrderItemListItemView(getContext());
    }

    @Override
    public IBitmapRequest createRequest(OrderItem item, int i, int i2, int i3) {
        return null;
    }
}
