<?php

namespace App\Controller;

use App\Page;
use App\SearchFilters\FilterFabric;
use App\Model\Product as Product;

class Search extends Page
{
    public function action_index()
    {
        $catId = $this->request->get('id');
        if (!empty($catId)) {
            $cat = $this->pixie->orm->get('Category')->loadCategory($catId);
            $model = $cat->products;
        } else {
            $model = new Product($this->pixie);
        }
        $this->view->filterFabric = new FilterFabric($this->pixie, $this->request, $model);
        $this->view->filterFabric->addFilter('nameFilter', 'App\SearchFilters\NameFilter', 'searchString');
        $this->view->products = $this->view->filterFabric->getResults();
        $label = $this->view->filterFabric->getFilter('nameFilter')->getValue();
        $this->view->searchString = is_null($label) ? '' : $label;
        $this->view->pageTitle = 'Search by %' . $this->view->searchString . '%';
        $this->view->subview = 'search/main';
    }
}