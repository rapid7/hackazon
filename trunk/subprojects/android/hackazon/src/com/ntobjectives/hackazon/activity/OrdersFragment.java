package com.ntobjectives.hackazon.activity;

import android.app.Fragment;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.util.Log;
import android.view.InflateException;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.Toast;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.adapter.OrdersListAdapter;
import com.ntobjectives.hackazon.model.Order;
import com.ntobjectives.hackazon.network.OrdersRetrofitSpiceRequest;
import com.ntobjectives.hackazon.view.OrderListItemView;
import com.octo.android.robospice.persistence.DurationInMillis;
import com.octo.android.robospice.persistence.exception.SpiceException;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 17.10.2014
 * Time: 14:50
 */
public class OrdersFragment extends Fragment {
    public static final String TAG = OrdersFragment.class.getSimpleName();

    private ListView ordersListView;
    private View loadingView;

    //private SimpleCursorAdapter adapter;
    private View view;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        Log.d(TAG, "onCreate");
        //getActivity().requestWindowFeature(Window.FEATURE_INDETERMINATE_PROGRESS);
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
            view = inflater.inflate(R.layout.orders_fragment, container, false);
        } catch (InflateException ignored) {
        }
        return view;
    }

    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        Log.d(TAG, "onActivityCreated");
        super.onActivityCreated(savedInstanceState);

        ordersListView = (ListView) getActivity().findViewById(R.id.listview_orders);
        loadingView = getActivity().findViewById(R.id.loading_layout);
        loadingView.setVisibility(View.VISIBLE);

        getActivity().setProgressBarIndeterminateVisibility(false);
    }

    @Override
    public void onStart() {
        Log.d(TAG, "onStart");
        super.onStart();

        OrdersRetrofitSpiceRequest req = new OrdersRetrofitSpiceRequest();

        String lastRequestCacheKey = req.createCacheKey();
        getActivity().setProgressBarIndeterminateVisibility(true);
        ((AbstractRootActivity) getActivity()).getSpiceManager().execute(req, lastRequestCacheKey, DurationInMillis.ONE_MINUTE,
                new OrdersRequestListener(getActivity().getApplicationContext()));

        ordersListView.setOnItemClickListener(new OnItemClickListener());
    }

    private void updateListViewContent(Order.OrdersResponse response) {
        AbstractRootActivity act = (AbstractRootActivity) getActivity();
        OrdersListAdapter orderListAdapter = new OrdersListAdapter(getActivity(), act.getSpiceManagerBinary(), response.data);
        ordersListView.setAdapter(orderListAdapter);

        loadingView.setVisibility(View.GONE);
        ordersListView.setVisibility(View.VISIBLE);
    }

    public final class OrdersRequestListener extends com.ntobjectives.hackazon.network.RequestListener<Order.OrdersResponse> {

        protected OrdersRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException spiceException) {
            if (getActivity() != null) {
                getActivity().setProgressBarIndeterminateVisibility(false);
                Toast.makeText(OrdersFragment.this.getActivity(), "failure", Toast.LENGTH_SHORT).show();
            }
        }

        @Override
        public void onSuccess(Order.OrdersResponse response) {
            if (getActivity() != null) {
                getActivity().setProgressBarIndeterminateVisibility(false);
                updateListViewContent(response);
                //Toast.makeText(OrdersFragment.this.getActivity(), "success", Toast.LENGTH_SHORT).show();
            }
        }
    }

    public final class OnItemClickListener implements AdapterView.OnItemClickListener {
        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
            OrderListItemView v = (OrderListItemView) view;
            Log.d(TAG, Integer.toString(v.getData().id));
            Intent intent = new Intent(getActivity(), OrderActivity.class);
            intent.putExtra("id", v.getData().id);
            startActivity(intent);
        }
    }
}