package com.ntobjectives.hackazon.activity;

import android.app.Fragment;
import android.app.FragmentManager;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.annotation.NonNull;
import android.support.v4.app.ActionBarDrawerToggle;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.util.Log;
import android.view.*;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import com.ntobjectives.hackazon.R;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 17.10.2014
 * Time: 15:48
 */
public class MainActivity extends AbstractRootActivity {
    private static final String TAG = "MainActivity";

    private DrawerLayout drawerLayout;
    private ListView drawerList;
    private ActionBarDrawerToggle drawerToggle;

    private String[] drawerMenuItemTitles;

    protected Bundle savedInstanceState;

    protected String page;
    protected int menuPosition;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        Log.d(TAG, "onCreate");

        if (getIntent().getBooleanExtra("EXIT", false)) {
            finish();
            return;
        }

        if (!isConnected()) {
            startActivity(new Intent(this, DisconnectedActivity.class));
        }

        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(this);
        Log.d(TAG, "Host: " + prefs.getString("host", ""));
        Log.d(TAG, "Token: " + prefs.getString("token", ""));

        if (prefs.getBoolean("first_time", true) || prefs.getString("token", "").equals("")) {
            startActivity(new Intent(this, LoginActivity.class));
            finish();
        }

        setContentView(R.layout.activity_main);

        drawerMenuItemTitles = getResources().getStringArray(R.array.drawer_items);
        drawerLayout = (DrawerLayout) findViewById(R.id.drawerLayout);
        drawerList = (ListView) findViewById(R.id.drawerMenu);

        drawerLayout.setDrawerShadow(R.drawable.drawer_shadow, GravityCompat.START);
        drawerList.setAdapter(new ArrayAdapter<String>(this, R.layout.drawer_list_item, drawerMenuItemTitles));
        drawerList.setOnItemClickListener(new DrawerItemClickListener());

        // enable ActionBar app icon to behave as action to toggle nav drawer
        if (getActionBar() != null) {
            getActionBar().setDisplayHomeAsUpEnabled(true);
            getActionBar().setHomeButtonEnabled(true);
        }

        // ActionBarDrawerToggle ties together the the proper interactions
        // between the sliding drawer and the action bar app icon
        drawerToggle = new ActionBarDrawerToggle(
                this,                  /* host Activity */
                drawerLayout,         /* DrawerLayout object */
                R.drawable.ic_drawer,  /* nav drawer image to replace 'Up' caret */
                R.string.drawer_open,  /* "open drawer" description for accessibility */
                R.string.drawer_close  /* "close drawer" description for accessibility */
        ) {
            public void onDrawerClosed(View view) {
                //getActionBar().setTitle(title);
                invalidateOptionsMenu(); // creates call to onPrepareOptionsMenu()
            }

            public void onDrawerOpened(View drawerView) {
                //getActionBar().setTitle(drawerTitle);
                invalidateOptionsMenu(); // creates call to onPrepareOptionsMenu()
            }
        };

        drawerLayout.setDrawerListener(drawerToggle);

        Intent intent = getIntent();

        String intentPage = intent.getStringExtra("page");

        if (intentPage != null && !intentPage.equals("")) {
            page = intentPage;
        } else if (savedInstanceState != null){
            menuPosition = savedInstanceState.getInt("menuPosition");

        } else {
            page = null;
            menuPosition = 0;
        }

    }

    @Override
    protected void onStart() {
        Log.d(TAG, "onStart");
        super.onStart();

        //service.setHost(prefs.getString("host", "http://hackazon.webscantest.com"));

        Log.d(TAG, "page: " + String.valueOf(page));
        Log.d(TAG, "menuPosition: " + String.valueOf(menuPosition));

        if (page == null || page.equals("")) {
            selectItem(menuPosition);

        } else {
            selectItem(page);
        }
    }

    @Override
    protected void onSaveInstanceState(@NonNull Bundle outState) {
        Log.d(TAG, "onSaveInstanceState. menuPosition: " + String.valueOf(menuPosition));
        outState.putInt("menuPosition", menuPosition);
        super.onSaveInstanceState(outState);
    }

    @Override
    protected void onPause() {
        super.onPause();
        Log.d(TAG, "onPause");
    }

    @Override
    protected void onResume() {
        super.onResume();
        Log.d(TAG, "onResume");
        Log.d(TAG, "ServiceManager is started: " + String.valueOf(getSpiceManager().isStarted()));
    }

    @Override
    protected void onRestart() {
        super.onRestart();
        Log.d(TAG, "onRestart");
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        return drawerToggle.onOptionsItemSelected(item) || super.onOptionsItemSelected(item);
    }

    /* The click listner for ListView in the navigation drawer */
    private class DrawerItemClickListener implements ListView.OnItemClickListener {
        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
            selectItem(position);
        }
    }

    private void selectItem(String section) {
        for (int pos = 0; pos < drawerMenuItemTitles.length; pos++) {
            String item = drawerMenuItemTitles[pos];
            if (item.toLowerCase().equals(section)) {
                page = section;
                selectItem(pos);
                break;
            }
        }
    }

    private void selectItem(int position) {
        menuPosition = position;
        // update the main content by replacing fragments
        Fragment fragment;
        Bundle args = new Bundle();

        String item = drawerMenuItemTitles[position].toLowerCase();

        if (item.equals("settings")) {
            fragment = new SettingsFragment();

        } else if (item.equals("orders")) {
            fragment = new OrdersFragment();

        } else if (item.equals("products")) {
            fragment = new ProductsFragment();

        } else if (item.equals("profile")) {
            fragment = new ProfileFragment();

        } else if (item.equals("cart")) {
            fragment = new CartFragment();

        } else if (item.equals("contact us")) {
            fragment = new ContactMessageFragment();

        } else {
            fragment = new AboutFragment();
        }

        fragment.setArguments(args);
        FragmentManager fragmentManager = getFragmentManager();
        fragmentManager.beginTransaction().replace(R.id.contentFrame, fragment).commit();

        // update selected item and title, then close the drawer
        drawerList.setItemChecked(position, true);
        setTitle(drawerMenuItemTitles[position]);
        drawerLayout.closeDrawer(drawerList);
    }

    /**
     * When using the ActionBarDrawerToggle, you must call it during
     * onPostCreate() and onConfigurationChanged()...
     */

    @Override
    protected void onPostCreate(Bundle savedInstanceState) {
        super.onPostCreate(savedInstanceState);
        // Sync the toggle state after onRestoreInstanceState has occurred.
        drawerToggle.syncState();
    }

    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        // Pass any configuration change to the drawer toggles
        drawerToggle.onConfigurationChanged(newConfig);
    }

    @Override
    public void setTitle(CharSequence title) {
        if (getActionBar() != null) {
            getActionBar().setTitle(title);
        }
    }

    /* Called whenever we call invalidateOptionsMenu() */
    @Override
    public boolean onPrepareOptionsMenu(Menu menu) {
        // If the nav drawer is open, hide action items related to the content view
//        boolean drawerOpen = drawerLayout.isDrawerOpen(drawerList);
        return super.onPrepareOptionsMenu(menu);
    }

    public static class AboutFragment extends Fragment {
        public AboutFragment() {
            // Empty constructor required for fragment subclasses
        }

        @Override
        public View onCreateView(LayoutInflater inflater, ViewGroup container,
                                 Bundle savedInstanceState) {
            return inflater.inflate(R.layout.activity_about, container, false);
        }
    }
}