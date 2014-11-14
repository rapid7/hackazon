package com.ntobjectives.hackazon.activity;

import android.app.Fragment;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.util.Log;
import android.view.InflateException;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import com.ntobjectives.hackazon.model.Cart;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 30.10.2014
 * Time: 22:26
 */
abstract public class CheckoutBaseFragment extends Fragment {
    protected static String TAG = CheckoutBaseFragment.class.getSimpleName();

    protected View loadingView;
    protected View view;

    protected Cart cart;
    protected CheckoutActivity activity;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        Log.d(TAG, "onCreate");
        super.onCreate(savedInstanceState);
    }

    public View doOnCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState, int fragmentId) {
        Log.d(TAG, "onCreateView");
        if (view != null) {
            ViewGroup parent = (ViewGroup) view.getParent();
            if (parent != null) {
                parent.removeView(view);
            }
        }
        try {
            view = inflater.inflate(fragmentId, container, false);
        } catch (InflateException ignored) {
        }
        return view;
    }

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        return doOnCreateView(inflater, container, savedInstanceState, 0);
    }

    @Override
    public void onStart() {
        super.onStart();
        Log.d(TAG, "onStart");
        activity = (CheckoutActivity) getActivity();
        cart = activity.getCart();
    }
}
