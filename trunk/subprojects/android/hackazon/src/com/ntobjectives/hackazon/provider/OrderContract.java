package com.ntobjectives.hackazon.provider;

import android.net.Uri;
import android.provider.BaseColumns;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 20.10.2014
 * Time: 13:14
 */
public class OrderContract {
    public static final String  DB_NAME = "hackazon.db";
    public static final int DB_VERSION = 1;
    public static final String TABLE = "orders";

    public static final String AUTHORITY = "com.ntobjectives.hackazon.OrderProvider";
    public static final Uri CONTENT_URI = Uri.parse("content://" + AUTHORITY + "/" + TABLE);

    public static final int USER_ITEM = 1;
    public static final int USER_DIR = 2;
    public static final String USER_TYPE_ITEM = "vnd.android.cursor.item/vnd.com.ntobjectives.hackazon.provider.order";
    public static final String USER_TYPE_DIR  = "vnd.android.cursor.dir/vnd.com.ntobjectives.hackazon.provider.order";

    public static final String DEFAULT_SORT = Column.CREATED_AT + " DESC";

    public class Column {
        public static final String ID = BaseColumns._ID;
        public static final String CREATED_AT = "created_at";
        public static final String UPDATED_AT = "updated_at";
        public static final String CUSTOMER_FIRSTNAME = "customer_firstname";
        public static final String CUSTOMER_LASTNAME = "customer_lastname";
        public static final String CUSTOMER_EMAIL = "customer_email";
        public static final String STATUS = "status";
        public static final String COMMENT = "comment";
        public static final String CUSTOMER_ID = "customer_id";
        public static final String PAYMENT_METHOD = "payment_method";
        public static final String SHIPPING_METHOD = "shipping_method";
        public static final String COUPON_ID = "coupon_id";
        public static final String DISCOUNT = "discount";
    }
}
