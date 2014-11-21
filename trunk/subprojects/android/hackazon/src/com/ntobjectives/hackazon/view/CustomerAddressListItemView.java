package com.ntobjectives.hackazon.view;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.RelativeLayout;
import android.widget.TextView;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.model.CustomerAddress;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 25.10.2014
 * Time: 17:35
 */
public class CustomerAddressListItemView extends RelativeLayout {
    private CustomerAddress address;
    protected View view;
    protected CustomerAddress item;
    protected TextView addressName;
    protected TextView fullName;
    protected TextView addressLine;
    protected TextView cityRegionCountryZIP;

    public CustomerAddressListItemView(Context context) {
        super(context);
        inflateView(context);
    }

    private void inflateView(Context context) {
        LayoutInflater.from(context).inflate(R.layout.customer_address_list_item_view, this);
        this.addressName = (TextView) this.findViewById(R.id.addressName);
        this.fullName = (TextView) this.findViewById(R.id.fullName);
        this.addressLine = (TextView) this.findViewById(R.id.address);
        this.cityRegionCountryZIP = (TextView) this.findViewById(R.id.location);
    }

    public void setAddress(CustomerAddress address) {
        this.address = address;
        this.fullName.setText(address.full_name);
        this.addressLine.setText(address.address_line_1 + ", " + address.address_line_2);
        this.cityRegionCountryZIP.setText(address.city + ", " + address.region + ", " + address.country_id
                + ", " + address.zip);
    }
}
