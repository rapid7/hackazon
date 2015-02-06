<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 01.10.2014
 * Time: 14:41
 */


namespace App\Rest\Controller;

use App\Rest\Controller;
use VulnModule\Config\Annotations as Vuln;

/**
 * Class Category
 * @package App\Rest\Controller
 * @property \App\Model\Category $item
 * @Vuln\Route("rest")
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

    /**
     * @return \App\Model\BaseModel|null
     * @Vuln\Description("Fetches the category with given ID")
     * @Vuln\Route("rest", params={"action": "get", "id": "_id_"})
     */
    public function action_get()
    {
        return parent::action_get();
    }
} 