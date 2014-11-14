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
import com.ntobjectives.hackazon.adapter.PaymentListArrayAdapter;
import com.ntobjectives.hackazon.model.Cart;

import java.util.Arrays;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 27.10.2014
 * Time: 21:38
 */
public class PaymentMethodDialogFragment extends DialogFragment {
    protected static final String TAG = PaymentMethodDialogFragment.class.getSimpleName();

    public interface PaymentMethodDialogListener {
        public void onDialogSelect(PaymentMethodDialogFragment dialog, String method);
    }

    PaymentMethodDialogListener listener;

    @NonNull
    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        //AbstractRootActivity activity = (AbstractRootActivity) getActivity();

        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        Object[] rawVals = Cart.PaymentMethods.getLabels().keySet().toArray();
        final String[] values = Arrays.copyOf(rawVals, rawVals.length, String[].class);
        final PaymentListArrayAdapter adapter = new PaymentListArrayAdapter(getActivity(), R.layout.shipping_method_list_item,
                values);

        builder.setTitle(getActivity().getString(R.string.select_payment_method))
                .setAdapter(adapter, new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        Log.d(TAG, "Selected payment method " + values[which]);
                        listener.onDialogSelect(PaymentMethodDialogFragment.this, values[which]);
                    }
                });

        return builder.create();
    }

    @Override
    public void onAttach(Activity activity) {
        super.onAttach(activity);

        try {
            // Instantiate the NoticeDialogListener so we can send events to the host
            listener = (PaymentMethodDialogListener) getTargetFragment();

        } catch (ClassCastException e) {
            // The activity doesn't implement the interface, throw exception
            throw new ClassCastException(getTargetFragment().toString()
                    + " must implement PaymentMethodDialogListener");
        }
    }
}
