package com.ntobjectives.hackazon.network;

import com.octo.android.robospice.persistence.exception.SpiceException;
/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 05.12.2014
 * Time: 13:40
 */
public class EmptyResultException extends SpiceException {
    public EmptyResultException(String detailMessage) {
        super(detailMessage);
    }
}
