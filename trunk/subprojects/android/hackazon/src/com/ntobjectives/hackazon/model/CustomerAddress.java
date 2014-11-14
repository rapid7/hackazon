package com.ntobjectives.hackazon.model;

import java.util.ArrayList;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 31.10.2014
 * Time: 12:30
 */
public class CustomerAddress implements Cloneable {
    public Integer id;
    public String full_name;
    public String address_line_1;
    public String address_line_2;
    public String city;
    public String region;
    public String zip;
    public String country_id;
    public String phone;
    public Integer customer_id;

    @SuppressWarnings("serial")
    public static class List extends ArrayList<CustomerAddress> {
    }

    public static class CustomerAddressesResponse extends CollectionResponse<List> {
    }

    @Override
    public CustomerAddress clone() throws CloneNotSupportedException {
        CustomerAddress address = (CustomerAddress) super.clone();
        address.full_name = full_name;
        address.address_line_1 = address_line_1;
        address.address_line_2 = address_line_2;
        address.city = city;
        address.region = region;
        address.country_id = country_id;
        address.zip = zip;
        address.phone = phone;
        return address;
    }
}
