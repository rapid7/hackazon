<?php

namespace App\Model;

class CartItems extends \PHPixie\ORM\Model {

    public $table = 'tbl_cart_items';
    public $id_field = 'id';

    public function addItems($productId, $qty)
    {
        $product = $this->pixie->orm->get('Product')->where('productID', $productId)->find();
        $cart = $this->pixie->orm->get('Cart')->getCart();

        $itemExist = $this->where(
            'and', array(
                array('product_id', '=', $productId),
                array('cart_id', '=', $cart->id)
            ))->find();
        if ($itemExist->loaded()) {//update existed
            $itemExist->qty += $qty;
            $itemExist->save();
        } else {//create new item
            $this->cart_id = $cart->id;
            $this->created_at = date('Y-m-d H:i:s');
            $this->product_id = $productId;
            $this->qty = $qty;
            $this->price = $product->Price;
            $this->name= $product->name;
            $this->save();
            $cart->items_count += 1;
        }
        $cart->total_price += $product->Price * $qty;
        $cart->items_qty += $qty;
        $cart->save();
        $this->pixie->session->flash('added_product_name', $product->name);
    }

    public function getAllItems()
    {
        $cart = $this->pixie->orm->get('Cart')->getCart();
        $items = $this->where('cart_id',$cart->id)->order_by('created_at','asc')->find_all();
        return $items;
    }

    public function getProduct()
    {
        $product = $this->pixie->orm->get('Product')->where('productID', $this->product_id)->find();
        $product = $product->getProductData($product);
        return $product;
    }

    public function updateItems($itemId, $qty)
    {
        $item = $this->where('id', $itemId)->find();

        $cart = $this->pixie->orm->get('Cart')->getCart();

        if ($qty <= 0) {
            $cart->total_price -= $item->price * $item->qty;
            $cart->items_count -= 1;
            $cart->items_qty -= $item->qty;
        } else {
            $cart->items_count += 1;
            $diffQty = $item->qty - $qty;
            if ($diffQty > 0) {
                $cart->items_qty -= $diffQty;
                $cart->total_price -= $item->price * $diffQty;
            } else {
                $cart->items_qty += abs($diffQty);
                $cart->total_price += abs($item->price * $diffQty);
            }
        }
        $cart->save();

        if ($qty <= 0) {
            $item->delete();
            return true;
        }
        $item->qty = $qty;
        $item->save();
    }
}