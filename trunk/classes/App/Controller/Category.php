<?php

namespace App\Controller;
use App\SearchFilters\FilterFabric;

class Category extends \App\Page
{

    public function action_view()
    {
        $categoryID = $this->request->param('id');
        $category = $this->model->loadCategory($categoryID);
        if ($category instanceof \App\Model\Category) {
            $this->view->pageTitle = $category->name;
            $this->view->filterFabric = new FilterFabric($this->pixie, $this->request, $category->products);
            $childs = $category->nested->children()->find_all()->as_array();
            $this->view->subCategories = $childs;
            $this->view->products = $this->view->filterFabric->getResults();
            $this->view->subview = 'category/category';
            $this->view->breadcrumbs = $this->getBreadcrumbs($category);

        }
    }

    private function getBreadcrumbs(&$category)
    {
        $breadcrumbs = [];
        $parents = $category->parents();
        $breadcrumbs['/'] = 'Home';
        foreach ($parents as $p) {
            $breadcrumbs['/category/view/' . $p->categoryID] = $p->name;
        }
        $breadcrumbs[''] = $category->name;
        return $breadcrumbs;
    }

}