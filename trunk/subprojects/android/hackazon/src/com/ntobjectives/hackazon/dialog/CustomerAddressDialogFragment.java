package com.ntobjectives.hackazon.dialog;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.DialogFragment;
import android.content.DialogInterface;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.util.Log;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.activity.AbstractRootActivity;
import com.ntobjectives.hackazon.adapter.CustomerAddressListArrayAdapter;
import com.ntobjectives.hackazon.model.CustomerAddress;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 27.10.2014
 * Time: 21:38
 */
public class CustomerAddressDialogFragment extends DialogFragment {
    protected static final String TAG = CustomerAddressDialogFragment.class.getSimpleName();
    protected CustomerAddress.List customerAddresses;

    public interface CustomerAddressDialogListener {
        public void onAddressDialogSelect(CustomerAddressDialogFragment dialog, CustomerAddress address);
    }

    CustomerAddressDialogListener listener;

    @NonNull
    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        AbstractRootActivity activity = (AbstractRootActivity) getActivity();

        AlertDialog.Builder builder = new AlertDialog.Builder(activity);
        final CustomerAddressListArrayAdapter adapter = new CustomerAddressListArrayAdapter(activity,
                R.layout.shipping_method_list_item, customerAddresses);

        builder.setTitle(activity.getString(R.string.checkout_select_address))
                .setAdapter(adapter, new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        Log.d(TAG, "Selected address " + which);
                        listener.onAddressDialogSelect(CustomerAddressDialogFragment.this, customerAddresses.get(which));
                    }
                });

        return builder.create();
    }

    @Override
    public void onAttach(Activity activity) {
        super.onAttach(activity);

        try {
            // Instantiate the NoticeDialogListener so we can send events to the host
            listener = (CustomerAddressDialogListener) getTargetFragment();

        } catch (ClassCastException e) {
            // The activity doesn't implement the interface, throw exception
            throw new ClassCastException(getTargetFragment().toString()
                    + " must implement CustomerAddressDialogListener");
        }
    }

    public CustomerAddress.List getCustomerAddresses() {
        return customerAddresses;
    }

    public void setCustomerAddresses(CustomerAddress.List customerAddresses) {
        this.customerAddresses = customerAddresses;
    }
}
