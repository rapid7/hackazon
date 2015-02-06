<?php

namespace App\Controller;

use App\Page;
use App\SearchFilters\FilterFabric;
use App\Model\Product as ProductModel;
use App\Paginate;
use VulnModule\Config\Annotations as Vuln;

class Search extends Page {

    protected $_products;

    /**
     * @Vuln\Route(name="search")
     * @Vuln\Description("View: search/main.")
     */
    public function action_index() {
        $catId = $this->request->getWrap('id');
        $name = $this->request->getWrap('searchString');
        $brand = $this->request->getWrap('brands');
        $price = $this->request->getWrap('price');
        $quality = $this->request->getWrap('quality');
        $current_page = $this->request->getWrap('page');//$this->request->param('page');
        if(!$current_page->raw()) {
            $current_page->setRaw(1);
        }
        $model = new ProductModel($this->pixie);

        $filterFabric = new FilterFabric($this->pixie, $this->request, $model);
        $filterFabric->addFilter('nameFilter', 'App\SearchFilters\NameFilter', 'searchString');

        $this->_products = $this->pixie->db->query('select')->table('tbl_products');

        if ($catId->raw()) {
            $category = $this->pixie->orm->get('Category')->loadCategory($catId);
			$subCategoriesIds = $category ? $category->getChildrenIDs() : [];
			if(sizeof($subCategoriesIds) > 0) {
				$this->_products
                    ->join('tbl_category_product', array('tbl_category_product.productID', 'tbl_products.productID'), 'left')
					->where("tbl_category_product.categoryID", "IN", $this->pixie->db->expr("(".implode(",", $subCategoriesIds).")"));
			} else {
				$this->_products
                    ->join('tbl_category_product', array('tbl_category_product.productID', 'tbl_products.productID'), 'left')
					->where("tbl_category_product.categoryID", $catId);
			}
        }
        if ($name->raw()) {
            $this->_products
                    ->where('name', 'LIKE', $name->copy('%' . $name->raw() . '%'));
        }

        if (!empty($price)) {
            $pricesVariants = $filterFabric->getFilter("Price")->getVariants();
            $this->_products
                    ->where('Price', '>=', $pricesVariants[$price->raw()][0])->where('Price', '<=', $pricesVariants[$price->raw()][1]);
        }

        if ($brand->raw() && $quality->raw()) {
            $this->_products
                    ->join('tbl_product_options_values', array('tbl_product_options_values.productID', 'tbl_products.productID'), 'left')
                    ->where(array(
                        array("tbl_product_options_values.variantID", $brand),
                        array("tbl_product_options_values.variantID", $quality)
            ));

        } else if ($brand->raw()) {
            $this->_products
                    ->join('tbl_product_options_values', array('tbl_product_options_values.productID', 'tbl_products.productID'), 'left')
                    ->where("tbl_product_options_values.variantID", $brand);

        } else if ($quality->raw()) {
            $this->_products
                    ->join('tbl_product_options_values', array('tbl_product_options_values.productID', 'tbl_products.productID'), 'left')
                    ->where("tbl_product_options_values.variantID", $quality);
        }


        $pager = $this->pixie->paginateDB->db($this->_products, $current_page, 12);

        $pager->set_url_callback(function($page) {
            $catId = $this->request->getWrap("id")->escapeXSS();
            $name = $this->request->getWrap("searchString")->escapeXSS();
            $brands = $this->request->getWrap('brands')->escapeXSS();
            $price = $this->request->getWrap('price')->escapeXSS();
            $quality = $this->request->getWrap('quality')->escapeXSS();
            return "/search/page/?page=$page&id=$catId&searchString=$name&brands=$brands&price=$price&quality=$quality";
        });

        //$label = $filterFabric->getFilter('nameFilter')->getValue();

        if ($this->request->is_ajax()) {
            $view = $this->pixie->view('search/main');

            $view->filterFabric = $filterFabric;
            $view->searchString = $name;
            $view->categoryId = $catId;
            $view->price = $price;
            $view->brand = $brand;
            $view->quality = $quality;

            $view->searchString = $name;
            $view->pageTitle = 'Search by &laquo;' . $name->escapeXSS() . '&raquo;';
            $view->pager = $pager;
            $view->currentItems = $pager->current_items();

            $this->response->body = $view->render();
            $this->execute = false;

        } else {
            $this->view->filterFabric = $filterFabric;
            $this->view->searchString = $name;
            $this->view->categoryId = $catId;
            $this->view->price = $price;
            $this->view->brand = $brand;
            $this->view->quality = $quality;

            $this->view->searchString = $name;
            $this->view->pageTitle = 'Search by &laquo;' . $name->escapeXSS() . '&raquo;';
            $this->view->pager = $pager;
            $this->view->currentItems = $pager->current_items();

            $this->view->subview = 'search/main';
        }
    }
}