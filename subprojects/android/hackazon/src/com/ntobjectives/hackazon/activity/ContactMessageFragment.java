package com.ntobjectives.hackazon.activity;

import android.app.Fragment;
import android.content.Context;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.util.Log;
import android.util.Patterns;
import android.view.InflateException;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.model.ContactMessage;
import com.ntobjectives.hackazon.network.ContactMessageAddRetrofitSpiceRequest;
import com.octo.android.robospice.persistence.exception.SpiceException;

import java.util.ArrayList;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 24.03.2015
 * Time: 12:21
 */
public class ContactMessageFragment extends Fragment {
    public static final String TAG = ContactMessageFragment.class.getSimpleName();
    private View view;
    private View loadingView;
    private View progressBarView;
    private Button reloadButton;
    private View contactMessageView;

    private EditText nameField;
    private EditText emailField;
    private EditText phoneField;
    private EditText messageField;
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
            view = inflater.inflate(R.layout.contact_message_fragment, container, false);
        } catch (InflateException ignored) {
        }
        return view;
    }

    @Override
    public void onStart() {
        Log.d(TAG, "onStart");
        super.onStart();
        loadingView = getActivity().findViewById(R.id.loading_layout);
        contactMessageView = getActivity().findViewById(R.id.contactMessageView);
        progressBarView = getActivity().findViewById(R.id.loaderBox);
        reloadButton = (Button) getActivity().findViewById(R.id.reloadButton);

        nameField = (EditText) getActivity().findViewById(R.id.name);
        emailField = (EditText) getActivity().findViewById(R.id.email);
        phoneField = (EditText) getActivity().findViewById(R.id.phoneField);
        messageField = (EditText) getActivity().findViewById(R.id.messageField);
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
                sendContactMessage();
            }
        });

        showContactMessage();
    }

    protected void loadProfile() {
        contactMessageView.setVisibility(View.GONE);
        loadingView.setVisibility(View.VISIBLE);
        reloadButton.setVisibility(View.GONE);
        progressBarView.setVisibility(View.VISIBLE);

//        getActivity().setProgressBarIndeterminateVisibility(true);
//        ((AbstractRootActivity) getActivity()).getSpiceManager()
//                .execute(new MeRetrofitSpiceRequest(), new ContactMessageAddRequestListener(this.getActivity()));
    }

    protected void showContactMessage() {
        loadingView.setVisibility(View.GONE);
        contactMessageView.setVisibility(View.VISIBLE);
    }

    protected void sendContactMessage() {
//        user.first_name = nameField.getText().toString();
//        user.last_name = emailField.getText().toString();

        ContactMessage message = new ContactMessage();

        message.email = emailField.getText().toString();
        message.name = nameField.getText().toString();
        message.phone = phoneField.getText().toString();
        message.message = messageField.getText().toString();

        if (!validateMessage(message)) {
            saveButton.setEnabled(true);
            return;
        }

        getActivity().setProgressBarIndeterminateVisibility(true);
        ((AbstractRootActivity) getActivity()).getSpiceManager()
                .execute(new ContactMessageAddRetrofitSpiceRequest(message), new ContactMessageAddRequestListener(this.getActivity()));
    }

    protected boolean validateMessage(ContactMessage message) {
        ArrayList<String> errors = new ArrayList<String>(4);

        if (message.name.equals("")) {
            errors.add("Please enter your name.");
        }

        if (message.email.equals("")) {
            errors.add("Please enter your email.");

        } else if (!Patterns.EMAIL_ADDRESS.matcher(message.email.trim()).matches()) {
            errors.add("You entered incorrect email.");
        }

        if (message.phone.equals("")) {
            errors.add("Please enter your phone.");
        }

        if (message.message.equals("")) {
            errors.add("Please enter your message.");
        }

        StringBuilder sb = new StringBuilder();
        for (String er : errors) {
            sb.append(er).append("\n");
        }

        if (errors.size() > 0) {
            Toast.makeText(ContactMessageFragment.this.getActivity(), sb.toString().trim(), Toast.LENGTH_LONG).show();
            return false;
        }

        return true;
    }

    public final class ContactMessageAddRequestListener extends com.ntobjectives.hackazon.network.RequestListener<ContactMessage> {

        protected ContactMessageAddRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException spiceException) {
            if (getActivity() != null) {
                getActivity().setProgressBarIndeterminateVisibility(false);
                Toast.makeText(ContactMessageFragment.this.getActivity(), "failure", Toast.LENGTH_SHORT).show();
                reloadButton.setVisibility(View.VISIBLE);
                progressBarView.setVisibility(View.GONE);
                saveButton.setEnabled(true);
            }
        }

        @Override
        public void onSuccess(ContactMessage user) {
            if (getActivity() != null) {
                getActivity().setProgressBarIndeterminateVisibility(false);
                saveButton.setEnabled(true);
                messageField.setText("");
                Toast.makeText(ContactMessageFragment.this.getActivity(),
                        "Your message has been successfully sent. We will reply shortly.", Toast.LENGTH_LONG).show();
            }
        }
    }
}