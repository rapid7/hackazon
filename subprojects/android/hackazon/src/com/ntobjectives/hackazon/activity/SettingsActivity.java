package com.ntobjectives.hackazon.activity;

import android.app.Activity;
import android.os.Bundle;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 17.10.2014
 * Time: 11:29
 */
public class SettingsActivity extends Activity {
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (getIntent().getBooleanExtra("EXIT", false)) {
            finish();
        }

        if (savedInstanceState == null) {
            SettingsFragment fragment = new SettingsFragment();
            getFragmentManager()
                .beginTransaction()
                    .add(android.R.id.content, fragment, fragment.getClass().getSimpleName())
                .commit();
        }
    }
}