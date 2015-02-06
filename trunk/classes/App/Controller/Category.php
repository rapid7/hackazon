<?php

namespace App\Controller;


use App\Exception\NotFoundException;
use App\Page;
use App\SearchFilters\FilterFabric;
use VulnModule\Config\Annotations as Vuln;

/**
 * Class Category
 * @package App\Controller
 * @property \App\Model\Category $model
 * @Vuln\Description("Controller for category handling.")
 * @Vuln\Description("Controller for category handling 21.")
 */
class Category extends Page
{
    /**
     * @throws NotFoundException
     * @throws \Exception
     * @Vuln\Description("View: category/category.")
     */
    public function action_view()
    {
        $categoryID = $this->request->getWrap('id');

        if (!$categoryID->raw()) {
            throw new NotFoundException();
        }

        $category = $this->model->loadCategory($categoryID);

        if ($category instanceof \App\Model\Category && $category->parent) {
            $this->view->pageTitle = $category->name;
            $filterFabric = new FilterFabric($this->pixie, $this->request, $category->products);
            $this->view->filterFabric = $filterFabric;
            $children = $category->nested->children()->find_all()->as_array();
            $this->view->subCategories = $children;
            $page = $this->request->getWrap('page', 1);
            $pager = $filterFabric->getResultsPager($page, 12);
            $this->view->products = $pager->current_items()->as_array();
            $this->view->pager = $pager;
            $this->view->subview = 'category/category';
            $this->view->breadcrumbs = $this->getBreadcrumbs($category);
            $this->view->categoryID = $categoryID;

        } else {
            throw new NotFoundException("No such category");
        }
    }

    /**
     * @param \App\Model\Category $category
     * @return array
     */
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