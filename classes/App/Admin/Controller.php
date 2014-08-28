<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 13:40
 */


namespace App\Admin;


use App\Core\BaseController;
use App\Core\View;
use App\Exception\HttpException;


class Controller extends BaseController
{
    /**
     * @var View
     */
    public $view;
    public $common_path;
    protected $user;

    public function before()
    {
        parent::before();

        $user = $this->pixie->auth->user();
        $this->user = $user;

        $this->view = $this->view('main');
        $config = $this->pixie->config->get('page');
        $this->common_path = $config['common_path'];
        $this->view->common_path = $config['common_path'];
        $this->view->returnUrl = '';
        $this->view->controller = $this;
        $this->view->adminRoot = '/admin';
        $className = $this->get_real_class($this);

        $this->view->user = $user;
        $this->view->pageHeader = 'Dashboard';

        $classModel = "App\\Model\\" . $className;
        if (class_exists($classModel)) {
            $this->model = new $classModel($this->pixie);
        } else {
            $this->model = null;
        }
    }

    public function after()
    {
        $this->response->body = $this->view->render();

        parent::after();
    }

    public function view($name, $group = 'admin')
    {
        return $this->pixie->view(($group ? $group . '/' : '') . $name);
    }

    public function run($action)
    {
        try {
            parent::run($action);
        } catch (HttpException $e) {
            $e->setOrigin(HttpException::ORIGIN_ADMIN);
            throw $e;
        }
    }
}