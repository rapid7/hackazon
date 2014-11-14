package com.ntobjectives.hackazon.view;

import android.content.Context;
import android.util.AttributeSet;
import android.view.LayoutInflater;
import android.widget.LinearLayout;
import android.widget.TextView;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.model.Countries;
import com.ntobjectives.hackazon.model.CustomerAddress;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 30.10.2014
 * Time: 19:49
 */
public class CustomerAddressLayout extends LinearLayout {
    protected CustomerAddress address = new CustomerAddress();

    protected TextView fullNameField;
    protected TextView address1Field;
    protected TextView address2Field;
    protected TextView cityField;
    protected TextView regionField;
    protected TextView zipField;
    protected TextView phoneField;
    protected TextView countryField;
    protected TextView titleField;

    public CustomerAddressLayout(Context context) {
        super(context);
        inflateView(context);
    }

    private void inflateView(Context context) {
        LayoutInflater.from(context).inflate(R.layout.customer_address_view, this);
        fullNameField = (TextView) this.findViewById(R.id.fullName);
        address1Field = (TextView) this.findViewById(R.id.address1);
        address2Field = (TextView) this.findViewById(R.id.address2);
        cityField = (TextView) this.findViewById(R.id.city);
        regionField = (TextView) this.findViewById(R.id.region);
        zipField = (TextView) this.findViewById(R.id.zip);
        phoneField = (TextView) this.findViewById(R.id.phone);
        countryField = (TextView) this.findViewById(R.id.country);
        titleField = (TextView) this.findViewById(R.id.addressName);
    }

    public CustomerAddressLayout(Context context, AttributeSet attrs){
        super(context, attrs);
        inflateView(context);
    }

    public CustomerAddressLayout(Context context, AttributeSet attrs, int defStyle) {
        super(context, attrs, defStyle);
        inflateView(context);
    }

    public CustomerAddress getAddress() {
        return address;
    }

    public void setAddress(CustomerAddress address) {
        this.address = address;
        fullNameField.setText(address.full_name);
        address1Field.setText(address.address_line_1);
        address2Field.setText(address.address_line_2);
        cityField.setText(address.city);
        regionField.setText(address.region);
        zipField.setText(address.zip);
        phoneField.setText(address.phone);
        countryField.setText(Countries.getLabel(address.country_id));
    }

    public CustomerAddress getData() {
        return address;
    }

    public void setTitle(String title) {
        titleField.setText(title);
    }
}
