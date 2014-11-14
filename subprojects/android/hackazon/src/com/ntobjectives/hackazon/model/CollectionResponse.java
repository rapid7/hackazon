package com.ntobjectives.hackazon.model;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 28.10.2014
 * Time: 11:37
 */
public class CollectionResponse<List> {
    public List data;
    public int page;
    public String page_url;
    public int first_page;
    public String first_page_url;
    public int last_page;
    public String last_page_url;
    public int next_page;
    public String next_page_url;
    public int total_items;
    public int pages;
    public int per_page;
}
