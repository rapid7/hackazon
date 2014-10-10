<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 17.09.2014
 * Time: 18:20
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;

class Coupon extends CRUDController
{
    public $modelNamePlural = 'Coupons';
    public $modelName = 'Coupon';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'id' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                ],
                'coupon' => [
                    'type' => 'link',
                    'max_length' => '50',
                    'strip_tags' => true,
                ],
                'discount' => [
                ],
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function getEditFields()
    {
        return [
            'id' => [],
            'coupon' => [
                'required' => true
            ],
            'discount' => [
                'required' => true,
                'default_value' => 0
            ],
        ];
    }

    public function action_edit()
    {
        parent::action_edit();
        if (!$this->execute) {
            return;
        }
    }
}