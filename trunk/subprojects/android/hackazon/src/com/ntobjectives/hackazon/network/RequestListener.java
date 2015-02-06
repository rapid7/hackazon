package com.ntobjectives.hackazon.network;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.preference.PreferenceManager;
import android.util.Log;
import com.ntobjectives.hackazon.activity.LoginActivity;
import com.octo.android.robospice.persistence.exception.SpiceException;
import org.apache.commons.io.IOUtils;
import retrofit.RetrofitError;

import java.io.IOException;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 29.10.2014
 * Time: 11:30
 */
abstract public class RequestListener<RESULT> implements com.octo.android.robospice.request.listener.RequestListener<RESULT> {
    private static final String TAG = RequestListener.class.getSimpleName();

    protected Context context;

    public RequestListener(Context context) {
        this.context = context;
    }

    @Override
    public void onRequestFailure(SpiceException e) {
        RetrofitError ex = e.getCause() instanceof RetrofitError ? ((RetrofitError) e.getCause()) : null;

        if (ex != null) {
            if (ex.getResponse() != null) {
                Log.d(TAG, "Caught the exception. Error code: " + ex.getResponse().getReason());
                if (ex.getResponse().getStatus() == 401) {
                    SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(context);
                    //prefs.edit().putString("token", "").apply();
                    Intent intent = new Intent(context, LoginActivity.class);
                    if (!prefs.getString("token", "").equals("")) {
                        //intent.putExtra("refresh", true);
                        prefs.edit().putString("token", "").apply();
                    }
                    context.startActivity(intent);
                }

                try {
                    Log.d(TAG, "ERROR BODY:\n\n " + IOUtils.toString(ex.getResponse().getBody().in(), "UTF-8"));

                } catch (IOException e1) {
                    e1.printStackTrace();
                }
            }
        }
        onFailure(e);
    }

    @Override
    public void onRequestSuccess(RESULT result) {
        if (result == null) {
            Log.d(TAG, "Invalid response parsing. For some reason the returned result is null.");
            onFailure(new EmptyResultException("Invalid response parsing"));
        } else {
            onSuccess(result);
        }
    }

    abstract public void onFailure(SpiceException e);

    abstract public void onSuccess(RESULT result);
}
