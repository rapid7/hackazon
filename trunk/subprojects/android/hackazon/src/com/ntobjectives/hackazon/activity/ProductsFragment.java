package com.ntobjectives.hackazon.activity;

import android.app.Fragment;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.util.Log;
import android.view.InflateException;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AbsListView;
import android.widget.AdapterView;
import android.widget.TextView;
import android.widget.Toast;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.adapter.ProductsListAdapter;
import com.ntobjectives.hackazon.dialog.CategorySelectDialogFragment;
import com.ntobjectives.hackazon.model.Category;
import com.ntobjectives.hackazon.model.Product;
import com.ntobjectives.hackazon.network.CategoriesRetrofitSpiceRequest;
import com.ntobjectives.hackazon.network.ProductsRetrofitSpiceRequest;
import com.ntobjectives.hackazon.view.ExSpiceListView;
import com.ntobjectives.hackazon.view.ProductListItemView;
import com.octo.android.robospice.SpiceManager;
import com.octo.android.robospice.persistence.DurationInMillis;
import com.octo.android.robospice.persistence.exception.SpiceException;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 17.10.2014
 * Time: 14:50
 */
public class ProductsFragment extends Fragment {
    public static final String TAG = ProductsFragment.class.getSimpleName();

    private ExSpiceListView productsListView;
    private View loadingView;

    private View view;
    private TextView categoryView;

    protected ProductsRetrofitSpiceRequest req;
    protected Product.List productList = null;
    protected boolean listIsFull = false;
    private int currentPage = 1;
    protected int offsetY = 0;
    //protected int totalPages = 1;
    protected boolean isLoading = false;
    protected Category.List categories;
    private Category category;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        Log.d(TAG, "onCreate");
        //getActivity().requestWindowFeature(Window.FEATURE_INDETERMINATE_PROGRESS);
        super.onCreate(savedInstanceState);
    }

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        Log.d(TAG, "onCreateView");
        if (view != null) {
            ViewGroup parent = (ViewGroup) view.getParent();
            if (parent != null) {
                parent.removeView(view);
            }
        }
        try {
            view = inflater.inflate(R.layout.products_fragment, container, false);
        } catch (InflateException ignored) {
        }
        return view;
    }

    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        Log.d(TAG, "onActivityCreated");
        super.onActivityCreated(savedInstanceState);
        productsListView = (ExSpiceListView) getActivity().findViewById(R.id.listview_products);
        loadingView = getActivity().findViewById(R.id.loading_layout);
        categoryView = (TextView) getActivity().findViewById(R.id.loadingCategoriesField);

        getActivity().setProgressBarIndeterminateVisibility(false);

        productsListView.setOnItemClickListener(new OnItemClickListener());
        productsListView.setOnScrollListener(new OnScrollListener());
        categoryView.setOnClickListener(new OnCategoryClickListener());
    }

    @Override
    public void onStart() {
        Log.d(TAG, "onStart");
        super.onStart();

        if (categories == null) {
            startCategoriesRequest();
        }

        loadingView.setVisibility(View.GONE);
    }

    /**
     * Load product page from current category
     * @param page Page to load
     */
    protected void startRequest(int page) {
        if (isLoading || listIsFull) {
            return;
        }
        isLoading = true;
        if (productList == null) {
            loadingView.setVisibility(View.VISIBLE);
        }
        Log.d(TAG, "Start request /api/product?page=" + page);
        offsetY = productsListView.computeVerticalScrollOffset();
        req = new ProductsRetrofitSpiceRequest(page, category != null ? category.categoryID : 0);
        String lastRequestCacheKey = req.createCacheKey();
        getActivity().setProgressBarIndeterminateVisibility(true);
        ((AbstractRootActivity) getActivity()).getSpiceManager().execute(req, lastRequestCacheKey, DurationInMillis.ONE_MINUTE,
                new ProductsRequestListener(getActivity()));
    }

    /**
     * Perform request for categories to fill the category selector
     */
    protected void startCategoriesRequest() {
        if (isLoading) {
            return;
        }
        isLoading = true;
        Log.d(TAG, "Start request /api/category");

        CategoriesRetrofitSpiceRequest catReq = new CategoriesRetrofitSpiceRequest(1);
        String lastRequestCacheKey = catReq.createCacheKey();
        getActivity().setProgressBarIndeterminateVisibility(true);

        SpiceManager spiceManager = ((AbstractRootActivity) getActivity()).getSpiceManager();
        spiceManager.execute(catReq, lastRequestCacheKey, DurationInMillis.ONE_MINUTE, new CategoriesRequestListener(getActivity()));
    }

    /**
     * Updates product list after loading it
     * @param response Product list response from REST
     */
    private void updateListViewContent(Product.ProductsResponse response) {
        Log.d(TAG, "Update view with page " + currentPage);
        AbstractRootActivity act = (AbstractRootActivity) getActivity();
        ProductsListAdapter productListAdapter;
        if (productList == null) {
            productList = response.data;
            productListAdapter = new ProductsListAdapter(getActivity(), act.getSpiceManagerBinary(), productList);
            productsListView.setAdapter(productListAdapter);

        } else {
            if (response.data.size() > 0) {
                productListAdapter = (ProductsListAdapter) productsListView.getAdapter();
                productListAdapter.addAll(response.data);
                currentPage++;
                productsListView.smoothScrollBy(100, 1000);
            }
        }

        loadingView.setVisibility(View.GONE);
        productsListView.setVisibility(View.VISIBLE);
    }

    /**
     * Select category and load the product list from this category
     * @param id Category id
     */
    public void selectCategory(int id) {
        Category newCat = null;
        for (Category cat : categories) {
            if (cat.categoryID == id) {
                categoryView.setText(cat.name.equals("0_ROOT") ? "All" : cat.name);
                newCat = cat;
                currentPage = 1;
                productList = null;
                listIsFull = false;
                break;
            }
        }

        if (category == null || newCat != category) {
            category = newCat;
            startRequest(currentPage);
        }
    }

    /**
     * Executed after loading the page of products
     */
    public final class ProductsRequestListener extends com.ntobjectives.hackazon.network.RequestListener<Product.ProductsResponse> {

        protected ProductsRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException spiceException) {
            isLoading = false;
            if (getActivity() != null) {
                getActivity().setProgressBarIndeterminateVisibility(false);
                Toast.makeText(ProductsFragment.this.getActivity(), "failure", Toast.LENGTH_SHORT).show();
            }
        }

        @Override
        public void onSuccess(Product.ProductsResponse response) {
            if (getActivity() != null) {
                getActivity().setProgressBarIndeterminateVisibility(false);
                updateListViewContent(response);
            }
            isLoading = false;
            if (response.data.size() == 0) {
                listIsFull = true;
            }
            //Toast.makeText(ProductsFragment.this.getActivity(), "success", Toast.LENGTH_SHORT).show();
        }
    }

    /**
     * Perform executing product details view after click the item in the product list
     */
    public final class OnItemClickListener implements AdapterView.OnItemClickListener {
        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
            ProductListItemView v = (ProductListItemView) view;
            Log.d(TAG, v.getData().name);
            Intent intent = new Intent(getActivity(), ProductActivity.class);
            intent.putExtra("id", v.getData().productID);
            startActivity(intent);
        }
    }

    /**
     * Loads subsequent page after user reach the bottom of the list
     */
    protected final class OnScrollListener implements AbsListView.OnScrollListener {
        @Override
        public void onScrollStateChanged(AbsListView view, int scrollState) {
            //Log.d(TAG, "Scroll list state: " + scrollState);
        }

        @Override
        public void onScroll(AbsListView view, int firstVisibleItem, int visibleItemCount, int totalItemCount) {

            if (totalItemCount > 0 && firstVisibleItem + visibleItemCount >= totalItemCount) {
                startRequest(currentPage + 1);
            }
        }
    }

    /**
     * Load category list
     */
    public final class CategoriesRequestListener extends com.ntobjectives.hackazon.network.RequestListener<Category.CategoriesResponse> {

        protected CategoriesRequestListener(Context context) {
            super(context);
        }

        @Override
        public void onFailure(SpiceException spiceException) {
            isLoading = false;
            if (getActivity() == null) {
                return;
            }
            getActivity().setProgressBarIndeterminateVisibility(false);
            Toast.makeText(ProductsFragment.this.getActivity(), getActivity().getString(R.string.category_load_failed),
                    Toast.LENGTH_SHORT).show();
        }

        @Override
        public void onSuccess(Category.CategoriesResponse response) {
            isLoading = false;
            categories = response.data;

            if (getActivity() == null) {
                return;
            }
            getActivity().setProgressBarIndeterminateVisibility(false);
            Log.d(TAG, "Loaded categories. Count: " + response.data.size());

            if (categories == null) {
                categories = new Category.List();
            } else {
                Category.TreeBuilder treeBuilder = new Category.TreeBuilder(categories);
                categories = treeBuilder.build();
            }
            if (categories.size() == 0) {
                categoryView.setText(getActivity().getString(R.string.no_categories));
            } else {
                selectCategory(categories.get(0).categoryID);
            }
            //updateListViewContent(response);


            //Toast.makeText(ProductsFragment.this.getActivity(), "success", Toast.LENGTH_SHORT).show();
        }
    }

    /**
     * Brings up the category dialog
     */
    protected class OnCategoryClickListener implements View.OnClickListener {
        @Override
        public void onClick(View v) {
            Log.d(TAG, "Clicked category selector");
            if (categories == null) {
                return;
            }

            Log.d(TAG, "Category count: " + categories.size());
            CategorySelectDialogFragment dialog = new CategorySelectDialogFragment();
            Bundle args = new Bundle();
            dialog.setArguments(args);
            dialog.setCategories(categories);
            dialog.setTargetFragment(ProductsFragment.this, 0);
            android.app.FragmentManager fm = getFragmentManager();
            dialog.show(fm, TAG);
        }
    }
}