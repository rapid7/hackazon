package com.ntobjectives.hackazon.adapter;

import android.content.Context;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import com.ntobjectives.hackazon.model.CustomerAddress;
import com.ntobjectives.hackazon.view.CustomerAddressListItemView;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 30.10.2014
 * Time: 19:58
 */
public class CustomerAddressListArrayAdapter extends ArrayAdapter<CustomerAddress> {
    public CustomerAddressListArrayAdapter(Context context, int resource, CustomerAddress.List objects) {
        super(context, resource, objects);
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        CustomerAddressListItemView v = new CustomerAddressListItemView(getContext());
        CustomerAddress item = getItem(position);
        v.setAddress(item);
        return v;
    }
}
