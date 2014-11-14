package com.ntobjectives.hackazon.model;

import java.util.ArrayList;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:29
 */
public class Product {
    public int productID;
    public int categoryID;
    public String name;
    public String description = "";
    public String customers_rating = "0";
    public Double Price;
    public String picture;
    public Integer in_stock;
    public String thumbnail;
    public String customer_votes = "0";
    public String items_sold = "0";
    public String big_picture;
    public int enabled = 0;
    public String brief_description;
    public String list_price;
    public String product_code;
    public String hurl;
    public String accompanyID;
    public String brandID;
    public String meta_title;
    public String meta_keywords;
    public String meta_desc;
    public String canonical;
    public String h1;
    public String yml = "1";
    public String min_qunatity = "1";
    public String managerID;

    @SuppressWarnings("serial")
    public static class List extends ArrayList<Product> {
    }

    public static class ProductsResponse extends CollectionResponse<List> {
    }
}
