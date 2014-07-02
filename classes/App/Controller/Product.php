<?php

namespace App\Controller;

class Product extends \App\Page {

    public function action_index() {
        $this->view->subview = 'product';
    }

    public function action_view(){
        $productID = $this->request->param('id');

        $this->view->product = $this->model->getProduct($productID);
        $this->view->pageTitle = $this->model->getPageTitle($productID);
        $this->view->subview = 'product/product';
    }


}