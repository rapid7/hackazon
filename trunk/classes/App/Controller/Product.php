<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use \App\Model\SpecialOffers;
use App\Page;
use VulnModule\Config\Annotations as Vuln;

/**
 * Class Product
 * @property \App\Model\Product model
 * @package App\Controller
 */
class Product extends Page
{
    /**
     * @throws NotFoundException
     * @Vuln\Description("View: product/product.")
     */
    public function action_view()
    {
        $productID = $this->request->getWrap('id');

        if (!$productID->getFilteredValue()) {
            throw new NotFoundException("Missing product id.");
        }
        /** @var \App\Model\Product $product */
        $product = $this->model->where('productID', '=', $productID)->find();

        if (!$product || !$product->loaded()) {
            throw new NotFoundException("Invalid product id"); //: " . $productID->escapeXSS());
        }

        $this->view->product = $product;
        $this->view->options = $this->view->product->options->find_all()->as_array();
        $this->view->pageTitle = $this->model->getPageTitle($productID);
        $this->view->breadcrumbs = $this->getBreadcrumbs($product);
        $offers = new SpecialOffers($this->pixie);
        $this->view->special_offers = $offers->getRandomOffers(4);
        $this->view->related = $this->model->getRandomProducts(4);
        $this->model->checkProductInCookie($productID);
        $this->view->subview = 'product/product';
    }

    private function getBreadcrumbs(\App\Model\Product $product)
    {
        /** @var \App\Model\Category[] $categories */
        $categories = $product->categories->find_all();
        $breadcrumbs = [];
        foreach ($categories as $cat) {
            $parents = $cat->parents();
            $breadcrumbsParts = [];
            foreach ($parents as $p) {
                $breadcrumbsParts['/category/view?id='.$p->categoryID] = $p->name;
            }
			$breadcrumbsParts['/category/view?id='.$cat->categoryID] = $cat->name;
            $breadcrumbsParts['/product/view?id='.$product->productID] = $product->name;
            $breadcrumbs[] = array_merge(['/' => 'Home'], $breadcrumbsParts);
        }
        return $breadcrumbs;
    }
}