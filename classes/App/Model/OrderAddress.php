<?php

namespace App\Model;

class OrderAddress extends \PHPixie\ORM\Model {

    public $table = 'tbl_order_address';
    public $id_field = 'id';

    public function getShippingAddress()
    {
        $addresses = $this->find_all()->as_array();
        foreach ($addresses as $address) {
            if ($address->address_type == 'shipping') {
                return $address;
            }
        }
        return array();
    }

    public function getBillingAddress()
    {
        $addresses = $this->find_all()->as_array();
        foreach ($addresses as $address) {
            if ($address->address_type == 'billing') {
                return $address;
            }
        }
        return array();
    }
}