package com.ntobjectives.hackazon.db;

import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;
import com.ntobjectives.hackazon.provider.UserContract;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 20.10.2014
 * Time: 13:12
 */
public class DbHelper extends SQLiteOpenHelper {
    public static final String TAG = DbHelper.class.getSimpleName();

    public DbHelper(Context context) {
        super(context, UserContract.DB_NAME, null, UserContract.DB_VERSION);
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        String sql = String
                .format("create table %s (%s int primary key, %s text, %s text, %s text, " +
                                "%s text, %s text, %s text, %s text, %s text, %s text, " +
                                "%s int, %s text, %s text, %s text)",
                    UserContract.TABLE,
                    UserContract.Column.ID,
                    UserContract.Column.USERNAME,
                    UserContract.Column.PASSWORD,
                    UserContract.Column.FIRST_NAME,
                    UserContract.Column.LAST_NAME,
                    UserContract.Column.USER_PHONE,
                    UserContract.Column.EMAIL,
                    UserContract.Column.OAUTH_PROVIDER,
                    UserContract.Column.CREATED_ON,
                    UserContract.Column.LAST_LOGIN,
                    UserContract.Column.ACTIVE,
                    UserContract.Column.RECOVER_PASSWD,
                    UserContract.Column.REST_TOKEN,
                    UserContract.Column.PHOTO
                );

        Log.d(TAG, "onCreate with SQL: " + sql);
        db.execSQL(sql);
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        db.execSQL("drop table if exists " + UserContract.TABLE);
        onCreate(db);
    }
}
