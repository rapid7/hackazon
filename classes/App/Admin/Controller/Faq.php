<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 20:01
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;

class Faq extends CRUDController
{
    public $modelNamePlural = 'Enquiries';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'faqID' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                ],
                'question' => [
                    'type' => 'link',
                    'max_length' => '80',
                    'strip_tags' => true,
                ],
                'answer' => [
                    'max_length' => '80',
                    'strip_tags' => true,
                ],
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function getEditFields()
    {
        return [
            'faqID' => [],
            'question' => [
                'required' => true
            ],
            'answer' => [
                'type' => 'textarea',
                'required' => true
            ],
            'email'
        ];
    }
} 