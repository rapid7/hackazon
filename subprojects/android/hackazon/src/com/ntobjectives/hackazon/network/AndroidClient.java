package com.ntobjectives.hackazon.network;

import android.net.http.AndroidHttpClient;
import android.util.Log;
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.client.methods.*;
import org.apache.http.entity.ByteArrayEntity;
import org.apache.http.message.BasicHeader;
import org.apache.http.util.EntityUtils;
import retrofit.client.Client;
import retrofit.client.Header;
import retrofit.client.Request;
import retrofit.client.Response;
import retrofit.mime.TypedByteArray;
import retrofit.mime.TypedInput;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 07.12.2014
 * Time: 15:11
 */
public class AndroidClient implements Client {
    public static final String TAG = AndroidClient.class.getSimpleName();
    protected AndroidHttpClient client;

    public AndroidClient() {

    }

    @Override
    public Response execute(Request req) throws IOException {
        client = AndroidHttpClient.newInstance("Hackazon");
        HttpUriRequest request = convertRequest(req);

        String result;
        try {
            HttpResponse response = client.execute(request);
            int status = response.getStatusLine().getStatusCode();

            if (status != HttpStatus.SC_OK) {
                Log.d(TAG, "FAIL: Bad status from url "
                        + status + ": "
                        + response.getStatusLine().getReasonPhrase());
            }

            HttpEntity entity = response.getEntity();
            TypedInput res;

            if (entity == null) {
                Log.d(TAG, "FAIL: Null entity in response");
                res = new TypedByteArray("application/unknown", new byte[0]);

            } else {
                result = EntityUtils.toString(entity, "UTF-8");
                res = new TypedByteArray(response.getEntity().getContentType().getValue(), result.getBytes());
            }

            return new Response(req.getUrl(), status, response.getStatusLine().getReasonPhrase(), convertHeadersFromApacheToRetrofit(response.getAllHeaders()), res);
        } catch (Exception e) {
          Log.d(TAG, "Exception: " + e);

        } finally {
            client.close();
        }

        throw new IOException("");
    }

    /**
     * Convert header collection from apache one to retrofit one
     * @param apacheHeaders Apache headers
     * @return Converted collection of headers
     */
    protected List<Header> convertHeadersFromApacheToRetrofit(org.apache.http.Header[] apacheHeaders) {
        List<Header> headers = new ArrayList<Header>();

        for (org.apache.http.Header h : apacheHeaders) {
            headers.add(new Header(h.getName(), h.getValue()));
        }

        return headers;
    }

    protected HttpUriRequest convertRequest(Request source) {
        HttpUriRequest result;

        String method = source.getMethod();

        if (method.equals("GET")) {
            result = new HttpGet(source.getUrl());

        } else if (method.equals("POST")) {
            result = new HttpPost(source.getUrl());

        } else if (method.equals("PUT")) {
            result = new HttpPut(source.getUrl());

        } else if (method.equals("DELETE")) {
            result = new HttpDelete(source.getUrl());

        } else if (method.equals("HEAD")) {
            result = new HttpHead(source.getUrl());

        } else if (method.equals("OPTIONS")) {
            result = new HttpOptions(source.getUrl());

        } else if (method.equals("TRACE")) {
            result = new HttpTrace(source.getUrl());

        } else {
            throw new RuntimeException("Invalid HTTP request method: '" + method + "'");
        }

        for (Header h : source.getHeaders()) {
            result.addHeader(new BasicHeader(h.getName(), h.getValue()));
        }

        if (result instanceof HttpPost && source.getBody() != null) {
            HttpEntity entity;

            try {
                ByteArrayOutputStream bodyOut = new ByteArrayOutputStream();
                source.getBody().writeTo(bodyOut);
                entity = new ByteArrayEntity(bodyOut.toByteArray());

            } catch (IOException e) {
                entity = new ByteArrayEntity(new byte[0]);
            }

            //((HttpPost) result).setEntity(entity);
        }

        return result;
    }
}
