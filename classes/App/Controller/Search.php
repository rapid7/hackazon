<?php

namespace App\Controller;

use App\Page;
use App\SearchFilters\FilterFabric;
use App\Model\Product as Product;

class Search extends Page {
	public function action_index() {
		$catId = $this->request->get('id');
		$name = $this->request->get('searchString');
		$brand = $this->request->get('brands');
		$price = $this->request->get('price');
		$quality = $this->request->get('quality');
		$current_page = $this->request->param('page');

		$model = new Product($this->pixie);

		$this->view->filterFabric = new FilterFabric($this->pixie, $this->request, $model);
        $this->view->filterFabric->addFilter('nameFilter', 'App\SearchFilters\NameFilter', 'searchString');
		
		$this->_products = $this->pixie->db->query('select')->table('tbl_products');

		if (!empty($catId)) {
			$this->_products
				->join('tbl_category_product',array('tbl_category_product.productID','tbl_products.productID'),'left')
				->where("tbl_category_product.categoryID", $catId);
			$cat = $this->pixie->orm->get('Category')->loadCategory($catId);
			$this->view->search_category['label'] = $catId;
			$this->view->search_category['value'] = $catId;
		}
		if (!empty($name)) {
			$this->_products
				->where('name', 'LIKE', '%'.$name.'%');
		}
		if (!empty($price)) {
			$pricesVariants = $this->view->filterFabric->getFilter("Price")->getVariants();
			$this->_products
				->where('Price', '>=', $pricesVariants[$price][0])->where('Price', '<=', $pricesVariants[$price][1]);
		}
		if (!empty($brand) && !empty($quality)) {
			$this->_products
				->join('tbl_product_options_values',array('tbl_product_options_values.productID','tbl_products.productID'),'left')
				->where(array(
					array("tbl_product_options_values.variantID", $brand),
					array("tbl_product_options_values.variantID", $quality)
				));
		} else if(!empty($brand)) {
			$this->_products
				->join('tbl_product_options_values',array('tbl_product_options_values.productID','tbl_products.productID'),'left')
				->where("tbl_product_options_values.variantID", $brand);
		}else if (!empty($quality)) {
			$this->_products
				->join('tbl_product_options_values',array('tbl_product_options_values.productID','tbl_products.productID'),'left')
				->where("tbl_product_options_values.variantID", $quality);
		}
		

		$pager = $this->pixie->paginate->db($this->_products, $current_page, 12);
		$pager->set_url_callback(function($page){
			$catId = $this->request->get("id");
			$name = $this->request->get("searchString");
			$brand = $this->request->get('brands');
			$price = $this->request->get('price');
			$quality = $this->request->get('quality');

				return "/search/page-$page?id=$catId&searchString=$name&brands=$brands&price=$price&quality=$quality";
		});
		
 
		
		//$pager->set_url_pattern("/page-#page#");
		
 		$this->view->pager = $pager;
		
		$label = $this->view->filterFabric->getFilter('nameFilter')->getValue();

		$this->view->searchString = $name;
		$this->view->categoryId = $catId;
		$this->view->price = $price;
		$this->view->brand = $brand;
		$this->view->quality = $quality;
		
		$this->view->searchString = is_null($label) ? '' : $label;
		$this->view->pageTitle = 'Search by %' . $this->view->searchString . '%';
        $this->view->subview = 'search/main';
		
		
	}

    /*public function action_index() {
		$catId = $this->request->get('id');
		$brandIds = $this->request->get('brands');
		$priceIds = $this->request->get('brands');
		$qualityIds = $this->request->get('brands');
        if (!empty($catId)) {
            $cat = $this->pixie->orm->get('Category')->loadCategory($catId);
            $model = $cat->products;
        } else {
            $model = new Product($this->pixie);
        }
		
        $this->view->filterFabric = new FilterFabric($this->pixie, $this->request, $model);
        $this->view->filterFabric->addFilter('nameFilter', 'App\SearchFilters\NameFilter', 'searchString');
        $this->view->filterFabric->addFilter('brandFilter', 'App\SearchFilters\BrandFilter', 'brands');
        $this->view->filterFabric->addFilter('priceFilter', 'App\SearchFilters\PriceFilter', 'price');
		$this->view->filterFabric->addFilter('qualityFilter', 'App\SearchFilters\QualityFilter', 'quality');

        $this->view->products = $this->view->filterFabric->getResults();
        $label = $this->view->filterFabric->getFilter('nameFilter')->getValue();

        $this->view->searchString = is_null($label) ? '' : $label;
        $this->view->pageTitle = 'Search by %' . $this->view->searchString . '%';
        $this->view->subview = 'search/main';
    }*/

}