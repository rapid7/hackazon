package com.ntobjectives.hackazon.view;

import android.content.Context;
import android.view.LayoutInflater;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.model.Order;
import com.octo.android.robospice.spicelist.SpiceListItemView;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 25.10.2014
 * Time: 17:35
 */
public class OrderListItemView extends RelativeLayout implements SpiceListItemView<Order> {
    protected Order order;
    protected TextView orderIdField;
    protected TextView statusField;
    protected TextView createdAtField;

    public OrderListItemView(Context context) {
        super(context);
        inflateView(context);
    }

    private void inflateView(Context context) {
        LayoutInflater.from(context).inflate(R.layout.order_list_item, this);
        this.orderIdField = (TextView) this.findViewById(R.id.orderId);
        this.statusField = (TextView) this.findViewById(R.id.status);
        this.createdAtField = (TextView) this.findViewById(R.id.createdAt);
    }

    @Override
    public Order getData() {
        return order;
    }

    @Override
    public ImageView getImageView(int i) {
        return null;
    }

    @Override
    public int getImageViewCount() {
        return 0;
    }

    @Override
    public void update(Order order) {
        this.order = order;
        this.orderIdField.setText(Integer.toString(order.id));
        this.statusField.setText(order.status);
        this.createdAtField.setText(order.created_at);
    }
}
