<?php

namespace App\Model;

class OrderItems extends \PHPixie\ORM\Model {

    public $table = 'tbl_order_items';
    public $id_field = 'id';

    protected $belongs_to=array(

        //Set the name of the relation, this defines the
        //name of the property that you can access this relation with
        'product' => array(

            //name of the model to link
            'model' => 'Product',

            //key in 'fairies' table
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