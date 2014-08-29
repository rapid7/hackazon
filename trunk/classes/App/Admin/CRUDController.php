<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 19:42
 */


namespace App\Admin;

/**
 * Controller which provides basic CRUD features for Model instances.
 * For detailed tuning of features, just derive fom it and override methods.
 * @package App\Admin
 */
class CRUDController extends Controller
{
    /**
     * @var string Plural name of the model to be shown in UI.
     */
    public $modelNamePlural = '';

    /**
     * @var string Singular name of the model
     */
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

    /**
     * List items.
     */
    public function action_index()
    {
        $this->view->subview = 'crud/index';
    }

    /**
     * Shows single item
     */
    public function action_show()
    {
        $this->view->subview = 'crud/show';
    }

    /**
     * Edit existing item
     */
    public function action_edit()
    {

    }

    /**
     * Create new item
     */
    public function action_new()
    {

    }
} 