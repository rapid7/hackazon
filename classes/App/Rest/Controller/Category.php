<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 01.10.2014
 * Time: 14:41
 */


namespace App\Rest\Controller;

use App\Rest\Controller;

/**
 * Class Category
 * @package App\Rest\Controller
 * @property \App\Model\Category $item
 */
class Category extends Controller
{
    protected function preloadModel()
    {
        parent::preloadModel();
        if ($this->model && !$this->request->param('id')) {
            $this->model->order_by('name', 'asc'); //->order_by('depth', 'asc')
        }
    }
} 