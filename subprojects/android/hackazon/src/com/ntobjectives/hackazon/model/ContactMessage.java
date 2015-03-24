package com.ntobjectives.hackazon.model;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 24.03.2015
 * Time: 12:15
 */
public class ContactMessage {
    public int id;
    public String created_at;
    public String name = null;
    public String email = null;
    public String phone = null;
    public String message = null;
    public Integer customer_id = null;

    public ContactMessage() {
        SimpleDateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        created_at = df.format(new Date());
    }

    @SuppressWarnings("serial")
    public static class List extends ArrayList<ContactMessage> {
    }

    public static class ContactMessageResponse extends CollectionResponse<List> {
    }
}
