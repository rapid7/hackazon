<?php

namespace App\Model;

/**
 * Class OrderAddress
 * @package App\Model
 * @property int $id
 * @property int $country_id
 * @property int $customer_id
 * @property int $order_id
 * @property string $full_name
 * @property string $address_line_1
 * @property string $address_line_2
 * @property string $city
 * @property string $region
 * @property string $zip
 * @property string $phone
 * @property string $address_type
 */
class OrderAddress extends BaseModel {

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