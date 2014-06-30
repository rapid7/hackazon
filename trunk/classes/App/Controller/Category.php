<?php

namespace App\Controller;

use App\Model\Product as Product;

class Category extends \App\Page {

    public function action_view() {
        $product = new Product($this->pixie);
        $categoryID = $this->request->param('id');

        $this->view->pageTitle = $this->model->getPageTitle($categoryID);
        $this->view->sidebar = $this->model->getRootCategoriesSidebar();
        $this->view->productPage = true;
        if($this->model->checkCategoryChild($categoryID)){
            $this->view->subCategories = $this->model->getSubCategories($categoryID);
            $this->view->productPage = false;
        }
        else{
            $this->view->products = $product->getProductsCategory($categoryID);
        }


        $this->view->common_path = $this->common_path;
        $this->view->subview = 'category/category';
    }

}