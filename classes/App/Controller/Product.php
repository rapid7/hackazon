<?php

namespace App\Controller;

use App\Model\Category as Category;

class Product extends \App\Page {

    public function action_index() {
        $this->view->subview = 'product';
    }

    public function action_view(){
        $category = new Category($this->pixie);
        $productID = $this->request->param('id');

        $this->view->product = $this->model->getProduct($productID);
        $this->view->pageTitle = $this->model->getPageTitle($productID);
        $this->view->sidebar = $category->getRootCategoriesSidebar();
        $this->view->common_path = $this->common_path;
        $this->view->subview = 'product/product';
    }


}