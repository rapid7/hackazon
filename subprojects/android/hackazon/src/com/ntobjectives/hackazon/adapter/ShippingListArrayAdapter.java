package com.ntobjectives.hackazon.adapter;

import android.content.Context;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import com.ntobjectives.hackazon.model.Cart;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 30.10.2014
 * Time: 19:58
 */
public class ShippingListArrayAdapter extends ArrayAdapter<String> {
    public ShippingListArrayAdapter(Context context, int resource, String[] objects) {
        super(context, resource, objects);
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        TextView v = (TextView) super.getView(position, convertView, parent);
        String item = getItem(position);
        v.setText(Cart.ShippingMethods.getLabel(item));
        return v;
    }
}
