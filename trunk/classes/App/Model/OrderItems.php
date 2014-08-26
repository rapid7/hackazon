<?php

namespace App\Model;

class OrderItems extends \PHPixie\ORM\Model {

    public $table = 'tbl_order_items';
    public $id_field = 'id';

    protected $belongs_to=array(
        'product' => array(
            'model' => 'Product',
            'key' => 'product_id',
        )
    );

    public function getItemsTotal()
    {
        $total = 0;
        $items = $this->find_all()->as_array();
        foreach ($items as $item) {
            $total += $item->price * $item->qty;
        }
        return $total;
    }
}