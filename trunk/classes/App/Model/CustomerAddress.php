<?php

namespace App\Model;

/**
 * Class CustomerAddress
 * @package App\Model
 * @property string $full_name
 * @property string $address_line_1
 * @property string $address_line_2
 * @property string $city
 * @property string $region
 * @property string $zip
 * @property int $country_id
 * @property string $phone
 * @property int $customer_id
 */
class CustomerAddress extends BaseModel {

    public $table = 'tbl_customer_address';
    public $id_field = 'id';

    protected $uid;

    public function create($post)
    {
        $this->createFromArray($post);
        $this->save();
        return $this->id();
    }

    public function createFromArray($data = [])
    {
        $this->full_name = $data['fullName'];
        $this->address_line_1 = $data['addressLine1'];
        $this->address_line_2 = $data['addressLine2'];
        $this->city = $data['city'];
        $this->region = $data['region'];
        $this->zip = $data['zip'];
        $this->country_id = $data['country_id'];
        $this->phone = $data['phone'];
        $this->customer_id = $this->pixie->auth->user() ? $this->pixie->auth->user()->id() : null;
    }

    /**
     * @return array|CustomerAddress|CustomerAddress[]
     */
    public function getAll()
    {
        return $this->where('customer_id', $this->pixie->auth->user()->id())->find_all()->as_array();
    }

    public function getById($addressId)
    {
        /** @var CustomerAddress $address */
        $address = $this->where(
            'and', array(
            array('id', '=', $addressId),
            array('customer_id', '=', $this->pixie->auth->user()->id())
        ))->find();
        if ($address->loaded()) {
            return $address->as_array();
        } else {
            return array();
        }
    }

    public function deleteById($addressId)
    {
        /** @var CustomerAddress $address */
        $address = $this->where(
            'and', array(
            array('id', '=', $addressId),
            array('customer_id', '=', $this->pixie->auth->user()->id())
        ))->find();
        if ($address->loaded()) {
            $address->delete();
        }
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    public function as_array()
    {
        $res = parent::as_array();
        $res['uid'] = $this->getUid();
        return $res;
    }

    /**
     * @param CustomerAddress $address
     * @return bool
     */
    public function isSimilarTo($address)
    {
        return $this->full_name == $address->full_name
            && $this->address_line_1 == $address->address_line_1
            && $this->address_line_2 == $address->address_line_2
            && $this->city == $address->city
            && $this->region == $address->region
            && $this->zip == $address->zip
            && $this->country_id == $address->country_id
            && $this->phone == $address->phone
            && $this->customer_id == $address->customer_id;
    }
}