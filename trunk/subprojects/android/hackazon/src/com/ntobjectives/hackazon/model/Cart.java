package com.ntobjectives.hackazon.model;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:29
 */
public class Cart {
    public int id;
    public String created_at;
    public String updated_at;
    public Integer items_count;
    public Integer items_qty;
    public Double total_price;
    public String uid;
    public Integer customer_id;
    public String customer_email;
    public Integer customer_is_guest;
    public String payment_method = null;
    public String shipping_method = null;
    public String shipping_address_id = null;
    public String billing_address_id = null;
    public Integer last_step;

    public ArrayList<CartItem> items = new ArrayList<CartItem>();

    @SuppressWarnings("serial")
    public static class List extends ArrayList<Cart> {
    }

    public static class OrdersResponse extends CollectionResponse<List> {
    }

    public static class ShippingMethods {
        public static final String MAIL = "mail";
        public static final String COLLECT = "collect";
        public static final String EXPRESS = "express";

        private static final HashMap<String, String> labels = new HashMap<String, String>();
        static {
            labels.put(MAIL, "Mail");
            labels.put(COLLECT, "Collect");
            labels.put(EXPRESS, "Express");
        }

        public static String getLabel(String method) {
            if (!labels.containsKey(method)) {
                //throw new IllegalArgumentException("Given payment method '" + method + "' doesn't exist");
                return "";
            }
            return labels.get(method);
        }

        public static HashMap<String, String> getLabels() {
            return labels;
        }
    }

    public static class PaymentMethods {
        public static final String WIRE_TRANSFER = "wire transfer";
        public static final String PAYPAL = "paypal";
        public static final String CREDIT_CARD = "creditcard";

        private static final HashMap<String, String> labels = new HashMap<String, String>();
        static {
            labels.put(WIRE_TRANSFER, "Wire Transfer");
            labels.put(PAYPAL, "Paypal");
            labels.put(CREDIT_CARD, "Credit Card");
        }

        public static String getLabel(String method) {
            if (!labels.containsKey(method)) {
                throw new IllegalArgumentException("Given payment method '" + method + "' doesn't exist");
            }
            return labels.get(method);
        }

        public static HashMap<String, String> getLabels() {
            return labels;
        }
    }
}
