package com.ntobjectives.hackazon.model;

import java.util.ArrayList;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 31.10.2014
 * Time: 11:11
 */
public class User {
    public Integer id;
    public String username;
    public String password;
    public String first_name;
    public String last_name;
    public String user_phone;
    public String email;
    public String oauth_provider;
    public String oauth_uid;
    public String created_on;
    public String last_login;
    public Integer active;
    public String recover_passw;
    public String rest_token;
    public String photo;
    public String credit_card;
    public String credit_card_expires;
    public String credit_card_cvv;

    @SuppressWarnings("serial")
    public static class List extends ArrayList<User> {
    }

    public static class UsersResponse extends CollectionResponse<List> {
    }
}
