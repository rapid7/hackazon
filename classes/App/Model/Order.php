<?php

namespace App\Model;


/**
 * Class Order
 * @package App\Model
 * @property OrderAddress $orderAddress
 * @property OrderItems $orderItems
 * @property int $customer_id
 * @property string $customer_firstname
 * @property string $customer_email
 * @property string $created_at
 * @property string $payment_method
 * @property string $shipping_method
 * @property string $status
 * @property int $discount
 */
class Order extends BaseModel
{
    const INCREMENT_BASE = 10000000;

    public $table = 'tbl_orders';
    public $id_field = 'id';

    protected $has_many = array(
        'orderItems' => array(
            'model' => 'orderItems',
            'key' => 'order_id'
        ),
        'orderAddress' => array(
            'model' => 'orderAddress',
            'key' => 'order_id'
        ),
    );

    protected $belongs_to = [
        'customer' => [
            'model' => 'User',
            'key' => 'customer_id'
        ]
    ];

    public function getMyOrders()
    {
        $rows = $this->where('customer_id', $this->pixie->auth->user()->id)->find_all()->as_array();
        return $rows;
    }

    public function getMyOrdersPager($page = 1, $perPage = 10)
    {
        $query = $this->where('customer_id', $this->pixie->auth->user()->id);
        $pager = $this->pixie->paginate->orm($query, $page, $perPage);
        $currentItems = $pager;
        return $currentItems;
    }

    public function get($propertyName)
    {
        if ($propertyName == 'increment_id') {
            return  $this->id() ? $this->id() + self::INCREMENT_BASE : $this->id();
        }
        return null;
    }

    /**
     * @param $incrementId
     * @return mixed|Order
     * @throws \InvalidArgumentException
     */
    public function getByIncrement($incrementId)
    {
        $id = $incrementId - self::INCREMENT_BASE;
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid order number: ' . $incrementId);
        }

        return $this->where('id', $id)->find();
    }

    public static function getOrderStatuses()
    {
        return ['new', 'pending', 'shipped', 'complete'];
    }
}