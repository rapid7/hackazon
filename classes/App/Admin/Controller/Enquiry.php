<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 20:01
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;
use App\Helpers\ArraysHelper;

class Enquiry extends CRUDController
{
    public $modelNamePlural = 'Enquiries';

    protected function getListFields()
    {
        return [
            'id' => [
                'column_classes' => 'dt-id-column',
            ],
            'title' => [
                'type' => 'link'
            ],
            'status',
            'creator.username' => [
                'is_link' => true,
                'title' => 'Created By',
                'template' => '/admin/user/%creator.id%'
            ],
            'assignee.username' => [
                'is_link' => true,
                'title' => 'Assigned To',
                'template' => '/admin/user/%assignee.id%'
            ]
        ];
    }

    protected function tuneModelForList()
    {
        $this->model->with('creator', 'assignee');
    }

    protected function getEditFields()
    {
        return [
            'id' => [],
            'created_by' => [
                'type' => 'select',
                'option_list' => 'App\Admin\Controller\User::getAvailableUsers',
            ],
            'assigned_to' => [
                'type' => 'select',
                'option_list' => 'App\Admin\Controller\User::getAvailableUsers',
            ],
            'title',
            'description' => [
                'type' => 'textarea'
            ],
            'status',
            'created_on',
            'updated_on',
        ];
    }

    public function fieldFormatter($value, $item = null, array $format = [])
    {
        $value2 = parent::fieldFormatter($value, $item, $format);
        if ($format['original_field_name'] == 'status') {
            $value2 = '<span class="label label-'.strtolower($value2).'">'.$value.'</span>';
        }

        return $value2;
    }
} 