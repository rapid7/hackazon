<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 19:42
 */


namespace App\Admin;


class CRUDController extends Controller
{
    public $modelNamePlural = '';
    public $modelName = '';

    public function before()
    {
        parent::before();

        if (!$this->modelName) {
            $this->modelName = $this->get_real_class($this);;
        }

        if (!$this->modelNamePlural) {
            $this->modelNamePlural = $this->modelName . 's';
        }

        $this->view->pageTitle = $this->modelNamePlural;
        $this->view->pageHeader = $this->modelNamePlural;
    }

    public function action_index()
    {
        $this->view->subview = 'crud/index';
    }
} 