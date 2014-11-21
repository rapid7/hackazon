package com.ntobjectives.hackazon.activity;

import android.os.Bundle;
import android.support.annotation.Nullable;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.dialog.CountriesDialogFragment;
import com.ntobjectives.hackazon.dialog.CustomerAddressDialogFragment;
import com.ntobjectives.hackazon.model.Countries;
import com.ntobjectives.hackazon.model.CustomerAddress;

import java.util.ArrayList;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 17.10.2014
 * Time: 14:50
 */
public class CheckoutBillingAddressFragment extends CheckoutBaseFragment
        implements CountriesDialogFragment.CountriesDialogListener,
        CustomerAddressDialogFragment.CustomerAddressDialogListener {
    public static final String TAG = CheckoutBillingAddressFragment.class.getSimpleName();

    protected TextView addressSelector;

    protected EditText fullNameField;
    protected EditText address1Field;
    protected EditText address2Field;
    protected EditText cityField;
    protected EditText regionField;
    protected EditText zipField;
    protected EditText phoneField;
    protected TextView countryField;

    protected Button prevButton;
    protected Button nextButton;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        return doOnCreateView(inflater, container, savedInstanceState, R.layout.checkout_address_fragment);
    }

    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        Log.d(TAG, "onActivityCreated");
        super.onActivityCreated(savedInstanceState);
        fullNameField = (EditText) getActivity().findViewById(R.id.fullName);
        address1Field = (EditText) getActivity().findViewById(R.id.address1);
        address2Field = (EditText) getActivity().findViewById(R.id.address2);
        cityField = (EditText) getActivity().findViewById(R.id.city);
        regionField = (EditText) getActivity().findViewById(R.id.region);
        zipField = (EditText) getActivity().findViewById(R.id.zip);
        phoneField = (EditText) getActivity().findViewById(R.id.phone);
        countryField = (TextView) getActivity().findViewById(R.id.country);

        addressSelector = (TextView) getActivity().findViewById(R.id.existingAddressSelector);

        nextButton = (Button) getActivity().findViewById(R.id.nextButton);
        prevButton = (Button) getActivity().findViewById(R.id.prevButton);
        loadingView = getActivity().findViewById(R.id.loading_layout);
        loadingView.setVisibility(View.GONE);

        nextButton.setText("Confirmation >");
        prevButton.setText("< Ship. Addr.");

        prevButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                saveAddress();
                activity.selectStep(CheckoutActivity.Steps.SHIPPING_ADDRESS);
            }
        });

        nextButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (checkFields()) {
                    saveAddress();
                    activity.selectStep(CheckoutActivity.Steps.CONFIRMATION);
                }
            }
        });

        countryField.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                CountriesDialogFragment dialog = new CountriesDialogFragment();
                dialog.setTargetFragment(CheckoutBillingAddressFragment.this, 0);
                android.app.FragmentManager fm = getFragmentManager();
                dialog.show(fm, TAG);
            }
        });

        if (((CheckoutActivity)getActivity()).customerAddresses.size() == 0) {
            addressSelector.setVisibility(View.GONE);
        } else {
            addressSelector.setVisibility(View.VISIBLE);
        }

        addressSelector.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                CustomerAddressDialogFragment dialog = new CustomerAddressDialogFragment();
                dialog.setTargetFragment(CheckoutBillingAddressFragment.this, 0);
                dialog.setCustomerAddresses(
                        ((CheckoutActivity)CheckoutBillingAddressFragment.this.getActivity()).customerAddresses);
                android.app.FragmentManager fm = getFragmentManager();
                dialog.show(fm, TAG);
            }
        });

        getActivity().setProgressBarIndeterminateVisibility(false);
    }

    public boolean checkFields() {
        ArrayList<String> errors = new ArrayList<String>();

        if (fullNameField.getText().toString().equals("")) {
            errors.add("Please enter your name");
        }

        if (address1Field.getText().toString().equals("")) {
            errors.add("Please enter your address");
        }

        if (cityField.getText().toString().equals("")) {
            errors.add("Please enter your city");
        }

        if (regionField.getText().toString().equals("")) {
            errors.add("Please enter your region");
        }

        if (zipField.getText().toString().equals("")) {
            errors.add("Please enter your ZIP");
        }

        if (errors.size() > 0) {
            StringBuilder sb = new StringBuilder();
            for (String er : errors) {
                sb.append(er).append("\n");
            }
            Toast.makeText(activity, sb.toString().trim(), Toast.LENGTH_SHORT).show();
            return false;
        }
        return true;
    }

    public void saveAddress() {
        CustomerAddress address = activity.getBillingAddress();
        address.full_name = fullNameField.getText().toString();
        address.address_line_1 = address1Field.getText().toString();
        address.address_line_2 = address2Field.getText().toString();
        address.city = cityField.getText().toString();
        address.region = regionField.getText().toString();
        address.zip = zipField.getText().toString();
        address.phone = phoneField.getText().toString();

        CustomerAddress.List addresses = ((CheckoutActivity)CheckoutBillingAddressFragment.this.getActivity()).customerAddresses;

        if (!addresses.contains(address)) {
            addresses.add(address);
        }
    }

    @Override
    public void onStart() {
        Log.d(TAG, "onStart");
        super.onStart();
        getActivity().setTitle("Billing Address");

        CustomerAddress address = activity.getBillingAddress();

        Log.d(TAG, "Billing Address: " + address);
        if (address == null) {
            if (activity.getShippingAddress() != null) {
                try {
                    address = activity.getShippingAddress().clone();
                } catch (CloneNotSupportedException ex) {
                    address = new CustomerAddress();
                }

            } else {
                address = new CustomerAddress();
            }
            activity.setBillingAddress(address);
        }

        populateFields(address);
    }

    @Override
    public void onDialogSelect(CountriesDialogFragment dialog, String country) {
        countryField.setText(Countries.getLabel(country));
        activity.getBillingAddress().country_id = country;
    }

    @Override
    public void onAddressDialogSelect(CustomerAddressDialogFragment dialog, CustomerAddress address) {
        populateFields(address);
    }

    protected void populateFields(CustomerAddress address) {
        if (address.country_id == null || address.country_id.equals("")) {
            address.country_id = Countries.RU;
        }

        countryField.setText(Countries.getLabel(address.country_id));

        if (address.full_name != null) {
            fullNameField.setText(address.full_name);
        }

        if (address.address_line_1 != null) {
            address1Field.setText(address.address_line_1);
        }

        if (address.address_line_2 != null) {
            address2Field.setText(address.address_line_2);
        }

        if (address.city != null) {
            cityField.setText(address.city);
        }

        if (address.region != null) {
            regionField.setText(address.region);
        }

        if (address.zip != null) {
            zipField.setText(address.zip);
        }

        if (address.phone != null) {
            phoneField.setText(address.phone);
        }
    }
}