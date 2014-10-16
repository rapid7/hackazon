<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 17.09.2014
 * Time: 18:20
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;

class Option extends CRUDController
{
    public $modelNamePlural = 'Product Options';
    public $modelNameSingle = 'Product Option';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'optionID' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                ],
                'name' => [
                    'type' => 'link',
                    'max_length' => '50',
                    'strip_tags' => true,
                ],
                'sort_order' => [
                ],
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function getEditFields()
    {
        return [
            'optionID' => [],
            'name' => [
                'required' => true
            ],
            'sort_order' => [
            ]
        ];
    }

    public function action_edit()
    {
        parent::action_edit();
        if (!$this->execute) {
            return;
        }

        /** @var \App\Model\Option $option */
        $option = $this->view->item;
        if ($option->id()) {
            //$this->view->enquiryMessages = $option->messages->with('author')->find_all()->as_array();
            $this->view->subview = 'option/edit';
            $this->view->pageHeader = "Product Option &laquo;" . $option->name . "&raquo;";
        }
    }
}