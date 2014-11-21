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

    public boolean equals(Object o) {
        if (o == this) {
            return true;
        }

        if (!(o instanceof CustomerAddress)) {
            return false;
        }

        CustomerAddress that = (CustomerAddress) o;

        return (id == null && that.id == null || id != null && id.equals(that.id)) && isSimilar(that);
    }

    public boolean isSimilar(CustomerAddress that) {
        return  that == this
                || ((full_name == null && that.full_name == null || full_name != null
                        && full_name.equals(that.full_name))
                && (address_line_1 == null && that.address_line_1 == null || address_line_1 != null
                        && address_line_1.equals(that.address_line_1))
                && (address_line_2 == null && that.address_line_2 == null || address_line_2 != null
                        && address_line_2.equals(that.address_line_2))
                && (city == null && that.city == null || city != null && city.equals(that.city))
                && (region == null && that.region == null || region != null && region.equals(that.region))
                && (zip == null && that.zip == null || zip != null && zip.equals(that.zip))
                && (country_id == null && that.country_id == null || country_id != null
                        && country_id.equals(that.country_id))
                && (phone == null && that.phone == null || phone != null && phone.equals(that.phone))
                && (customer_id == null && that.customer_id == null || customer_id != null
                        && customer_id.equals(that.customer_id)));
    }

    @SuppressWarnings("serial")
    public static class List extends ArrayList<CustomerAddress> {
        public CustomerAddress findSimilar(CustomerAddress address) {
            for (CustomerAddress a:this) {
                if (a.id == null || a.id.equals(0)) {
                    continue;
                }
                if (a.isSimilar(address)) {
                    return a;
                }
            }

            return null;
        }
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
