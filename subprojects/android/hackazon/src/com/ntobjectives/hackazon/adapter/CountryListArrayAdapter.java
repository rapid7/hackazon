package com.ntobjectives.hackazon.adapter;

import android.content.Context;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import com.ntobjectives.hackazon.model.Countries;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 30.10.2014
 * Time: 19:58
 */
public class CountryListArrayAdapter extends ArrayAdapter<String> {
    public CountryListArrayAdapter(Context context, int resource, String[] objects) {
        super(context, resource, objects);
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        TextView v = (TextView) super.getView(position, convertView, parent);
        String item = getItem(position);
        v.setText(Countries.getLabel(item));
        return v;
    }
}
