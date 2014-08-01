<?php

namespace App\Controller;

use \App\Model\SpecialOffers;
/**
 * Class Product
 * @property \App\Model\Product model
 * @package App\Controller
 */
class Product extends \App\Page {

    public function action_index() {
        $this->view->subview = 'product';
    }

    public function action_view(){
        $productID = $this->request->param('id');

        $this->view->product = $this->model->getProduct($productID);
        $this->view->productObj = $this->model->where('productID', '=', $productID)->find_all()->current();
        $this->view->pageTitle = $this->model->getPageTitle($productID);
        $offers = new SpecialOffers($this->pixie);
        $this->view->special_offers = $offers->getRandomOffers(5);
        $this->view->related = $this->model->getRandomProducts(4);
        $this->model->checkProductInCookie($productID);

        $this->view->subview = 'product/product';
    }
}