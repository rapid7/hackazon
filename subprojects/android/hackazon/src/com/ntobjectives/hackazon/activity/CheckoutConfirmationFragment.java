package com.ntobjectives.hackazon.activity;

import android.content.Context;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.annotation.Nullable;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.widget.*;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.adapter.CartItemsListAdapter;
import com.ntobjectives.hackazon.model.*;
import com.ntobjectives.hackazon.network.*;
import com.ntobjectives.hackazon.view.CustomerAddressLayout;
import com.octo.android.robospice.persistence.exception.SpiceException;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 17.10.2014
 * Time: 14:50
 */
public class CheckoutConfirmationFragment extends CheckoutBaseFragment {
    public static final String TAG = CheckoutConfirmationFragment.class.getSimpleName();

    protected TextView itemNumberField;
    protected TextView totalField;
    protected TextView shippingMethodField;
    protected TextView paymentMethodField;
    protected CustomerAddressLayout shippingAddressView;
    protected CustomerAddressLayout billingAddressView;
    protected ListView cartItemsListView;

    protected Button prevButton;
    protected Button nextButton;

    protected CustomerAddress shippingAddress;
    protected CustomerAddress billingAddress;
    protected OrderAddress orderShippingAddress;
    protected OrderAddress orderBillingAddress;
    protected Order order;

    protected int remainingOrderItemsRequests = 0;
    protected int successfulOrderItemsRequests = 0;
    protected int totalOrderItemsRequests = 0;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        return doOnCreateView(inflater, container, savedInstanceState, R.layout.checkout_overview_fragment);
    }

    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        Log.d(TAG, "onActivityCreated");
        super.onActivityCreated(savedInstanceState);

        itemNumberField = (TextView) getActivity().findViewById(R.id.itemNum);
        totalField = (TextView) getActivity().findViewById(R.id.total);
        shippingAddressView = (CustomerAddressLayout) getActivity().findViewById(R.id.shippingAddress);
        billingAddressView = (CustomerAddressLayout) getActivity().findViewById(R.id.billingAddress);
        cartItemsListView = (ListView) getActivity().findViewById(R.id.listviewCartItems);
        shippingMethodField = (TextView) getActivity().findViewById(R.id.shippingMethod);
        paymentMethodField = (TextView) getActivity().findViewById(R.id.paymentMethod);

        nextButton = (Button) getActivity().findViewById(R.id.nextButton);
        prevButton = (Button) getActivity().findViewById(R.id.prevButton);
        loadingView = getActivity().findViewById(R.id.loading_layout);
        loadingView.setVisibility(View.GONE);

        prevButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                activity.selectStep(CheckoutActivity.Steps.BILLING_ADDRESS);
            }
        });

        cartItemsListView.setOnTouchListener(new View.OnTouchListener() {
            // Setting on Touch Listener for handling the touch inside ScrollView
            @Override
            public boolean onTouch(View v, MotionEvent event) {
                // Disallow the touch request for parent scroll on touch of child view
                v.getParent().requestDisallowInterceptTouchEvent(true);
                return false;
            }
        });
        setListViewHeightBasedOnChildren(cartItemsListView);

        nextButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                startPlacingOrderTask();
            }
        });

        getActivity().setProgressBarIndeterminateVisibility(false);
    }

    private void startPlacingOrderTask() {
        nextButton.setEnabled(false);
        prevButton.setEnabled(false);

        shippingAddress = null;
        billingAddress = null;

        CustomerAddress.List caList = ((CheckoutActivity)CheckoutConfirmationFragment.this.getActivity()).customerAddresses;

        User u = activity.getUser();
        CustomerAddress sa = activity.getShippingAddress();
        sa.customer_id = u.id;

        CustomerAddress ca;
        ca = caList.findSimilar(sa);
        if (ca == null) {
            activity.getSpiceManager().execute(new CustomerAddressAddRetrofitSpiceRequest(activity.getShippingAddress()),
                    new AddCustomerAddressResponse(activity, "shipping"));
        } else {
            sa.id = ca.id;
            shippingAddress = sa;
            saveCartTask();
        }

        CustomerAddress ba = activity.getBillingAddress();
        ba.customer_id = u.id;
        ca = caList.findSimilar(ba);

        if (ca == null) {
            activity.getSpiceManager().execute(new CustomerAddressAddRetrofitSpiceRequest(ba),
                    new AddCustomerAddressResponse(activity, "billing"));
        } else {
            ba.id = ca.id;
            billingAddress = ba;
            saveCartTask();
        }
    }

    private void saveCartTask() {
        if (shippingAddress == null || billingAddress == null) {
            return;
        }

        Cart c = activity.getCart();
        User u = activity.getUser();
        c.shipping_address_id = Integer.toString(shippingAddress.id);
        c.billing_address_id = Integer.toString(billingAddress.id);
        c.customer_email = u.email == null ? "" : u.email;
        c.customer_id = u.id;

        activity.getSpiceManager().execute(new CartUpdateRetrofitSpiceRequest(c),
                new UpdateCartResponseListener(activity));
    }

    protected void addOrderTask() {
        order = new Order();
        User u = activity.getUser();
        order.customer_firstname = u.username;
        order.customer_email = u.email;
        order.status = "complete";
        order.customer_id = Integer.toString(u.id);
        order.shipping_method = cart.shipping_method;
        order.payment_method = cart.payment_method;
        order.discount = "0";
        order.coupon_id = "";
        order.comment = "";
        order.customer_lastname = "";

        activity.getSpiceManager().execute(new OrderAddRetrofitSpiceRequest(order),
                new OrderAddResponseListener(activity));
    }

    protected void addOrderAddressesAndItemsTask() {
        User u = activity.getUser();
        OrderAddress shipAddr = new OrderAddress();
        OrderAddress billAddr = new OrderAddress();
        shipAddr.fillFromCustomerAddress(shippingAddress);
        shipAddr.address_type = "shipping";
        shipAddr.order_id = order.id;
        billAddr.fillFromCustomerAddress(billingAddress);
        billAddr.address_type = "billing";
        billAddr.order_id = order.id;


        shipAddr.customer_id = u.id;
        activity.getSpiceManager().execute(new OrderAddressAddRetrofitSpiceRequest(shipAddr),
                new AddOrderAddressResponse(activity, "shipping"));

        billAddr.customer_id = u.id;
        activity.getSpiceManager().execute(new OrderAddressAddRetrofitSpiceRequest(billAddr),
                new AddOrderAddressResponse(activity, "billing"));



        totalOrderItemsRequests = cart.items.size();
        successfulOrderItemsRequests = 0;
        remainingOrderItemsRequests = cart.items.size();

        for (CartItem cItem : cart.items) {
            OrderItem oItem = new OrderItem();
            oItem.fillFromCartItem(cItem);
            oItem.order_id = order.id;

            activity.getSpiceManager().execute(new OrderItemAddRetrofitSpiceRequest(oItem),
                    new AddOrderItemResponse(activity));
        }
    }

    protected void goToSuccessTask() {
        if (orderShippingAddress == null || orderBillingAddress == null) {
            return;
        }

        if (remainingOrderItemsRequests > 0) {
            return;
        }

        if (successfulOrderItemsRequests != totalOrderItemsRequests) {
            Toast.makeText(activity, "Failed to add all order items", Toast.LENGTH_SHORT).show();
            return;
        }

        activity.getSpiceManager().execute(new CartDeleteRetrofitSpiceRequest(cart.id),
                new DeleteCartResponse(activity, cart.id));

        // Clear the cart
        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(activity);
        prefs.edit().putString("cart_uid", "").apply();

        activity.selectStep(CheckoutActivity.Steps.SUCCESS);
    }

    @Override
    public void onStart() {
        Log.d(TAG, "onStart");
        super.onStart();
        getActivity().setTitle("Overview");

        itemNumberField.setText(Integer.toString(cart.items.size()));
        totalField.setText("$" + Double.toString(cart.total_price));
        shippingAddressView.setAddress(activity.getShippingAddress());
        shippingAddressView.setTitle("Shipping Address:");
        billingAddressView.setAddress(activity.getBillingAddress());
        billingAddressView.setTitle("Billing Address:");
        shippingMethodField.setText(Cart.ShippingMethods.getLabel(cart.shipping_method));
        paymentMethodField.setText(Cart.PaymentMethods.getLabel(cart.payment_method));

        CartItemsListAdapter listAdapter = new CartItemsListAdapter(getActivity(), activity.getSpiceManagerBinary(), cart.items);
        cartItemsListView.setAdapter(listAdapter);
    }

    public static void setListViewHeightBasedOnChildren(ListView listView) {
        ListAdapter listAdapter = listView.getAdapter();
        if (listAdapter == null)
            return;

        int desiredWidth = View.MeasureSpec.makeMeasureSpec(listView.getWidth(), View.MeasureSpec.UNSPECIFIED);
        int totalHeight = 0;
        View view = null;
        for (int i = 0; i < listAdapter.getCount(); i++) {
            view = listAdapter.getView(i, view, listView);
            if (i == 0)
                view.setLayoutParams(new ViewGroup.LayoutParams(desiredWidth, AbsListView.LayoutParams.WRAP_CONTENT));

            view.measure(desiredWidth, View.MeasureSpec.UNSPECIFIED);
            totalHeight += view.getMeasuredHeight();
        }
        ViewGroup.LayoutParams params = listView.getLayoutParams();
        params.height = totalHeight + (listView.getDividerHeight() * (listAdapter.getCount() - 1));
        listView.setLayoutParams(params);
        listView.requestLayout();
    }

    protected class AddCustomerAddressResponse extends RequestListener<CustomerAddress> {
        protected String type;

        public AddCustomerAddressResponse(Context context, String type) {
            super(context);
            this.type = type;
        }

        @Override
        public void onFailure(SpiceException e) {
            Toast.makeText(activity, "failure while adding customer address", Toast.LENGTH_SHORT).show();
            nextButton.setEnabled(true);
            prevButton.setEnabled(true);
        }

        @Override
        public void onSuccess(CustomerAddress address) {
            if (type.equals("shipping")) {
                CheckoutConfirmationFragment.this.shippingAddress = address;
            } else if (type.equals("billing")) {
                CheckoutConfirmationFragment.this.billingAddress = address;
            }

            saveCartTask();
        }
    }


    protected class AddOrderAddressResponse extends RequestListener<OrderAddress> {
        protected String type;

        public AddOrderAddressResponse(Context context, String type) {
            super(context);
            this.type = type;
        }

        @Override
        public void onFailure(SpiceException e) {
            Toast.makeText(activity, "failure while adding customer address", Toast.LENGTH_SHORT).show();
            nextButton.setEnabled(true);
            prevButton.setEnabled(true);
        }

        @Override
        public void onSuccess(OrderAddress address) {
            if (type.equals("shipping")) {
                CheckoutConfirmationFragment.this.orderShippingAddress = address;
            } else if (type.equals("billing")) {
                CheckoutConfirmationFragment.this.orderBillingAddress = address;
            }

            goToSuccessTask();
        }
    }

    protected class UpdateCartResponseListener extends RequestListener<Cart> {
        public UpdateCartResponseListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException e) {
            Toast.makeText(activity, "failure while updating cart address", Toast.LENGTH_SHORT).show();
            nextButton.setEnabled(true);
            prevButton.setEnabled(true);
        }

        @Override
        public void onSuccess(Cart cart) {
            Log.d(TAG, "Successfully updated cart and ready to place an order.");
            addOrderTask();
        }
    }

    protected class OrderAddResponseListener extends RequestListener<Order> {
        public OrderAddResponseListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException e) {
            Toast.makeText(activity, "failure while adding order", Toast.LENGTH_SHORT).show();
            nextButton.setEnabled(true);
            prevButton.setEnabled(true);
        }

        @Override
        public void onSuccess(Order order) {
            Log.d(TAG, "Successfully added order №" + order.id);
            CheckoutConfirmationFragment.this.order = order;
            addOrderAddressesAndItemsTask();
        }
    }


    protected class AddOrderItemResponse extends RequestListener<OrderItem> {
        public AddOrderItemResponse(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException e) {
            Toast.makeText(activity, "failure while adding order item", Toast.LENGTH_SHORT).show();
            nextButton.setEnabled(true);
            prevButton.setEnabled(true);
            remainingOrderItemsRequests--;
        }

        @Override
        public void onSuccess(OrderItem item) {
            Log.d(TAG, "Successfully added order item №" + item.id);
            remainingOrderItemsRequests--;
            successfulOrderItemsRequests++;
            goToSuccessTask();
        }
    }


    protected class DeleteCartResponse extends RequestListener<Void> {
        protected int id;

        public DeleteCartResponse(Context context, int id) {
            super(context);
            this.id = id;
        }

        @Override
        public void onFailure(SpiceException e) {
            Log.e(TAG, "Unable to remove the cart №" + id);
        }

        @Override
        public void onSuccess(Void result) {
            Log.d(TAG, "Successfully removed the cart №" + id);
        }
    }
}