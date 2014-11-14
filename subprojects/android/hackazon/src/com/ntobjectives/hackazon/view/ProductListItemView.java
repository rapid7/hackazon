package com.ntobjectives.hackazon.view;

import android.content.Context;
import android.view.LayoutInflater;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.model.Product;
import com.octo.android.robospice.spicelist.SpiceListItemView;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 25.10.2014
 * Time: 17:35
 */
public class ProductListItemView extends RelativeLayout implements SpiceListItemView<Product> {
    protected Product product;
    protected TextView productNameField;
    protected TextView priceField;
    protected ImageView imageView;

    public ProductListItemView(Context context) {
        super(context);
        inflateView(context);
    }

    private void inflateView(Context context) {
        LayoutInflater.from(context).inflate(R.layout.product_list_item, this);
        this.productNameField = (TextView) this.findViewById(R.id.productName);
        this.priceField = (TextView) this.findViewById(R.id.price);
        this.imageView = (ImageView) this.findViewById(R.id.imageView);
    }

    @Override
    public Product getData() {
        return product;
    }

    @Override
    public ImageView getImageView(int i) {
        return imageView;
    }

    @Override
    public int getImageViewCount() {
        return 1;
    }

    @Override
    public void update(Product product) {
        this.product = product;
        this.productNameField.setText(product.name.trim());
        this.priceField.setText("$" + Double.toString(product.Price));
    }
}
