package com.ntobjectives.hackazon.model;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 28.10.2014
 * Time: 11:40
 */
@SuppressWarnings("serial")
public class OrderItem {
    public int id;
    public Integer cart_id;
    public String created_at;
    public String updated_at;
    public Integer product_id;
    public String name;
    public int qty = 0;
    public double price = 0.0;
    public Integer order_id;

    public OrderItem() {
        SimpleDateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        created_at = df.format(new Date());
        updated_at = created_at.substring(0);
    }

    public void fillFromCartItem(CartItem item) {
        cart_id = item.cart_id;
        name = item.name;
        price = item.price;
        product_id = item.product_id;
        qty = item.qty;
    }

    @SuppressWarnings("serial")
    public static class List extends ArrayList<OrderItem> {
    }

    public static class OrderItemsResponse extends CollectionResponse<List> {
    }
}
