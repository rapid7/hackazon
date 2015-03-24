package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.ntobjectives.hackazon.model.ContactMessage;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 24.03.2015
 * Time: 12:17
 */
public class ContactMessageAddRetrofitSpiceRequest extends RetrofitSpiceRequest<ContactMessage, Hackazon> {
    public static final String TAG = ContactMessageAddRetrofitSpiceRequest.class.getSimpleName();
    protected ContactMessage contactMessage;

    public ContactMessageAddRetrofitSpiceRequest(ContactMessage contactMessage) {
        super(ContactMessage.class, Hackazon.class);
        this.contactMessage = contactMessage;
    }

    @Override
    public ContactMessage loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Add order address on network service.");
        return getService().addContactMessages(contactMessage);
    }
}
