<?php

namespace App\Controller;
use App\Exception\NotFoundException;
use App\Page;
use App\SearchFilters\FilterFabric;

/**
 * Class Category
 * @package App\Controller
 * @property \App\Model\Category $model
 */
class Category extends Page
{

    public function action_view()
    {
        $categoryID = $this->request->get('id');//$this->request->param('id');
        if (!$categoryID) {
            throw new NotFoundException();
        }

        $category = $this->model->loadCategory($categoryID);
        if ($category instanceof \App\Model\Category) {
            $this->view->pageTitle = $category->name;
            $filterFabric = new FilterFabric($this->pixie, $this->request, $category->products);
            $this->view->filterFabric = $filterFabric;
            $children = $category->nested->children()->find_all()->as_array();
            $this->view->subCategories = $children;
            $page = $this->request->get('page', 1);
            $pager = $filterFabric->getResultsPager($page, 12);
            $this->view->products = $pager->current_items()->as_array();
            $this->view->pager = $pager;
            $this->view->subview = 'category/category';
            $this->view->breadcrumbs = $this->getBreadcrumbs($category);
            $this->view->categoryID = $categoryID;
        }
    }

    private function getBreadcrumbs(&$category)
    {
        $breadcrumbs = [];
        $parents = $category->parents();
        $breadcrumbs['/'] = 'Home';
        foreach ($parents as $p) {
            $breadcrumbs['/category/view?id=' . $p->categoryID] = $p->name;
        }
        $breadcrumbs[''] = $category->name;
        return $breadcrumbs;
    }

}