<?php

namespace App\Model;

class Cart extends \PHPixie\ORM\Model {

    public $table = 'tbl_cart';
    public $id_field = 'id';
    private $_cart;

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
}