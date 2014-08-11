<?php

namespace App\Controller;

class Category extends \App\Page
{

    public function action_view()
    {
        $categoryID = $this->request->param('id');
        $category = $this->model->loadCategory($categoryID);
        if ($category instanceof \App\Model\Category) {
            $this->view->pageTitle = $category->name;
            $childs = $category->children->find_all()->as_array();
            if (count($childs) > 0) {
                $this->view->subCategories = $childs;
                $this->view->productPage = false;
            } else {
                $this->view->products = $category->products->find_all()->as_array();
                $this->view->productPage = true;
            }
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