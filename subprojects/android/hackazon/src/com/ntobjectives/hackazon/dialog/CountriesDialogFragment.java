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
import com.ntobjectives.hackazon.adapter.CountryListArrayAdapter;
import com.ntobjectives.hackazon.model.Countries;

import java.util.Arrays;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 27.10.2014
 * Time: 21:38
 */
public class CountriesDialogFragment extends DialogFragment {
    protected static final String TAG = CountriesDialogFragment.class.getSimpleName();

    public interface CountriesDialogListener {
        public void onDialogSelect(CountriesDialogFragment dialog, String country);
    }

    CountriesDialogListener listener;

    @NonNull
    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        AbstractRootActivity activity = (AbstractRootActivity) getActivity();

        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        Object[] rawValues = Countries.getLabels().keySet().toArray();
        final String[] values = Arrays.copyOf(rawValues, rawValues.length, String[].class);
        final CountryListArrayAdapter adapter = new CountryListArrayAdapter(getActivity(), R.layout.country_list_item,
                values);

        builder.setTitle(getActivity().getString(R.string.checkout_shipping_address_select_country))
                .setAdapter(adapter, new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        Log.d(TAG, "Selected country " + values[which]);
                        listener.onDialogSelect(CountriesDialogFragment.this, values[which]);
                    }
                });

        return builder.create();
    }

    @Override
    public void onAttach(Activity activity) {
        super.onAttach(activity);

        try {
            // Instantiate the NoticeDialogListener so we can send events to the host
            listener = (CountriesDialogListener) getTargetFragment();

        } catch (ClassCastException e) {
            // The activity doesn't implement the interface, throw exception
            throw new ClassCastException(getTargetFragment().toString()
                    + " must implement ShippingMethodDialogListener");
        }
    }
}
