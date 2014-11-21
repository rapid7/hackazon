package com.ntobjectives.hackazon.activity;

import android.app.Fragment;
import android.app.FragmentManager;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.View;
import android.widget.Toast;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.model.*;
import com.ntobjectives.hackazon.network.CartRetrofitSpiceRequest;
import com.ntobjectives.hackazon.network.CustomerAddressesRetrofitSpiceRequest;
import com.ntobjectives.hackazon.network.MeRetrofitSpiceRequest;
import com.octo.android.robospice.persistence.exception.SpiceException;

import static com.ntobjectives.hackazon.activity.CheckoutActivity.Steps.METHODS;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 30.10.2014
 * Time: 17:00
 */
public class CheckoutActivity extends AbstractRootActivity {
    protected Cart cart;
    protected User user;
    protected CustomerAddress shippingAddress;
    protected CustomerAddress billingAddress;
    protected CustomerAddress.List customerAddresses;

    protected OrderAddress.List orderAddresses;
    //protected View contentFrame;
    protected View loadingView;
    protected CartRetrofitSpiceRequest req;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (getIntent().getBooleanExtra("EXIT", false)) {
            finish();
        }

        setContentView(R.layout.activity_checkout);
        loadingView = findViewById(R.id.loading_layout);
        loadingView.setVisibility(View.VISIBLE);
        loadCartAndUser();
    }

    public enum Steps {
        METHODS,
        SHIPPING_ADDRESS,
        BILLING_ADDRESS,
        CONFIRMATION,
        PAYMENT,
        SUCCESS,
        ERROR
    }

    public void selectStep(Steps step) {
        // update the main content by replacing fragments
        Fragment fragment;
        Bundle args = new Bundle();

        switch (step) {
            case METHODS:
                fragment = new CheckoutMethodsFragment();

                break;
            case SHIPPING_ADDRESS:
                fragment = new CheckoutShippingAddressFragment();

                break;
            case BILLING_ADDRESS:
                fragment = new CheckoutBillingAddressFragment();

                break;
            case CONFIRMATION:
                fragment = new CheckoutConfirmationFragment();

                break;
            case PAYMENT:
                fragment = new CartFragment();

                break;
            case SUCCESS:
                fragment = new CheckoutSuccessFragment();

                break;
            case ERROR:
                fragment = new CartFragment();

                break;
            default:
                fragment = new Fragment();
                break;
        }

        fragment.setArguments(args);
        FragmentManager fragmentManager = getFragmentManager();
        fragmentManager.beginTransaction().replace(R.id.contentFrame, fragment).commit();
    }

    public void startSteps() {
        if (cart != null && user != null && customerAddresses != null) {
            loadingView.setVisibility(View.GONE);
            selectStep(METHODS);
        }
    }

    public Cart getCart() {
        return cart;
    }

    public User getUser() {
        return user;
    }

    public CustomerAddress getShippingAddress() {
        return shippingAddress;
    }

    public CustomerAddress getBillingAddress() {
        return billingAddress;
    }

    public void setShippingAddress(CustomerAddress shippingAddress) {
        this.shippingAddress = shippingAddress;
    }

    public void setBillingAddress(CustomerAddress billingAddress) {
        this.billingAddress = billingAddress;
    }

    public OrderAddress.List getOrderAddresses() {
        return orderAddresses;
    }

    @Override
    protected void onNewIntent(Intent intent) {
        String page = intent.getStringExtra("page");
        Log.d(TAG, "Intent to page: " + page);
    }

    protected void loadCartAndUser() {
        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(this);
        loadCartAndUser(prefs.getString("cart_uid", ""));
    }

    protected void loadCartAndUser(String uid) {
        setProgressBarIndeterminateVisibility(true);
        req = new CartRetrofitSpiceRequest(uid);
        getSpiceManager().execute(req, new CartRequestListener(this));
        getSpiceManager().execute(new MeRetrofitSpiceRequest(), new MeRequestListener(this));
        getSpiceManager().execute(new CustomerAddressesRetrofitSpiceRequest(), new CustomerAddressesRequestListener(this));
    }

    public final class CartRequestListener extends com.ntobjectives.hackazon.network.RequestListener<Cart> {

        protected CartRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException spiceException) {
            setProgressBarIndeterminateVisibility(false);
            Toast.makeText(CheckoutActivity.this, "failure", Toast.LENGTH_SHORT).show();
        }

        @Override
        public void onSuccess(Cart cart) {
            setProgressBarIndeterminateVisibility(false);
            CheckoutActivity.this.cart = cart;
            int itemsCount = 0;
            for (CartItem item : cart.items) {
                itemsCount += item.qty;
            }
            cart.items_count = itemsCount;
            cart.items_qty = itemsCount;

            startSteps();
        }
    }

    public final class MeRequestListener extends com.ntobjectives.hackazon.network.RequestListener<User> {

        protected MeRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException spiceException) {
            setProgressBarIndeterminateVisibility(false);
            Toast.makeText(CheckoutActivity.this, "failure", Toast.LENGTH_SHORT).show();
        }

        @Override
        public void onSuccess(User user) {
            setProgressBarIndeterminateVisibility(false);
            CheckoutActivity.this.user = user;
            startSteps();
        }
    }


    public final class CustomerAddressesRequestListener
            extends com.ntobjectives.hackazon.network.RequestListener<CustomerAddress.CustomerAddressesResponse> {

        protected CustomerAddressesRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException spiceException) {
            setProgressBarIndeterminateVisibility(false);
            Toast.makeText(CheckoutActivity.this, "failure", Toast.LENGTH_SHORT).show();
        }

        @Override
        public void onSuccess(CustomerAddress.CustomerAddressesResponse response) {
            setProgressBarIndeterminateVisibility(false);
            CheckoutActivity.this.customerAddresses = response.data;
            startSteps();
        }
    }
}