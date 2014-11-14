package com.ntobjectives.hackazon.dialog;

import android.app.AlertDialog;
import android.app.Dialog;
import android.app.DialogFragment;
import android.content.DialogInterface;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.util.Log;
import com.ntobjectives.hackazon.R;
import com.ntobjectives.hackazon.activity.AbstractRootActivity;
import com.ntobjectives.hackazon.activity.ProductsFragment;
import com.ntobjectives.hackazon.adapter.CategoryListAdapter;
import com.ntobjectives.hackazon.model.Category;

/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov
 * Date: 27.10.2014
 * Time: 21:38
 */
public class CategorySelectDialogFragment extends DialogFragment {
    protected static final String TAG = CategorySelectDialogFragment.class.getSimpleName();
    protected Category.List categories;

    public CategorySelectDialogFragment() {
        categories = new Category.List();
    }

    public void setCategories(Category.List categories) {
        this.categories = categories;
    }

    @NonNull
    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        AbstractRootActivity activity = (AbstractRootActivity) getActivity();

        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        Log.d(TAG, getActivity().getClass().getSimpleName());
        Log.d(TAG, Boolean.toString(categories == null));
        final CategoryListAdapter adapter = new CategoryListAdapter(getActivity(), activity.getSpiceManagerBinary(), categories);

        builder.setTitle(getActivity().getString(R.string.select_category))
                .setAdapter(adapter, new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        Log.d(TAG, "Selected category №" + which);
                        ((ProductsFragment)getTargetFragment()).selectCategory(adapter.getItem(which).categoryID);
                    }
                });

        return builder.create();
    }
}
