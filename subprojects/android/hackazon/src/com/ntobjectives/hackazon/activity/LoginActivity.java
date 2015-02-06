package com.ntobjectives.hackazon.activity;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.annotation.NonNull;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import com.google.gson.Gson;
import com.google.gson.JsonSyntaxException;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.model.Auth;
import com.ntobjectives.hackazon.network.AuthRequest;
import com.octo.android.robospice.persistence.exception.SpiceException;
import com.octo.android.robospice.request.listener.RequestListener;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 20.10.2014
 * Time: 17:37
 */
public class LoginActivity extends AbstractRootActivity {
    public static final String TAG = "LoginActivity";

    protected String host;
    protected String username;
    protected String password;
    protected String token;

    protected EditText hostField;
    protected EditText usernameField;
    protected EditText passwordField;
    protected Button button;

    protected boolean refresh = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (getIntent().getBooleanExtra("EXIT", false)) {
            finish();
        }

        setContentView(R.layout.activity_login);

        SharedPreferences pref = PreferenceManager.getDefaultSharedPreferences(this);
        refresh = getIntent().getBooleanExtra("refresh", false);

        if (!pref.getBoolean("first_time", true) && !pref.getString("token", "").equals("") && !refresh) {
            startActivity(new Intent(this, MainActivity.class));
        }

        if (savedInstanceState != null) {
            host = savedInstanceState.getString("host", pref.getString("host", ""));
            username = savedInstanceState.getString("username", "");
            password = savedInstanceState.getString("password", "");

        } else {
            host = pref.getString("host", "");
            username = pref.getString("username", "");
            password = pref.getString("password", "");
        }

        hostField = (EditText) findViewById(R.id.host);
        usernameField = (EditText) findViewById(R.id.username);
        passwordField = (EditText) findViewById(R.id.password);
        button = (Button) findViewById(R.id.loginButton);

        if (!host.equals("")) {
            hostField.setText(host);
        }
        usernameField.setText(username);
        passwordField.setText(password);

        button.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                button.setEnabled(false);

                host = hostField.getText().toString();
                username = usernameField.getText().toString();
                password = passwordField.getText().toString();

                Log.d(TAG, "Clicked with: username = " + username + ", password = " + password + ", host = " + host);
                AuthRequest req = new AuthRequest(LoginActivity.this, host, username, password);
                req.setRefresh(refresh);
                getSpiceManager().execute(req, new AuthRequestListener(host, username, password));
            }
        });
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.auth, menu);
        return true;
    }

    @Override
    protected void onSaveInstanceState(@NonNull Bundle outState) {
        outState.putString("host", host);
        outState.putString("username", username);
        outState.putString("password", password);

        super.onSaveInstanceState(outState);
    }

    @Override
    protected void onRestoreInstanceState(@NonNull Bundle savedInstanceState) {
        super.onRestoreInstanceState(savedInstanceState);
    }

    @Override
    protected void onStart() {
        super.onStart();
    }

    public final class AuthRequestListener implements RequestListener<String> {
        protected String host;
        protected String username;
        protected String password;

        public AuthRequestListener(String host, String username, String password) {
            this.host = host;
            this.username = username;
            this.password = password;
        }

        @Override
        public void onRequestFailure(SpiceException spiceException) {
            Toast.makeText(LoginActivity.this, "Incorrect request.", Toast.LENGTH_SHORT).show();
            button.setEnabled(true);
        }

        @Override
        public void onRequestSuccess(String response) {
            Log.d(TAG, response);
            button.setEnabled(true);

            Gson gson = new Gson();
            try {
                Auth auth = gson.fromJson(response, Auth.class);
                token = auth.token;
                SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(LoginActivity.this.getApplicationContext());
                prefs
                    .edit()
                        .putString("host", host)
                        .putString("username", username)
                        .putString("password", password)
                        .putString("token", token)
                        .putBoolean("first_time", false)
                    .apply();

                Log.d(TAG, "Token: " + token);
                Log.d(TAG, "Host: " + host);

                startActivity(new Intent(LoginActivity.this, MainActivity.class));
                finish();

            } catch (JsonSyntaxException ex) {
                Toast.makeText(LoginActivity.this, "Service response is invalid.", Toast.LENGTH_SHORT).show();
            }
        }
    }
}
