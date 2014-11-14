package com.ntobjectives.hackazon.view;

import android.content.Context;
import android.widget.ImageView;
import android.widget.TextView;
import com.ntobjectives.hackazon.model.Category;
import com.octo.android.robospice.spicelist.SpiceListItemView;
import org.apache.commons.lang3.StringUtils;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 25.10.2014
 * Time: 17:35
 */
public class CategoryListItemView extends TextView implements SpiceListItemView<Category> {
    protected Category category;

    public CategoryListItemView(Context context) {
        super(context);
       setPadding(10, 10, 10, 10);
    }

    @Override
    public Category getData() {
        return category;
    }

    @Override
    public ImageView getImageView(int i) {
        return null;
    }

    @Override
    public int getImageViewCount() {
        return 0;
    }

    @Override
    public void update(Category category) {
        this.category = category;
        String name = category.name;
        if (name.equals("0_ROOT")) {
            name = "All";
        }
        setText(StringUtils.repeat("    ", category.depth) + name);
    }
}
