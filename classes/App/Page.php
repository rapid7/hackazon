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
        $this->view->sidebar = $this->getSidebar();


                $className = $this->get_real_class($this);

                
                if($className != "Home"){
                    $model = new \App\Model\Category($this->pixie);
                    $this->view->categories = $model->getRootCategories();
                }
                
                $this->vulninjection = $this->pixie->vulninjection->service(strtolower($className));
                $this->pixie->db->get()->settings($this->vulninjection->getSection("sql"));
                
                $classModel = "App\\Model\\" . $className;
                if(class_exists($classModel) ) {
                    $this->model = new $classModel($this->pixie);
                }else{
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

           if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
               $classname = $matches[1];
           }

           return $classname;
       }

    protected function getSidebar(){
        $category = new Category($this->pixie);
        return $category->getRootCategoriesSidebar();
    }

}
