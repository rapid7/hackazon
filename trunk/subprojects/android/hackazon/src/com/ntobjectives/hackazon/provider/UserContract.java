package com.ntobjectives.hackazon.provider;

import android.net.Uri;
import android.provider.BaseColumns;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 20.10.2014
 * Time: 13:14
 */
public class UserContract {
    public static final String  DB_NAME = "hackazon.db";
    public static final int DB_VERSION = 1;
    public static final String TABLE = "users";

    public static final String AUTHORITY = "com.ntobjectives.hackazon.UserProvider";
    public static final Uri CONTENT_URI = Uri.parse("content://" + AUTHORITY + "/" + TABLE);

    public static final int USER_ITEM = 1;
    public static final int USER_DIR = 2;
    public static final String USER_TYPE_ITEM = "vnd.android.cursor.item/vnd.com.ntobjectives.hackazon.provider.user";
    public static final String USER_TYPE_DIR  = "vnd.android.cursor.dir/vnd.com.ntobjectives.hackazon.provider.user";

    public static final String DEFAULT_SORT = Column.USERNAME + " DESC";

    public class Column {
        public static final String ID = BaseColumns._ID;
        public static final String USERNAME = "username";
        public static final String PASSWORD = "password";
        public static final String FIRST_NAME = "first_name";
        public static final String LAST_NAME = "last_name";
        public static final String USER_PHONE = "user_phone";
        public static final String EMAIL = "email";
        public static final String OAUTH_PROVIDER = "oauth_provider";
        public static final String CREATED_ON = "created_on";
        public static final String LAST_LOGIN = "last_login";
        public static final String ACTIVE = "active";
        public static final String RECOVER_PASSWD = "recover_passwd";
        public static final String REST_TOKEN = "rest_token";
        public static final String PHOTO = "photo";
    }
}
