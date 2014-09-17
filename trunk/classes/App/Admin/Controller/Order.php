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
use App\Pixie;

class Order extends CRUDController
{
    public $modelNamePlural = 'Orders';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'id' => [
                    'column_classes' => 'dt-id-column',
                ],
                'customer_firstname' => [
                    'title' => 'First Name',
                ],
                'customer_lastname' => [
                    'title' => 'Last Name',
                ],
                'customer_email' => [
                    'title' => 'Email',
                ],
                'customer.username' => [
                    'title' => 'Customer Username',
                    'is_link' => true,
                    'template' => '/admin/user/edit/%customer.id%'
                ],
                'status' => [
                    'type' => 'status'
                ],
                'payment_method' => [],
                'shipping_method' => []
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function getEditFields()
    {
        return [
            'id' => [],
            'status' => [
                'type' => 'select',
                'option_list' => ArraysHelper::arrayFillEqualPairs(\App\Model\Order::getOrderStatuses()),
                'required' => true
            ],
            'customer_id' => [
                'label' => 'Customer',
                'type' => 'select',
                'option_list' => 'App\Admin\Controller\User::getAvailableUsers',
                'required' => true
            ],
            'customer_firstname',
            'customer_lastname',
            'customer_email',
            'payment_method' => [
                'required' => true
            ],
            'shipping_method' => [
                'required' => true
            ],
            'comment' => [
                'type' => 'textarea'
            ],
            'created_at' => [
                'data_type' => 'date'
            ],
            'updated_at' => [
                'data_type' => 'date'
            ],
        ];
    }

    protected function tuneModelForList()
    {
        $this->model->with('customer');
    }

    public function action_edit()
    {
        parent::action_edit();
        /** @var \App\Model\Order $order */
        $order = $this->view->item;
        $this->view->order = $order;
        $this->view->orderItems = $order->orderItems->find_all()->as_array();
        if ($order->id()) {
            $this->view->subview = 'order/edit';
        }
    }
}