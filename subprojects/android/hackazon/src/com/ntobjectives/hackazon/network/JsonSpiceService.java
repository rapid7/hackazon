package com.ntobjectives.hackazon.network;

import android.content.Context;
import android.preference.PreferenceManager;
import android.util.Log;
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

    protected String host = "http://hackazon.webscantest.com";

    @Override
    public void onCreate() {
        super.onCreate();
        addRetrofitInterface(Hackazon.class);
    }

    @Override
    protected String getServerUrl() {
        return PreferenceManager.getDefaultSharedPreferences(this.getApplicationContext())
                .getString("host", "http://hackazon.webscantest.com");
    }

    @Override
    protected RestAdapter.Builder createRestAdapterBuilder() {
        Log.d(TAG, "createRestAdapterBuilder");

        RestAdapter.Builder builder =  super.createRestAdapterBuilder();
        builder.setRequestInterceptor(new RequestInterceptor(this.getApplicationContext(), this));
        // Uncomment to enable Http request and response logging
        builder.setLogLevel(RestAdapter.LogLevel.FULL);
        builder.setEndpoint(new Endpoint(this.getApplicationContext()));

//        OkHttpClient client = new OkHttpClient();
//        client.setConnectTimeout(3, TimeUnit.MINUTES);
//        client.setReadTimeout(10, TimeUnit.MINUTES);
//        OkClient clientProvider = new OkClient(client);
//        builder.setClient(clientProvider);

//        UrlConnectionClient client = new UrlConnectionClient();
//        builder.setClient(client);

//        AndroidClient client = new AndroidClient();
//        builder.setClient(client);


        // Enforce Gson to encode nulls
        GsonBuilder gsonBuilder = new GsonBuilder();
        gsonBuilder.serializeNulls();
        builder.setConverter(new GsonConverter(gsonBuilder.create()));

        return builder;
    }

    public String getHost() {
        return host;
    }

    public void setHost(String host) {
        this.host = host;
    }

    public class Endpoint implements retrofit.Endpoint {

        protected Context context;

        public Endpoint(Context context) {
            this.context = context;
        }

        @Override
        public String getUrl() {
            return PreferenceManager.getDefaultSharedPreferences(context.getApplicationContext()).getString("host", getServerUrl());
        }

        @Override
        public String getName() {
            return null;
        }
    }
}
