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
        $this->region = $post['region'];
        $this->zip = $post['zip'];
        $this->country_id = $post['country_id'];
        $this->phone = $post['phone'];
        $this->customer_id = $this->pixie->auth->user()->id;
        $this->save();
        return $this->id;
    }

    public function getAll()
    {
        return $this->where('customer_id', $this->pixie->auth->user()->id)->find_all()->as_array();
    }

    public function getById($addressId)
    {
        $address = $this->where(
            'and', array(
            array('id', '=', $addressId),
            array('customer_id', '=', $this->pixie->auth->user()->id)
        ))->find();
        if ($address->loaded()) {
            return $address->as_array();
        } else {
            return array();
        }
    }

    public function deleteById($addressId)
    {
        $address = $this->where(
            'and', array(
            array('id', '=', $addressId),
            array('customer_id', '=', $this->pixie->auth->user()->id)
        ))->find();
        if ($address->loaded()) {
            $address->delete();
        }
    }
}