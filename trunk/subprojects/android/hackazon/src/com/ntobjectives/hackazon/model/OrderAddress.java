package com.ntobjectives.hackazon.model;

import java.util.ArrayList;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 28.10.2014
 * Time: 11:39
 */
@SuppressWarnings("serial")
public class OrderAddress {
    public int id;
    public String full_name;
    public String address_line_1;
    public String address_line_2;
    public String city;
    public String region;
    public String zip;
    public String country_id;
    public String phone;
    public Integer customer_id;
    public String address_type;
    public Integer order_id;

    public OrderAddress() {
    }

    @SuppressWarnings("serial")
    public static class List extends ArrayList<OrderAddress> {
    }

    public static class OrderAddressesResponse extends CollectionResponse<List> {
    }

    public void fillFromCustomerAddress(CustomerAddress address) {
        address_line_1 = address.address_line_1;
        address_line_2 = address.address_line_2;
        city = address.city;
        country_id = address.country_id;
        zip = address.zip;
        phone = address.phone;
        region = address.region;
        full_name = address.full_name;
        customer_id = address.customer_id;
    }
}
