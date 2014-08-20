<?php

namespace App\Controller;

use \App\Model\SpecialOffers;
use \App\Model\Category;

/**
 * Class Product
 * @property \App\Model\Product model
 * @package App\Controller
 */
class Product extends \App\Page
{

    public function action_index()
    {
        $this->view->subview = 'product/index';
    }

    public function action_view()
    {
        $productID = $this->request->param('id');
        $this->view->product = $this->model->where('productID', '=', $productID)->find();
        if ($this->view->product->loaded()) {
            $this->view->options = $this->view->product->options->find_all()->as_array();
            $this->view->pageTitle = $this->model->getPageTitle($productID);
            $this->view->breadcrumbs = $this->getBreadcrumbs();
            $offers = new SpecialOffers($this->pixie);
            $this->view->special_offers = $offers->getRandomOffers(5);
            $this->view->related = $this->model->getRandomProducts(4);
            $this->model->checkProductInCookie($productID);
        }
        $this->view->subview = 'product/product';
    }

    private function getBreadcrumbs()
    {
        $categories = $this->view->product->categories->find_all();
        $breadcrumbs = [];
        foreach ($categories as $cat) {
            $parents = $cat->parents();
            $breadcrumbsParts = [];
            foreach ($parents as $p) {
                $breadcrumbsParts['/category/view/'.$p->categoryID] = $p->name;
            }
            $breadcrumbsParts['/product/view/'.$this->view->product->productID] = $this->view->product->name;
            $breadcrumbs[] = array_merge(['/' => 'Home'], $breadcrumbsParts);
        }
        return $breadcrumbs;
    }
}