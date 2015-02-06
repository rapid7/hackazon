package com.ntobjectives.hackazon.activity;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.view.Menu;
import android.view.MenuItem;
import android.view.Window;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.network.JsonSpiceService;
import com.octo.android.robospice.SpiceManager;
import com.octo.android.robospice.spicelist.okhttp.OkHttpBitmapSpiceManager;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 17.10.2014
 * Time: 12:18
 */
public class AbstractRootActivity extends Activity {
    static final String TAG = "AbstractRootActivity";

    private SpiceManager spiceManager = new SpiceManager(JsonSpiceService.class);
    private OkHttpBitmapSpiceManager spiceManagerBinary = new OkHttpBitmapSpiceManager();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_INDETERMINATE_PROGRESS);
        setProgressBarIndeterminateVisibility(false);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case R.id.settings:
                startActivity(new Intent(this, SettingsActivity.class));
                return true;
            case R.id.about:
                startActivity(new Intent(this, AboutActivity.class));
                return true;
            case R.id.logout:
                SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(this);
                prefs.edit().putString("token", "").apply();
                startActivity(new Intent(this, LoginActivity.class));
                finish();
                return true;
            case  R.id.exit:
                exit();
                return true;
            default:
                return super.onOptionsItemSelected(item);
        }
    }

    public void exit() {
        Intent intent = new Intent(getApplicationContext(), MainActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        intent.putExtra("EXIT", true);
        startActivity(intent);
        finish();
    }

    @Override
    protected void onStart() {
        spiceManager.start(this);
        spiceManagerBinary.start(this);
        super.onStart();
    }
    @Override
    protected void onStop() {
        spiceManager.shouldStop();
        spiceManagerBinary.shouldStop();
        super.onStop();
    }
    public SpiceManager getSpiceManager() {
        return spiceManager;
    }
    public OkHttpBitmapSpiceManager getSpiceManagerBinary() {
        return spiceManagerBinary;
    }

    protected boolean isConnected() {
        ConnectivityManager cm =
                (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);

        NetworkInfo activeNetwork = cm.getActiveNetworkInfo();
        return activeNetwork != null && activeNetwork.isConnectedOrConnecting();
    }
}
