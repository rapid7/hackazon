<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 20.08.2014
 * Time: 13:01
 */


namespace App\Rest\Controller;


use App\Rest\Controller;

class CustomerAddress extends Controller
{
    protected $modelName = 'CustomerAddress';

    protected function preloadModel()
    {
        $this->model->where('customer_id', '=', $this->getUser()->id());
        parent::preloadModel();
    }
} 