package com.ntobjectives.hackazon.adapter;

import android.content.Context;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.model.Product;
import com.ntobjectives.hackazon.network.ImageRequestFactory;
import com.ntobjectives.hackazon.view.ProductListItemView;
import com.octo.android.robospice.persistence.DurationInMillis;
import com.octo.android.robospice.persistence.exception.SpiceException;
import com.octo.android.robospice.request.CachedSpiceRequest;
import com.octo.android.robospice.request.listener.RequestListener;
import com.octo.android.robospice.request.simple.IBitmapRequest;
import com.octo.android.robospice.spicelist.SpiceListItemView;
import com.octo.android.robospice.spicelist.okhttp.OkHttpBitmapSpiceManager;
import com.octo.android.robospice.spicelist.okhttp.OkHttpSpiceArrayAdapter;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 25.10.2014
 * Time: 17:16
 */
public class ProductsListAdapter extends OkHttpSpiceArrayAdapter<Product> {
    public static final String TAG = ProductsListAdapter.class.getSimpleName();
//    private final int layoutRes;
    private final int imageRes;
    protected ImageRequestFactory imageRequestFactory;

    public ProductsListAdapter(Context context, OkHttpBitmapSpiceManager spiceManagerBinary, Product.List objects) {
        super(context, spiceManagerBinary, objects);
        imageRequestFactory = new ImageRequestFactory(context);
        imageRequestFactory.setSampleSize(100, 100);
//        layoutRes = R.id.list_item_content;
        imageRes = R.id.imageView;
    }

    @Override
    public SpiceListItemView<Product> createView(Context context, ViewGroup viewGroup) {
        return new ProductListItemView(getContext());
    }

    @Override
    public IBitmapRequest createRequest(Product product, int i, int i2, int i3) {
        return null;
    }

    private void executeImageRequest(int position, ViewMetaData viewMetaData, boolean cacheOnly) {
        Product product = getItem(position);
        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(getContext());
        String url = prefs.getString("host", "") + "/products_pictures/" + product.big_picture;
        Log.d(TAG, "Loading image: " + url);
        viewMetaData.pendingRequest = imageRequestFactory.create(url);
        BitmapRequestListener requestListener = new BitmapRequestListener(viewMetaData);
        if (cacheOnly) {
            viewMetaData.imageState = ImageState.LOADING_CACHE_ONLY;
            spiceManagerBinary.getFromCache(viewMetaData.pendingRequest.getResultType(),
                    viewMetaData.pendingRequest.getRequestCacheKey(),
                    viewMetaData.pendingRequest.getCacheDuration(), requestListener);
        } else {
            viewMetaData.imageState = ImageState.LOADING_WITH_NETWORK;
            spiceManagerBinary.execute(viewMetaData.pendingRequest, viewMetaData.pendingRequest.getRequestCacheKey(),
                    DurationInMillis.ONE_MINUTE * 10, requestListener);
        }
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (convertView == null) {
            convertView = super.getView(position, null, parent);
            ViewMetaData viewMetaData = new ViewMetaData(convertView);
            convertView.setTag(viewMetaData);
        }

        ViewMetaData viewMetaData = (ViewMetaData) convertView.getTag();

        viewMetaData.image.setImageDrawable(new ColorDrawable(Color.parseColor("#ffffff")));
        viewMetaData.imageState = ImageState.EMPTY;

        if (viewMetaData.pendingRequest != null) {
            viewMetaData.pendingRequest.cancel();
        }

        executeImageRequest(position, viewMetaData, false);

        return convertView;
    }

    private class ViewMetaData {
        public ImageView image;
        public CachedSpiceRequest<Bitmap> pendingRequest;
        public ImageState imageState;
        public ViewMetaData(View itemView) {
            image = (ImageView) itemView.findViewById(imageRes);
        }
    }
    private enum ImageState {
        EMPTY, LOADING_WITH_NETWORK, LOADING_CACHE_ONLY, LOADING_COMPLETE
    }

    private class BitmapRequestListener implements RequestListener<Bitmap> {
        private final ViewMetaData target;

        public BitmapRequestListener(ViewMetaData target) {
            this.target = target;
        }
        @Override
        public void onRequestFailure(SpiceException spiceException) {
            // maybe we should log something here...need to differentiate
            // between regular and canceled requests
            Log.d(TAG, "Image didn't loaded");

        }
        @Override
        public void onRequestSuccess(Bitmap bitmap) {
            if (bitmap == null) {
                target.imageState = ImageState.EMPTY;
                Log.d(TAG, "Image loaded: empty");

            } else {
                target.imageState = ImageState.LOADING_COMPLETE;
                target.image.setImageBitmap(bitmap);
                Log.d(TAG, "Image loaded: " + bitmap.getWidth());
            }
            target.pendingRequest = null;
        }
    }
}
