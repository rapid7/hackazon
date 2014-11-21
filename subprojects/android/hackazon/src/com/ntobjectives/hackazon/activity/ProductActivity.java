package com.ntobjectives.hackazon.activity;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.model.Cart;
import com.ntobjectives.hackazon.model.CartItem;
import com.ntobjectives.hackazon.model.Product;
import com.ntobjectives.hackazon.network.*;
import com.octo.android.robospice.persistence.DurationInMillis;
import com.octo.android.robospice.persistence.exception.SpiceException;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 27.10.2014
 * Time: 13:06
 */
public class ProductActivity extends AbstractRootActivity {
    protected View loadingView;
    protected View productView;
    protected Button addToCartButton;
    protected Button backButton;
    protected Button goToCartButton;
    protected int id;
    protected Product product;
    protected Cart cart = null;

    ProductRetrofitSpiceRequest req;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (getIntent().getBooleanExtra("EXIT", false)) {
            finish();
        }

        setContentView(R.layout.activity_product);

        id = getIntent().getExtras().getInt("id", 0);
        req = new ProductRetrofitSpiceRequest(id);

        productView = findViewById(R.id.productView);
        loadingView = findViewById(R.id.loading_layout);
        addToCartButton = (Button) findViewById(R.id.addToCartButton);
        backButton = (Button) findViewById(R.id.backButton);
        goToCartButton = (Button) findViewById(R.id.goToCartButton);
        loadingView.setVisibility(View.VISIBLE);

        String lastRequestCacheKey = req.createCacheKey();
        getSpiceManager().execute(req, lastRequestCacheKey, DurationInMillis.ONE_MINUTE,
                new ProductRequestListener(getApplicationContext()));

        addToCartButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (product == null) {
                    return;
                }
                requestCartAndAddProduct(product);
            }
        });

        backButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                ProductActivity.this.finish();
            }
        });

        goToCartButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(ProductActivity.this, MainActivity.class);
                intent.putExtra("page", "cart");
                startActivity(intent);
                finish();
            }
        });
    }

    protected void requestCartAndAddProduct(Product product) {
        if (cart != null) {
            requestAddOrUpdate(product);
        } else {
            SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(this);
            getSpiceManager().execute(
                    new CartRetrofitSpiceRequest(prefs.getString("cart_uid", "")),
                    new CartRequestListener(getApplicationContext(), product)
            );
        }
    }

    protected void requestAddProduct(Product product) {
        if (cart == null) {
            Toast.makeText(ProductActivity.this, "Can't add product without a cart.", Toast.LENGTH_SHORT).show();
            return;
        }

        CartItem item = new CartItem();
        item.name = product.name;
        item.cart_id = cart.id;
        item.price = product.Price;
        item.product_id = product.productID;
        item.qty = 1;

        getSpiceManager().execute(
                new CartItemAddRetrofitSpiceRequest(item),
                new AddCartItemRequestListener(getApplicationContext())
        );
    }

    protected void requestUpdateCartItem(CartItem item) {
        requestUpdateCartItem(item, 1);
    }

    protected void requestUpdateCartItem(CartItem item, int quantity) {
        if (cart == null) {
            Toast.makeText(ProductActivity.this, "Can't add product without a cart.", Toast.LENGTH_SHORT).show();
            return;
        }

        item.qty += quantity;

        getSpiceManager().execute(
                new CartItemUpdateRetrofitSpiceRequest(item),
                new UpdateCartItemRequestListener(getApplicationContext())
        );
    }

    private void updateViewContent(Product product) {
        TextView nameField = (TextView) productView.findViewById(R.id.productName);
        TextView priceField = (TextView) productView.findViewById(R.id.price);
        TextView descField = (TextView) productView.findViewById(R.id.description);

        nameField.setText(product.name.trim());
        priceField.setText("$" + Double.toString(product.Price));
        descField.setText(product.description.trim());

        setTitle(product.name);
        loadingView.setVisibility(View.GONE);
        productView.setVisibility(View.VISIBLE);
    }

    public class ProductRequestListener extends com.ntobjectives.hackazon.network.RequestListener<Product> {

        protected ProductRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException e) {
            Toast.makeText(ProductActivity.this, "failure", Toast.LENGTH_SHORT).show();
        }

        @Override
        public void onSuccess(Product product) {
            ProductActivity.this.product = product;
            updateViewContent(product);
        }
    }

    protected class AddCartItemRequestListener extends RequestListener<CartItem> {

        protected AddCartItemRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException e) {
            Toast.makeText(ProductActivity.this, "failure", Toast.LENGTH_SHORT).show();
        }

        @Override
        public void onSuccess(CartItem item) {
            cart.items.add(item);
            Toast.makeText(ProductActivity.this, "Successfully added to cart", Toast.LENGTH_SHORT).show();
        }
    }

    protected class UpdateCartItemRequestListener extends RequestListener<CartItem> {

        protected UpdateCartItemRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException e) {
            Toast.makeText(ProductActivity.this, "failure", Toast.LENGTH_SHORT).show();
        }

        @Override
        public void onSuccess(CartItem item) {
            Toast.makeText(ProductActivity.this, "Successfully added one more item to cart", Toast.LENGTH_SHORT).show();
        }
    }

    protected class CartRequestListener extends RequestListener<Cart> {
        protected Product product;

        public CartRequestListener(Context context, Product product) {
            super(context);
            this.product = product;
        }

        @Override
        public void onFailure(SpiceException e) {
            Toast.makeText(ProductActivity.this, "Failed to get cart", Toast.LENGTH_SHORT).show();
        }

        @Override
        public void onSuccess(Cart cart) {
            Log.d(TAG, "Successfully fetched cart");
            ProductActivity.this.cart = cart;
            SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(ProductActivity.this);
            prefs.edit().putString("cart_uid", cart.uid).apply();

            requestAddOrUpdate(product);
        }
    }

    private void requestAddOrUpdate(Product product) {
        CartItem existingItem = null;
        for (CartItem item : cart.items) {
            if (item.product_id == product.productID) {
                existingItem = item;
                break;
            }
        }
        if (existingItem == null) {
            requestAddProduct(product);
        } else {
            requestUpdateCartItem(existingItem);
        }
    }
}