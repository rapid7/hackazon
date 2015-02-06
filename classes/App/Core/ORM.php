<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 26.01.2015
 * Time: 21:44
 */


namespace App\Core;


use PHPixie\ORM\Model;
use VulnModule\VulnerableField;

class ORM extends \PHPixie\ORM
{
    public function get($name, $id = null)
    {
        $name = explode('_', $name);
        $name = array_map('ucfirst', $name);
        $name = implode("\\", $name);
        $model = $this->pixie->app_namespace."Model\\".$name;
        /** @var Model $model */
        $model = new $model($this->pixie);
        if ($id != null)
        {
            $model = $model->where($model->id_field, $id)->find();
            $model->values(array($model->id_field => $id instanceof VulnerableField ? $id->raw() : $id));
        }
        return $model;
    }
}