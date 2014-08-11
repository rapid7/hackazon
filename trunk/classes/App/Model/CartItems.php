<?php

namespace App\Model;

class CartItems extends \PHPixie\ORM\Model {

    public $table = 'tbl_cart_items';
    public $id_field = 'id';
    private $_cart;

    public function setCart()
    {
        $this->_cart = $this->pixie->orm->get('Cart')->getCart();
    }

    public function getCart()
    {
        if (empty($this->_cart)) {
            $this->setCart();
        }
        return $this->_cart;
    }

    /**
     * Create cart item
     * @param int $productId
     * @param int $qty
     */
    public function addItems($productId, $qty)
    {
        $product = $this->pixie->orm->get('Product')->where('productID', $productId)->find();

        $itemExist = $this->where(
            'and', array(
                array('product_id', '=', $productId),
                array('cart_id', '=', $this->getCart()->id)
            ))->find();
        if ($itemExist->loaded()) {//update existed
            $itemExist->qty += $qty;
            $itemExist->save();
        } else {//create new item
            $this->cart_id = $this->getCart()->id;
            $this->created_at = date('Y-m-d H:i:s');
            $this->product_id = $productId;
            $this->qty = $qty;
            $this->price = $product->Price;
            $this->name= $product->name;
            $this->save();
            $this->getCart()->items_count += 1;
        }
        $this->getCart()->total_price += $product->Price * $qty;
        $this->getCart()->items_qty += $qty;
        $this->getCart()->save();
        $this->pixie->session->flash('added_product_name', $product->name);
    }

    public function getAllItems()
    {
        return $this->where('cart_id',$this->getCart()->id)->order_by('created_at','asc')->find_all()->as_array();
    }

    public function getProduct()
    {
        $product = $this->pixie->orm->get('Product')->where('productID', $this->product_id)->find();
        $product = $product->getProductData($product);
        return $product;
    }

    /**
     * Update items qty, remove items
     * @param int $itemId
     * @param int $qty
     * @return bool
     */
    public function updateItems($itemId, $qty)
    {
        $item = $this->where('id', $itemId)->find();

        if ($qty <= 0) {
            $this->getCart()->total_price -= $item->price * $item->qty;
            $this->getCart()->items_count -= 1;
            $this->getCart()->items_qty -= $item->qty;
        } else {
            $this->getCart()->items_count += 1;
            $diffQty = $item->qty - $qty;
            if ($diffQty > 0) {
                $this->getCart()->items_qty -= $diffQty;
                $this->getCart()->total_price -= $item->price * $diffQty;
            } else {
                $this->getCart()->items_qty += abs($diffQty);
                $this->getCart()->total_price += abs($item->price * $diffQty);
            }
        }
        $this->getCart()->save();

        if ($qty <= 0) {
            $item->delete();
            return true;
        }
        $item->qty = $qty;
        $item->save();
    }

    public function getItemsTotal()
    {
        $items = $this->getAllItems();
        $total = 0;
        foreach ($items as $item) {
            $total += $item->price * $item->qty;
        }
        return $total;
    }
}