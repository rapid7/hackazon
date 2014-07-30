<?php

namespace App;

use App\Model\Category as Category;

/**
 * Base controller
 *
 * @property-read \App\Pixie $pixie Pixie dependency container
 */
class Page extends \PHPixie\Controller {

    protected $view;
    protected $common_path;
    protected $model;
    protected $vulninjection;
    protected $errorMessage;

    public function before() {
        $this->view = $this->pixie->view('main');
        $config = $this->pixie->config->get('page');
        $this->view->common_path = $config['common_path'];
        $this->common_path = $config['common_path'];
        $className = $this->get_real_class($this);

        if (!($className == 'Home' && $this->request->param('action') == 'install')) {
            $category = new Category($this->pixie);
            $this->view->sidebar = $category->getCategoryTreeArray();
            //$this->view->sidebar = $this->getSidebar();


            $this->view->search_category = $this->getSearchCategory($className);
            $this->view->search_subcategories = $this->getAllCategories($this->view->sidebar);

            if ($className != "Home") {
                $model = new \App\Model\Category($this->pixie);
                $this->view->categories = $model->getRootCategories();
            }
        }

        $this->vulninjection = $this->pixie->vulninjection->service(strtolower($className));
        //$this->pixie->db->get()->settings($this->vulninjection->getSection("sql"));

        $classModel = "App\\Model\\" . $className;
        if (class_exists($classModel)) {
            $this->model = new $classModel($this->pixie);
        } else {
            $this->model = null;
        }
    }

    public function after() {
        $this->response->body = $this->view->render();
    }

    /**
     * Obtains an object class name without namespaces
     */
    public function get_real_class($obj) {
        $classname = get_class($obj);

        if (preg_match('@\\\\(?<class_name>[\w]+)$@', $classname, $matches)) {
            $classname = $matches['class_name'];
        }

        return $classname;
    }

    protected function getSidebar() {
        $category = new Category($this->pixie);
        return $category->getRootCategoriesSidebar();
    }

    protected function getSearchCategory($className) {
        switch ($className) {
            case 'Category':
                $category = new Category($this->pixie);
                $search_category = $category->getPageTitle($this->request->param('id'));
                break;
            default:
                $search_category = 'All';
        }
        return $search_category;
    }

    protected function getAllCategories($categories) {
        $all_categories = array();
        foreach ($categories as $category) {
            $all_categories[] = $category['name'];
            foreach ($category['child'] as $subcategory) {
                $all_categories[] = $subcategory['name'];
            }
        }
        sort($all_categories);
        return $all_categories;
    }

}
