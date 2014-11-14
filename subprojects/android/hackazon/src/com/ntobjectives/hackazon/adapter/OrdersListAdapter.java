package com.ntobjectives.hackazon.adapter;

import android.content.Context;
import android.view.ViewGroup;
import com.ntobjectives.hackazon.model.Order;
import com.ntobjectives.hackazon.view.OrderListItemView;
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
public class OrdersListAdapter extends OkHttpSpiceArrayAdapter<Order> {
    public OrdersListAdapter(Context context, OkHttpBitmapSpiceManager spiceManagerBinary, Order.List objects) {
        super(context, spiceManagerBinary, objects);
    }

    @Override
    public SpiceListItemView<Order> createView(Context context, ViewGroup viewGroup) {
        return new OrderListItemView(getContext());
    }

    @Override
    public IBitmapRequest createRequest(Order order, int i, int i2, int i3) {
        return null;
    }
}
