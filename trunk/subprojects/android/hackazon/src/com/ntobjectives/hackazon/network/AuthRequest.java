package com.ntobjectives.hackazon.network;

import android.content.Context;
import android.preference.PreferenceManager;
import android.util.Log;
import com.octo.android.robospice.request.okhttp.OkHttpSpiceRequest;
import com.squareup.okhttp.OkHttpClient;
import org.apache.commons.io.IOUtils;

import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import static android.util.Base64.DEFAULT;
import static android.util.Base64.encodeToString;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 22.10.2014
 * Time: 15:00
 */
public class AuthRequest extends OkHttpSpiceRequest<String>{
    public static final String TAG = AuthRequest.class.getSimpleName();
    protected String host;
    protected String username;
    protected String password;

    protected boolean refresh = false;

    public AuthRequest(Context context, String host, String username, String password) {
        super(String.class);
        this.host = host.trim().equals("") ? PreferenceManager.getDefaultSharedPreferences(context).getString("host", "") : host;
        this.username = username;
        this.password = password;
    }

    public AuthRequest(Context context) {
        super(String.class);
        this.host = PreferenceManager.getDefaultSharedPreferences(context).getString("host", "");
        this.username = PreferenceManager.getDefaultSharedPreferences(context).getString("username", "");
        this.password = PreferenceManager.getDefaultSharedPreferences(context).getString("password", "");
    }

    @Override
    public String loadDataFromNetwork() throws Exception {
        Log.d(TAG, host + "/api/auth");
        // With Uri.Builder class we can build our url is a safe manner
        OkHttpClient client = new OkHttpClient();
        URL url = new URL(host + "/api/auth" + (refresh ? "?refresh=1" : ""));
        String credentials = username + ":" + password;

        HttpURLConnection conn = client.open(url);
        conn.setRequestProperty("Authorization", "Basic " + encodeToString(credentials.getBytes(), DEFAULT));

        Log.d(TAG, conn.getResponseCode() + ", " + conn.getResponseMessage());
        InputStream in = null;
        Log.d(TAG, "Input: " + conn.getRequestProperty("Authorization"));
        Log.d(TAG, "Output: " + conn.getResponseCode() + " " + conn.getResponseMessage());
        try {
            // Read the response.
            in = conn.getInputStream();
            return IOUtils.toString(in, "UTF-8");

        } finally {
            if (in != null) {
                in.close();
            }
        }
    }

    public void setRefresh(boolean refresh) {
        this.refresh = refresh;
    }
}
