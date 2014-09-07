<?php

namespace App;

use App\Core\BaseController;
use App\Model\Cart;
use App\Model\Category as Category;
use App\Model\Product;
use PHPixie\View;

/**
 * Base controller
 * @inheritdoc
 */
class Page extends BaseController
{
    public function before() {
        parent::before();

        $this->view = $this->pixie->view('main');
        $config = $this->pixie->config->get('parameters');
        $this->view->common_path = $config['common_path'];
        $this->common_path = $config['common_path'];
        $className = $this->get_real_class($this);
        $this->view->returnUrl = '';
        $this->view->controller = $this;

        if (!($className == 'Home' && $this->request->param('action') == 'install')) {
            $category = new Category($this->pixie);
            $this->view->sidebar = $category->getCategoriesSidebar();
            $this->view->search_category = $this->getSearchCategory($className);
            $this->view->search_subcategories = $this->getAllCategories($this->view->sidebar);

            if ($className != "Home") {
                $this->view->categories = $category->getRootCategories();
            }
        }
        $classModel = "App\\Model\\" . $className;
        if (class_exists($classModel)) {
            $this->model = new $classModel($this->pixie);
        } else {
            $this->model = null;
        }
    }

    public function after() {
        $this->response->body = $this->view->render();

        parent::after();
    }

    protected function getSearchCategory($className) {
        switch ($className) {
            case 'Category':
                $category = new Category($this->pixie);
                $search_category = $category->getPageTitle($this->request->param('id'));
                $value = $this->request->param('id');
                break;
            case 'Search':
                $value = $this->request->get("id");
                $category = new Category($this->pixie);
                $search_category = $category->getPageTitle($this->request->get('id'));
				$search_category = ($search_category == "") ? "All" : $search_category;
                break;
            default:
                $search_category = 'All';
                $value = '';
                break;
        }
        return ['value' => $value, 'label' => $search_category];
    }

    protected function getAllCategories($categories) {
        $all_categories = array();
        foreach ($categories as $category) {
            $all_categories[$category->categoryID] = $category->name;
            foreach ($category->childs as $subcategory) {
                $all_categories[$subcategory->categoryID] = $subcategory->name;
            }
        }
        return $all_categories;
    }

    /**
     * @return Product
     */
    protected function getProductsInCart() {
        $cart = $this->getCart();
        return $cart->products->find_all();
    }

    protected function getCart() {
        /** @var Cart $model */
        $model = $this->pixie->orm->get('Cart');
        return $model->getCart();
    }

    protected function getProductsInCartIds() {
        $items = $this->getProductsInCart()->as_array();
        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item->id();
        }
        return $ids;
    }

}
