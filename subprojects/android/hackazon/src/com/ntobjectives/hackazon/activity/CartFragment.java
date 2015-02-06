package com.ntobjectives.hackazon.activity;

import android.app.Fragment;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.annotation.Nullable;
import android.util.Log;
import android.view.InflateException;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.*;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.adapter.CartItemsListAdapter;
import com.ntobjectives.hackazon.model.Cart;
import com.ntobjectives.hackazon.network.CartRetrofitSpiceRequest;
import com.octo.android.robospice.persistence.exception.SpiceException;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 17.10.2014
 * Time: 14:50
 */
public class CartFragment extends Fragment {
    public static final String TAG = CartFragment.class.getSimpleName();

    private ListView cartItemsListView;
    private View loadingView;
    private View view;
    private Button emptyCartButton;
    private Button checkoutButton;

    protected CartRetrofitSpiceRequest req;
    private Cart cart;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        Log.d(TAG, "onCreate");
        super.onCreate(savedInstanceState);
    }

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        Log.d(TAG, "onCreateView");
        if (view != null) {
            ViewGroup parent = (ViewGroup) view.getParent();
            if (parent != null) {
                parent.removeView(view);
            }
        }
        try {
            view = inflater.inflate(R.layout.cart_fragment, container, false);
        } catch (InflateException ignored) {
        }
        return view;
    }

    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        Log.d(TAG, "onActivityCreated");
        super.onActivityCreated(savedInstanceState);
        cartItemsListView = (ListView) getActivity().findViewById(R.id.listviewCartItems);
        emptyCartButton = (Button) getActivity().findViewById(R.id.emptyCartButton);
        checkoutButton = (Button) getActivity().findViewById(R.id.checkoutButton);
        loadingView = getActivity().findViewById(R.id.loading_layout);
        loadingView.setVisibility(View.VISIBLE);

        getActivity().setProgressBarIndeterminateVisibility(false);

        emptyCartButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(CartFragment.this.getActivity());
                prefs.edit().putString("cart_uid", "").apply();
                ((AbstractRootActivity) getActivity()).getSpiceManager().cancel(req);
                loadCart("");
            }
        });

        checkoutButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (cart.items.size() == 0) {
                    return;
                }
                startActivity(new Intent(getActivity(), CheckoutActivity.class));
            }
        });
    }

    @Override
    public void onStart() {
        Log.d(TAG, "onStart");
        super.onStart();
        loadCart();
    }

    protected void loadCart() {
        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(this.getActivity());
        loadCart(prefs.getString("cart_uid", ""));
    }

    protected void loadCart(String uid) {
        req = new CartRetrofitSpiceRequest(uid);
        getActivity().setProgressBarIndeterminateVisibility(true);
        ((AbstractRootActivity) getActivity()).getSpiceManager().execute(req,
                new CartRequestListener(this.getActivity().getApplicationContext()));
    }

    private void updateListViewContent(Cart cart) {
        if (cart == null) {
            return;
        }
        TextView itemNumField = (TextView) view.findViewById(R.id.itemNum);
        TextView totalField = (TextView) view.findViewById(R.id.total);

        itemNumField.setText(Integer.toString(cart.items.size()));
        totalField.setText("$" + Double.toString(cart.total_price));

        AbstractRootActivity act = (AbstractRootActivity) getActivity();
        CartItemsListAdapter listAdapter = new CartItemsListAdapter(getActivity(), act.getSpiceManagerBinary(), cart.items);
        cartItemsListView.setAdapter(listAdapter);

        loadingView.setVisibility(View.GONE);
        cartItemsListView.setVisibility(View.VISIBLE);
    }

    public final class CartRequestListener extends com.ntobjectives.hackazon.network.RequestListener<Cart> {

        protected CartRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException spiceException) {
            getActivity().setProgressBarIndeterminateVisibility(false);
            Toast.makeText(CartFragment.this.getActivity(), "failure", Toast.LENGTH_SHORT).show();
        }

        @Override
        public void onSuccess(Cart response) {
            if (getActivity() != null) {
                getActivity().setProgressBarIndeterminateVisibility(false);
                CartFragment.this.cart = response;
                updateListViewContent(response);
                //Toast.makeText(OrdersFragment.this.getActivity(), "success", Toast.LENGTH_SHORT).show();
            }
        }
    }
}