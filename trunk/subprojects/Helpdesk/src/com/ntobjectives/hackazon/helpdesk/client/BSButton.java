package com.ntobjectives.hackazon.helpdesk.client;

import com.google.gwt.user.client.ui.Button;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 02.09.2014
 * Time: 12:01
 */
public class BSButton extends Button {
    public BSButton() {
        super();
        commonInit();
    }

    public BSButton(String text) {
        super();
        commonInit();
        setText(text);
    }

    protected void commonInit() {
        addStyleName("btn btn-primary");
        removeStyleName("gwt-Button");
    }
}
