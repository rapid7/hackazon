package com.ntobjectives.hackazon.activity;

import android.app.Fragment;
import android.content.Context;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.util.Log;
import android.view.InflateException;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.model.User;
import com.ntobjectives.hackazon.network.MeRetrofitSpiceRequest;
import com.ntobjectives.hackazon.network.UserUpdateRetrofitSpiceRequest;
import com.octo.android.robospice.persistence.exception.SpiceException;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 17.10.2014
 * Time: 14:54
 */
public class ProfileFragment extends Fragment {
    public static final String TAG = ProfileFragment.class.getSimpleName();
    private View view;
    private User user;
    private View loadingView;
    private View progressBarView;
    private Button reloadButton;
    private View profileView;

    private EditText firstNameField;
    private EditText lastNameField;
    private Button saveButton;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        Log.d(TAG, "onCreateView");
        if (view != null) {
            ViewGroup parent = (ViewGroup) view.getParent();
            if (parent != null) {
                parent.removeView(view);
            }
        }
        try {
            view = inflater.inflate(R.layout.profile_fragment, container, false);
        } catch (InflateException ignored) {
        }
        return view;
    }

    @Override
    public void onStart() {
        Log.d(TAG, "onStart");
        super.onStart();
        loadingView = getActivity().findViewById(R.id.loading_layout);
        profileView = getActivity().findViewById(R.id.profileView);
        progressBarView = getActivity().findViewById(R.id.loaderBox);
        reloadButton = (Button) getActivity().findViewById(R.id.reloadButton);

        firstNameField = (EditText) getActivity().findViewById(R.id.firstName);
        lastNameField = (EditText) getActivity().findViewById(R.id.lastName);
        saveButton = (Button) getActivity().findViewById(R.id.saveButton);

        reloadButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                loadProfile();
            }
        });

        saveButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                saveButton.setEnabled(false);
                saveProfile();
            }
        });

        loadProfile();
    }

    protected void loadProfile() {
        profileView.setVisibility(View.GONE);
        loadingView.setVisibility(View.VISIBLE);
        reloadButton.setVisibility(View.GONE);
        progressBarView.setVisibility(View.VISIBLE);

        getActivity().setProgressBarIndeterminateVisibility(true);
        ((AbstractRootActivity) getActivity()).getSpiceManager()
                .execute(new MeRetrofitSpiceRequest(), new MeRequestListener(this.getActivity()));
    }

    protected void showProfile() {
        loadingView.setVisibility(View.GONE);
        profileView.setVisibility(View.VISIBLE);

        firstNameField.setText(user.first_name);
        lastNameField.setText(user.last_name);
    }

    protected void saveProfile() {
        user.first_name = firstNameField.getText().toString();
        user.last_name = lastNameField.getText().toString();

        getActivity().setProgressBarIndeterminateVisibility(true);
        ((AbstractRootActivity) getActivity()).getSpiceManager()
                .execute(new UserUpdateRetrofitSpiceRequest(user), new UserUpdateRequestListener(this.getActivity()));
    }

    public final class MeRequestListener extends com.ntobjectives.hackazon.network.RequestListener<User> {

        protected MeRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException spiceException) {
            if (getActivity() != null) {
                getActivity().setProgressBarIndeterminateVisibility(false);
                Toast.makeText(ProfileFragment.this.getActivity(), "failure", Toast.LENGTH_SHORT).show();
                reloadButton.setVisibility(View.VISIBLE);
                progressBarView.setVisibility(View.GONE);
            }
        }

        @Override
        public void onSuccess(User user) {
            if (getActivity() != null) {
                getActivity().setProgressBarIndeterminateVisibility(false);
                ProfileFragment.this.user = user;
                showProfile();
            }
        }
    }

    public final class UserUpdateRequestListener extends com.ntobjectives.hackazon.network.RequestListener<User> {

        protected UserUpdateRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException spiceException) {
            if (getActivity() != null) {
                getActivity().setProgressBarIndeterminateVisibility(false);
                Toast.makeText(ProfileFragment.this.getActivity(), "Error. Please try again.", Toast.LENGTH_SHORT).show();
                saveButton.setEnabled(true);
            }
        }

        @Override
        public void onSuccess(User user) {
            if (getActivity() != null) {
                getActivity().setProgressBarIndeterminateVisibility(false);
                saveButton.setEnabled(true);
                Toast.makeText(ProfileFragment.this.getActivity(), "Profile is successfully saved.", Toast.LENGTH_SHORT).show();
            }
        }
    }
}