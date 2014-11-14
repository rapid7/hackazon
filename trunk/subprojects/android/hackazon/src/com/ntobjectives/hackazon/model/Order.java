package com.ntobjectives.hackazon.model;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:29
 */
public class Order {
    public int id;
    public String created_at;
    public String updated_at;
    public String customer_firstname;
    public String customer_lastname = null;
    public String customer_email = null;
    public String status = "new";
    public String comment = null;
    public String customer_id = null;
    public String payment_method = null;
    public String shipping_method = null;
    public String coupon_id = null;
    public String discount = "0";
    public String increment_id;
    public Double total_price = 0.0;

    public ArrayList<OrderAddress> orderAddress = new ArrayList<OrderAddress>();
    public ArrayList<OrderItem> orderItems = new ArrayList<OrderItem>();

    public Order() {
        SimpleDateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        created_at = df.format(new Date());
        updated_at = created_at.substring(0);
    }

    @SuppressWarnings("serial")
    public static class List extends ArrayList<Order> {
    }

    public static class OrdersResponse extends CollectionResponse<List> {
    }
}
