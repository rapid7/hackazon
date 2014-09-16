<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 20:01
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;

class Role extends CRUDController
{
    public $modelNamePlural = 'Roles';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'id' => [
                    'column_classes' => 'dt-id-column',
                ],
                'name' => [
                    'title' => 'Role',
                    'type' => 'link'
                ]
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function getEditFields()
    {
        return [
            'id',
            'name' => [
                'required' => true
            ]
        ];
    }

    /**
     * @param $value
     * @param null|\App\Model\Role $item
     * @param array $format
     * @return string|void
     */
    public function fieldFormatter($value, $item = null, array $format = [])
    {
        if ($format['extra'] && in_array($format['original_field_name'], ['delete']) && !$item->removable) {
            return '';
        } else {
            return parent::fieldFormatter($value, $item, $format);
        }
    }
} 