package com.ntobjectives.hackazon.activity;

import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import com.ntobjectives.hackazon.R;

/**
 * Shows splash screen when the app is ran without internet.
 * User: Nikolay Chervyakov
 * Date: 20.11.2014
 * Time: 17:24
 */
public class DisconnectedActivity extends Activity {
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (getIntent().getBooleanExtra("EXIT", false)) {
            finish();
        }

        if (isConnected()) {
            startActivity(new Intent(DisconnectedActivity.this, MainActivity.class));
            finish();
        }

        setContentView(R.layout.activity_disconnected);

        registerReceiver(new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                if (isConnected()) {
                    startActivity(new Intent(DisconnectedActivity.this, MainActivity.class));
                    finish();
                }
            }
        }, new IntentFilter("android.net.conn.CONNECTIVITY_CHANGE"));
    }

    protected boolean isConnected() {
        ConnectivityManager cm =
                (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);

        NetworkInfo activeNetwork = cm.getActiveNetworkInfo();
        return activeNetwork != null &&activeNetwork.isConnectedOrConnecting();
    }
}