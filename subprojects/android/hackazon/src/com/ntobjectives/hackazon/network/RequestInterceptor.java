package com.ntobjectives.hackazon.network;

import android.content.Context;
import android.content.SharedPreferences;
import android.preference.PreferenceManager;
import android.util.Log;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 22.10.2014
 * Time: 13:23
 */
public class RequestInterceptor implements retrofit.RequestInterceptor {
    private static final String TAG = RequestInterceptor.class.getSimpleName();

    protected Context context;
    protected JsonSpiceService service;

    public RequestInterceptor(Context context, JsonSpiceService service) {
        this.context = context;
        this.service = service;
    }

    @Override
    public void intercept(RequestFacade requestFacade) {
        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(context);
        String token = prefs.getString("token", "");
        Log.d(TAG, "Token used: " + prefs.getString("token", ""));
        requestFacade.addHeader("Authorization", "Token " + token);
    }
}
