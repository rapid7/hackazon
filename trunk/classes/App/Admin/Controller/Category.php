<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 20:02
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;

class Category extends CRUDController
{
    public $modelNamePlural = 'Categories';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'categoryID' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                ],
                'name' => [
                    'max_length' => 64,
                    'type' => 'link',
                ],
                'parentCategory.name' => [
                    'is_link' => true,
                    'template' => '/admin/category/%parentCategory.categoryID%',
                    'title' => 'Parent'
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'column_classes' => 'dt-flag-column',
                    'title' => '+'
                ]
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function tuneModelForList()
    {
        $this->model->with('parentCategory')->where('categoryID', '<>', 1);
    }

    public function fieldFormatter($value, $item = null, array $format = [])
    {
        if ($format['original_field_name'] == 'parentCategory.name' && $value == '0_ROOT') {
            $value = '';
        }
        return parent::fieldFormatter($value, $item, $format);
    }

    protected function getEditFields()
    {
        return [
            'categoryID' => [
                'label' => 'Id'
            ],
            'name' => [
                'type' => 'text',
                'required' => true
            ],
            'parent' => [
                'label' => 'Category',
                'type' => 'select',
                'option_list' => [$this, 'getAvailableCategoryOptions']
            ],
            'description' => [
                'type' => 'textarea'
            ],
            'enabled' => [
                'type' => 'boolean'
            ],
            'hidden' => [
                'type' => 'boolean'
            ],
            'picture' => [
                'type' => 'image',
                'dir_path' => '/products_pictures/',
                'abs_path' => false
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
        ];
    }

    public static function getAvailableCategoryOptions($pixie)
    {
        $results = [];
        /** @var \App\Model\Category $categoryModel */
        $categoryModel = $pixie->orm->get('category');
        /** @var \App\Model\Category[] $items */
        $items = $categoryModel->with('parentCategory')
            ->where('depth', 2)
            ->order_by('parentCategory.name')->order_by('name', 'asc')->order_by('lpos', 'asc')
            ->find_all();
        foreach ($items as $item) {
            $results[$item->id()] = $item->parentCategory->name . ' / ' . $item->name;
        }
        return $results;
    }
} 