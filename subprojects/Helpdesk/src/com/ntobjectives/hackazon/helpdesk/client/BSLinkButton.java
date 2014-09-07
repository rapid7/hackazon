package com.ntobjectives.hackazon.helpdesk.client;

import com.google.gwt.user.client.ui.Anchor;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 02.09.2014
 * Time: 12:01
 */
public class BSLinkButton extends Anchor {
    public BSLinkButton() {
        super();
        commonInit();
    }

    public BSLinkButton(String link, String text) {
        super();
        commonInit();
        setText(text);
        getElement().setAttribute("href", link);
    }

    protected void commonInit() {
        addStyleName("btn btn-primary");
        removeStyleName("gwt-InlineHyperlink");
    }
}
