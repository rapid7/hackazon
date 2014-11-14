package com.ntobjectives.hackazon.network;

import android.util.Log;
import com.ntobjectives.hackazon.model.User;
import com.octo.android.robospice.request.retrofit.RetrofitSpiceRequest;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 21.10.2014
 * Time: 18:35
 */
public class MeRetrofitSpiceRequest extends RetrofitSpiceRequest<User, Hackazon> {
    public static final String TAG = MeRetrofitSpiceRequest.class.getSimpleName();

    public MeRetrofitSpiceRequest() {
        super(User.class, Hackazon.class);
    }

    @Override
    public User loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Load user (me) from network.");
        return getService().me();
    }

    public String createCacheKey() {
        return "hackazon.user.me";
    }
}
