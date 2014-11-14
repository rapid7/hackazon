package com.ntobjectives.hackazon.network;

import android.content.Context;
import android.graphics.Bitmap;
import com.octo.android.robospice.persistence.DurationInMillis;
import com.octo.android.robospice.request.CachedSpiceRequest;
import com.octo.android.robospice.request.simple.BitmapRequest;
import roboguice.util.temp.Ln;

import java.io.File;
import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 30.10.2014
 * Time: 14:15
 */
public class ImageRequestFactory {

    private final Context context;
    private int targetWidth = -1;
    private int targetHeight = -1;
    public static final String SMALL_THUMB_SQUARE = "s";
    public static final String LARGE_THUMB_SQUARE = "q";
    public static final String THUMBNAIL = "t";
    public static final String SMALL_240 = "m";

    public ImageRequestFactory(Context context) {
        this.context = context;
    }
    public ImageRequestFactory setSampleSize(int height, int width) {
        targetWidth = width;
        targetHeight = height;
        return this;
    }

    public CachedSpiceRequest<Bitmap> create(String photoSource) {
        String photoUrl = photoSource.substring(0);
        File cacheFile = null;
        String filename = null;
        try {
            filename = URLEncoder.encode(photoUrl, "UTF-8");
            cacheFile = new File(context.getCacheDir(), filename);
        } catch (UnsupportedEncodingException e) {
            Ln.e(e);
        }
        BitmapRequest request = new BitmapRequest(photoUrl, targetWidth, targetHeight, cacheFile);
        return new CachedSpiceRequest<Bitmap>(request, filename, DurationInMillis.ONE_MINUTE * 10);
    }

    public int getTargetHeight() {
        return targetHeight;
    }
    public int getTargetWidth() {
        return targetWidth;
    }
}
