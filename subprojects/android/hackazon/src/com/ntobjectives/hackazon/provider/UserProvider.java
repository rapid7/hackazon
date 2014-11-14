package com.ntobjectives.hackazon.provider;

import android.content.ContentProvider;
import android.content.ContentValues;
import android.content.UriMatcher;
import android.database.Cursor;
import android.net.Uri;
import android.util.Log;
import com.ntobjectives.hackazon.db.DbHelper;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 20.10.2014
 * Time: 13:01
 */
public class UserProvider extends ContentProvider {
    private static final String TAG = UserProvider.class.getSimpleName();
    private DbHelper dbHelper;

    private static final UriMatcher sUriMatcher = new UriMatcher(UriMatcher.NO_MATCH);
    static {
        sUriMatcher.addURI(UserContract.AUTHORITY, UserContract.TABLE, UserContract.USER_DIR);
        sUriMatcher.addURI(UserContract.AUTHORITY, UserContract.TABLE + "/#", UserContract.USER_ITEM);
    }

    @Override
    public boolean onCreate() {
        dbHelper = new DbHelper(getContext());
        Log.d(TAG, "onCreated1");
        return true;
    }

    @Override
    public Cursor query(Uri uri, String[] projection, String selection, String[] selectionArgs, String sortOrder) {
        Log.d(TAG, "query: " + uri);
        return null;
    }

    @Override
    public String getType(Uri uri) {
        switch (sUriMatcher.match(uri)) {
            case UserContract.USER_DIR:
                Log.d(TAG, "gotType: " + UserContract.USER_TYPE_DIR);
                return UserContract.USER_TYPE_DIR;

            case UserContract.USER_ITEM:
                Log.d(TAG, "gotType: " + UserContract.USER_TYPE_ITEM);
                return UserContract.USER_TYPE_ITEM;

            default:
                throw new IllegalArgumentException("Illegal uri: " + uri);
        }
    }

    @Override
    public Uri insert(Uri uri, ContentValues values) {
        Log.d(TAG, "insert");
        return null;
    }

    @Override
    public int delete(Uri uri, String selection, String[] selectionArgs) {
        Log.d(TAG, "delete");
        return 0;
    }

    @Override
    public int update(Uri uri, ContentValues values, String selection, String[] selectionArgs) {
        Log.d(TAG, "update");
        return 0;
    }
}
