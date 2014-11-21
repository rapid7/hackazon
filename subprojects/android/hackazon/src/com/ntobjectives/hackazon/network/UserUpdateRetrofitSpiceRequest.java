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
public class UserUpdateRetrofitSpiceRequest extends RetrofitSpiceRequest<User, Hackazon> {
    public static final String TAG = UserUpdateRetrofitSpiceRequest.class.getSimpleName();
    protected User user;

    public UserUpdateRetrofitSpiceRequest(User user) {
        super(User.class, Hackazon.class);
        if (user == null || user.id <= 0) {
            throw new IllegalArgumentException("Incorrect user provided");
        }
        this.user = user;
    }

    @Override
    public User loadDataFromNetwork() throws Exception {
        Log.d(TAG, "Update user " + user.id);
        return getService().updateUser(user.id, user);
    }

    public String createCacheKey() {
        return "hackazon.user." + user.id;
    }
}
