<?php

namespace App\Model;

class CustomerAddress extends \PHPixie\ORM\Model {

    public $table = 'tbl_customer_address';
    public $id_field = 'id';

    public function create($post)
    {
        $this->full_name = $post['fullName'];
        $this->address_line_1 = $post['addressLine1'];
        $this->address_line_2 = $post['addressLine2'];
        $this->city = $post['city'];
        $this->region = $post['state'];
        $this->zip = $post['zip'];
        $this->country_id = $post['country'];
        $this->phone = $post['phone'];
        $this->customer_id = $this->pixie->auth->user()->id;
        $this->save();
    }

    public function getAll()
    {
        return $this->where('customer_id', $this->pixie->auth->user()->id)->find_all()->as_array();
    }
}