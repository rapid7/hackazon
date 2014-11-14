package com.ntobjectives.hackazon.activity;

import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.dialog.PaymentMethodDialogFragment;
import com.ntobjectives.hackazon.dialog.ShippingMethodDialogFragment;
import com.ntobjectives.hackazon.model.Cart;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 17.10.2014
 * Time: 14:50
 */
public class CheckoutMethodsFragment extends CheckoutBaseFragment
        implements ShippingMethodDialogFragment.ShippingMethodDialogListener,
        PaymentMethodDialogFragment.PaymentMethodDialogListener {
    public static final String TAG = CheckoutMethodsFragment.class.getSimpleName();

    protected TextView shippingMethodField;
    protected TextView paymentMethodField;
    protected Button goToCartButton;
    protected Button shippingAddressButton;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        return doOnCreateView(inflater, container, savedInstanceState, R.layout.checkout_methods_fragment);
    }

    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        Log.d(TAG, "onActivityCreated");
        super.onActivityCreated(savedInstanceState);
        shippingMethodField = (TextView) getActivity().findViewById(R.id.shippingMethod);
        paymentMethodField = (TextView) getActivity().findViewById(R.id.paymentMethod);
        shippingAddressButton = (Button) getActivity().findViewById(R.id.shippingMethodButton);
        goToCartButton = (Button) getActivity().findViewById(R.id.goToCartButton);
        loadingView = getActivity().findViewById(R.id.loading_layout);
        loadingView.setVisibility(View.GONE);

        goToCartButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(CheckoutMethodsFragment.this.getActivity(), MainActivity.class);
                intent.putExtra("page", "cart");
                startActivity(intent);
            }
        });

        shippingAddressButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                activity.selectStep(CheckoutActivity.Steps.SHIPPING_ADDRESS);
            }
        });

        shippingMethodField.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                ShippingMethodDialogFragment dialog = new ShippingMethodDialogFragment();
                dialog.setTargetFragment(CheckoutMethodsFragment.this, 0);
                android.app.FragmentManager fm = getFragmentManager();
                dialog.show(fm, TAG);
            }
        });

        paymentMethodField.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                PaymentMethodDialogFragment dialog = new PaymentMethodDialogFragment();
                dialog.setTargetFragment(CheckoutMethodsFragment.this, 0);
                android.app.FragmentManager fm = getFragmentManager();
                dialog.show(fm, TAG);
            }
        });

        getActivity().setProgressBarIndeterminateVisibility(false);
    }

    @Override
    public void onStart() {
        Log.d(TAG, "onStart");
        super.onStart();
        getActivity().setTitle("Shipping and payment methods");

        if (cart.shipping_method == null || cart.shipping_method.equals("")
                || !Cart.ShippingMethods.getLabels().containsKey(cart.shipping_method)
        ) {
            cart.shipping_method = Cart.ShippingMethods.MAIL;
        }
        if (cart.payment_method == null || cart.payment_method.equals("")
                || !Cart.PaymentMethods.getLabels().containsKey(cart.payment_method)
        ) {
            cart.payment_method = Cart.PaymentMethods.CREDIT_CARD;
        }
        shippingMethodField.setText(Cart.ShippingMethods.getLabel(cart.shipping_method));
        paymentMethodField.setText(Cart.PaymentMethods.getLabel(cart.payment_method));
    }

    @Override
    public void onDialogSelect(PaymentMethodDialogFragment dialog, String method) {
        paymentMethodField.setText(Cart.PaymentMethods.getLabel(method));
        cart.payment_method = method;
    }

    @Override
    public void onDialogSelect(ShippingMethodDialogFragment dialog, String method) {
        shippingMethodField.setText(Cart.ShippingMethods.getLabel(method));
        cart.shipping_method = method;
    }
}