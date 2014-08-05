<?php

namespace App\Model;

class Cart extends \PHPixie\ORM\Model {

    public $table = 'tbl_cart';
    public $id_field = 'id';
    private $_cart;
    const STEP_OVERVIEW  = 1;
    const STEP_SHIPPING  = 2;
    const STEP_BILLING   = 3;
    const STEP_CONFIRM   = 4;
    const STEP_ORDER     = 5;

    public function getCart(){
        if (empty($this->_cart)) {
            $this->setCart();
        }
        return $this->_cart;
    }

    private function setCart()
    {
        if (!session_id())
            session_start();
        $uid = session_id();
        $cart = $this->getCartByUID($uid);
        if (!$cart) {
            $cart = $this->createNewCart($uid);
        }
        $this->_cart = $cart;
    }

    private function createNewCart($uid)
    {
        $this->pixie->db->query('insert')->table('tbl_cart')
            ->data(array('uid' => $uid, 'created_at' => date('Y-m-d H:i:s')))
            ->execute();
        $lastId = $this->pixie->db->insert_id();
        return $this->pixie->orm->get('Cart')->where('id',$lastId)->find();
    }

    private function getCartByUID($uid)
    {
        $cart = $this->pixie->orm->get('Cart')->where('uid',$uid)->find();
        return $cart->loaded() ? $cart : false;
    }

    public function updateAddress($addressId, $type)
    {
        $cart = $this->getCart();
        $cart->{$type . '_address_id'} = $addressId;
        $cart->save();
        $step = $type == 'shipping' ? self::STEP_BILLING : self::STEP_CONFIRM;
        $this->updateLastStep($step);
    }

    public function updateLastStep($step)
    {
        $cart = $this->getCart();
        if ($step > $cart->last_step) {
            $cart->last_step = $step;
            $cart->save();
        }
    }

    public function getStepLabel()
    {
        $cart = $this->getCart();
        $step = $cart->last_step;
        switch ($step) {
            case self::STEP_OVERVIEW :
                return 'shipping';
            case self::STEP_SHIPPING :
                return 'shipping';
            case self::STEP_BILLING :
                return 'billing';
            case self::STEP_CONFIRM :
                return 'confirmation';
            default :
                return 'shipping';
        }
    }
}