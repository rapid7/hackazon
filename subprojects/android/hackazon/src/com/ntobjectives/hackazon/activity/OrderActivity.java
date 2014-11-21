package com.ntobjectives.hackazon.activity;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.adapter.OrderItemsListAdapter;
import com.ntobjectives.hackazon.model.Order;
import com.ntobjectives.hackazon.network.OrderRetrofitSpiceRequest;
import com.octo.android.robospice.persistence.DurationInMillis;
import com.octo.android.robospice.persistence.exception.SpiceException;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 27.10.2014
 * Time: 13:06
 */
public class OrderActivity extends AbstractRootActivity {
    protected View loadingView;
    protected View view;

    protected int id;

    OrderRetrofitSpiceRequest req;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (getIntent().getBooleanExtra("EXIT", false)) {
            finish();
        }

        setContentView(R.layout.activity_order);


        id = getIntent().getExtras().getInt("id", 0);
        req = new OrderRetrofitSpiceRequest(id);

        view = findViewById(R.id.orderView);
        loadingView = findViewById(R.id.loading_layout);
        loadingView.setVisibility(View.VISIBLE);

        String lastRequestCacheKey = req.createCacheKey();
        getSpiceManager().execute(req, lastRequestCacheKey, DurationInMillis.ONE_MINUTE,
                new OrderRequestListener(getApplicationContext()));
    }

    private void updateViewContent(Order order) {
        TextView idField = (TextView) view.findViewById(R.id.orderId);
        TextView statusField = (TextView) view.findViewById(R.id.status);
        TextView createdField = (TextView) view.findViewById(R.id.createdAt);
        TextView itemNumField = (TextView) view.findViewById(R.id.itemNum);
        TextView totalField = (TextView) view.findViewById(R.id.total);
        ListView orderItemsListView = (ListView) view.findViewById(R.id.listviewOrderItems);

        idField.setText(Integer.toString(order.id));
        statusField.setText(order.status);
        createdField.setText(order.created_at);
        itemNumField.setText(Integer.toString(order.orderItems.size()));
        totalField.setText("$" + Double.toString(order.total_price));

        OrderItemsListAdapter orderItemsListAdapter = new OrderItemsListAdapter(this, getSpiceManagerBinary(), order.orderItems);
        orderItemsListView.setAdapter(orderItemsListAdapter);

        setTitle("Order №" + order.id);
        loadingView.setVisibility(View.GONE);
        view.setVisibility(View.VISIBLE);
    }

    public class OrderRequestListener extends com.ntobjectives.hackazon.network.RequestListener<Order> {

        protected OrderRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException e) {
            Toast.makeText(OrderActivity.this, "failure", Toast.LENGTH_SHORT).show();
        }

        @Override
        public void onSuccess(Order order) {
            updateViewContent(order);
        }
    }
}