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
use App\Model\User;
use PHPixie\DB\PDOV\Connection;
use Symfony\Component\Form\FormFactory;


class Controller extends BaseController
{
    /**
     * @var View
     */
    public $view;
    public $common_path;

    public $root = '/admin';

    /**
     * @var User
     */
    protected $user;

    public function before()
    {
        parent::before();

        $user = $this->pixie->auth->user();
        $this->user = $user;

        $this->view = $this->view('main');
        $config = $this->pixie->config->get('parameters');
        $this->common_path = $config['common_path'];
        $this->view->common_path = $config['common_path'];
        $this->view->returnUrl = '';
        $this->view->controller = $this;
        $this->view->adminRoot = $this->root;
        $className = isset($this->modelName) && $this->modelName ? $this->modelName : $this->get_real_class($this);
        $this->view->sidebarLinks = $this->getSidebarLinks();

        $this->view->user = $user;
        $this->view->pageHeader = 'Dashboard';

        try {
            /** @var Connection $pdov */
            $this->pixie->db->get();
            
        } catch (\Exception $e) {
            $this->redirect('/install');
            return;
        }
        
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

    public function getSidebarLinks()
    {
        return [
            $this->root => ['label' => 'Dashboard', 'link_class' => 'fa fa-dashboard fa-fw'],
            $this->root.'/user' => ['label' => 'Users', 'link_class' => 'fa fa-user fa-fw'],
            $this->root.'/role' => ['label' => 'Roles', 'link_class' => 'fa fa-puzzle-piece fa-fw'],
            $this->root.'/category' => ['label' => 'Product Categories', 'link_class' => 'fa fa-sitemap fa-fw'],
            $this->root.'/product' => ['label' => 'Products', 'link_class' => 'fa fa-archive fa-fw'],
            $this->root.'/option' => ['label' => 'Product Options', 'link_class' => 'fa fa-check-circle-o fa-fw'],
            $this->root.'/order' => ['label' => 'Orders', 'link_class' => 'fa fa-shopping-cart fa-fw'],
            $this->root.'/coupon' => ['label' => 'Coupons', 'link_class' => 'fa fa-percent fa-fw'],
            $this->root.'/enquiry' => ['label' => 'Enquiries', 'link_class' => 'fa fa-life-saver fa-fw'],
            $this->root.'/faq' => ['label' => 'Faq', 'link_class' => 'fa fa-question-circle fa-fw'],
            $this->root.'/vulnerability' => ['label' => 'Vulnerability Config', 'link_class' => 'fa fa-question-circle fa-fw'],
            //$this->root.'/vulnerability/test' => ['label' => 'Vulnerability Calculator', 'link_class' => 'fa fa-question-circle fa-fw'],
            //$this->root.'/vulnerability/matrix' => ['label' => 'Vulnerability Matrix', 'link_class' => 'fa fa-th fa-fw'],
        ];
    }

    /**
     * @return FormFactory
     */
    public function getFormFactory()
    {
        return $this->pixie->container['form.factory'];
    }
}