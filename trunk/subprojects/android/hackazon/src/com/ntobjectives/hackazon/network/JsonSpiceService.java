package com.ntobjectives.hackazon.network;

import android.preference.PreferenceManager;
import com.google.gson.GsonBuilder;
import com.octo.android.robospice.retrofit.RetrofitGsonSpiceService;
import retrofit.RestAdapter;
import retrofit.converter.GsonConverter;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 17:31
 */
public class JsonSpiceService extends RetrofitGsonSpiceService {
   // public static final String SERVER_URL = "http://192.168.1.206:8888";
    public static final String TAG = JsonSpiceService.class.getSimpleName();

    @Override
    public void onCreate() {
        super.onCreate();
        addRetrofitInterface(Hackazon.class);
    }

    @Override
    protected String getServerUrl() {
        return PreferenceManager.getDefaultSharedPreferences(this).getString("host", "http://hackazon.webscantest.com");
    }

    @Override
    protected RestAdapter.Builder createRestAdapterBuilder() {
        RestAdapter.Builder builder =  super.createRestAdapterBuilder();
        builder.setRequestInterceptor(new RequestInterceptor(this.getApplicationContext()));
        // Uncomment to enable Http request and response logging
        builder.setLogLevel(RestAdapter.LogLevel.FULL);

        // Enforce Gson to encode nulls
        GsonBuilder gsonBuilder = new GsonBuilder();
        gsonBuilder.serializeNulls();
        builder.setConverter(new GsonConverter(gsonBuilder.create()));

        return builder;
    }
}
