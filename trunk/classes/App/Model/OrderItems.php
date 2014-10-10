<?php

namespace App\Model;

class OrderItems extends BaseModel {

    public $table = 'tbl_order_items';
    public $id_field = 'id';

    protected $belongs_to=array(
        'product' => array(
            'model' => 'Product',
            'key' => 'product_id',
        ),
        'order' => array(
            'model' => 'Order',
            'key' => 'order_id',
        )
    );

    public function getItemsTotal()
    {
        $total = 0;
        $items = $this->find_all()->as_array();
        $order = $items[0] ? $items[0]->order : null;
        $multiplier = (100 - (int)($order ? $order->discount : 0)) / 100;
        foreach ($items as $item) {
            $total += $item->price * $item->qty;
        }
        return $total * $multiplier;
    }
}