<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 20:02
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;
use App\Model\Category;

class Product extends CRUDController
{
    public $modelNamePlural = 'Products';

    protected function getListFields()
    {
        return [
            'productID' => [
                'title' => 'Id',
                'column_classes' => 'dt-id-column',
                'data_type' => 'integer',
            ],
            'name' => [
                'max_length' => 64,
                'type' => 'link'
            ],
            'category.name' => [
                'title' => 'Category',
                'type' => 'link',
                'template' => '/admin/category/%category.categoryID%',
                'width' => 150
            ],
            'Price' => [
                'value_prefix' => '$',
                'data_type' => 'integer',
            ],
            'picture' => [
                'type' => 'image',
                'dir_path' => '/products_pictures/',
                'max_width' => 40,
                'max_height' => 30,
                'is_link' => true,
                'column_classes' => 'dt-picture-column',
                'title' => 'Pic'
            ]
        ];
    }

    protected function tuneModelForList()
    {
        $this->model->with('category');
    }

    protected function getEditFields()
    {
         $fields = [
            'productID' => [
                'label' => 'Id'
            ],
            'name' => [
                'type' => 'text'
            ],
            'categoryID' => [
                'label' => 'Category',
                'type' => 'select',
                'option_list' => 'App\Admin\Controller\Category::getAvailableCategoryOptions'
            ],
            'description' => [
                'type' => 'textarea'
            ],
            'brief_description' => [
                'type' => 'textarea'
            ],
            'Price' => [
                'label' => 'Price ($)'
            ],
            'product_code' => [

            ],
            'picture' => [
                'type' => 'image',
                'dir_path' => '/products_pictures/'
            ],
            'big_picture' => [
                'type' => 'image',
                'dir_path' => '/products_pictures/'
            ],
            'meta_title' => [
                'type' => 'textarea',
            ],
            'meta_keywords' => [
                'type' => 'textarea',
            ],
            'meta_desc' => [
                'type' => 'textarea',
                'label' => 'Meta Description'
            ],
            'in_stock' => [
                'type' => 'boolean'
            ],
            'enabled' => [
                'type' => 'boolean'
            ]
        ];

        return $fields;
    }
}