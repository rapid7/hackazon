package com.ntobjectives.hackazon.activity;

import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import com.ntobjectives.hackazon.R;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 17.10.2014
 * Time: 14:50
 */
public class CheckoutSuccessFragment extends CheckoutBaseFragment {
    public static final String TAG = CheckoutSuccessFragment.class.getSimpleName();

    protected Button ordersButton;
    protected Button productsButton;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        return doOnCreateView(inflater, container, savedInstanceState, R.layout.checkout_success_fragment);
    }

    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        Log.d(TAG, "onActivityCreated");
        super.onActivityCreated(savedInstanceState);

        productsButton = (Button) getActivity().findViewById(R.id.productsButton);
        ordersButton = (Button) getActivity().findViewById(R.id.ordersButton);
        loadingView = getActivity().findViewById(R.id.loading_layout);
        loadingView.setVisibility(View.GONE);

        ordersButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(CheckoutSuccessFragment.this.getActivity(), MainActivity.class);
                intent.putExtra("page", "orders");
                startActivity(intent);
            }
        });

        productsButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(CheckoutSuccessFragment.this.getActivity(), MainActivity.class);
                intent.putExtra("page", "products");
                startActivity(intent);
            }
        });

        getActivity().setProgressBarIndeterminateVisibility(false);
    }

    @Override
    public void onStart() {
        Log.d(TAG, "onStart");
        super.onStart();
        getActivity().setTitle("Overview");
    }
}