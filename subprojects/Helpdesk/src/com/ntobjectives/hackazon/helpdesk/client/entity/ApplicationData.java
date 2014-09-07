package com.ntobjectives.hackazon.helpdesk.client.entity;

import java.io.Serializable;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 03.09.2014
 * Time: 14:04
 */
public class ApplicationData implements Serializable {
    private User user;
    private boolean isAutorized;

    public User getUser() {
        return user;
    }

    public void setUser(User user) {
        this.user = user;
    }

    public boolean isAutorized() {
        return isAutorized;
    }

    public void setAutorized(boolean isAutorized) {
        this.isAutorized = isAutorized;
    }
}
