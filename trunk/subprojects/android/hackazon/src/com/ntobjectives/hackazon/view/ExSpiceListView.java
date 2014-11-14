package com.ntobjectives.hackazon.view;

import android.content.Context;
import android.util.AttributeSet;
import com.octo.android.robospice.spicelist.SpiceListView;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 25.10.2014
 * Time: 17:35
 */
public class ExSpiceListView extends SpiceListView {
    public ExSpiceListView(Context context) {
        super(context);
    }

    public ExSpiceListView(Context context, AttributeSet attrs, int defStyle) {
        super(context, attrs, defStyle);
    }

    public ExSpiceListView(Context context, AttributeSet attrs) {
        super(context, attrs);
    }

    @Override
    public int computeVerticalScrollOffset() {
        return super.computeVerticalScrollOffset();
    }
}
