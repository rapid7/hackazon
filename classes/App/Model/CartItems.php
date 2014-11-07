<?php

namespace App\Model;

/**
 * Class CartItems
 * @package App\Model
 * @property int $id
 * @property int $cart_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $product_id
 * @property string $name
 * @property int $qty
 * @property number $price
 * @property Product $product
 */
class CartItems extends BaseModel {

    public $table = 'tbl_cart_items';
    public $id_field = 'id';
    private $_cart;

    protected $belongs_to=array(
        'product' => array(
            'model' => 'Product',
            'key' => 'product_id',
        )
    );


    public function setCart($uid = null)
    {
        $this->_cart = $this->pixie->orm->get('Cart')->getCart($uid);
    }

    /**
     * @param null $uid
     * @return Cart
     */
    public function getCart($uid = null)
    {
        if (empty($this->_cart)) {
            $this->setCart($uid);
        }
        return $this->_cart;
    }

    /**
     * Create cart item
     * @param int $productId
     * @param int $qty
     * @return array
     */
    public function addItems($productId, $qty)
    {
        $product = $this->pixie->orm->get('Product')->where('productID', $productId)->find();

        $itemExist = $this->where(
            'and', array(
                array('product_id', '=', $productId),
                array('cart_id', '=', $this->getCart()->id)
            ))->find();

        $item = $itemExist;
        if ($itemExist->loaded()) {//update existed
            $itemExist->qty += $qty;
            $itemExist->save();
        } else if($product->loaded())  {//create new item
            $this->cart_id = $this->getCart()->id;
            $this->created_at = date('Y-m-d H:i:s');
            $this->product_id = $productId;
            $this->qty = $qty;
            $this->price = $product->Price;
            $this->name= $product->name;
            $this->save();
            $this->getCart()->items_count += 1;
            $item = $this;
        }
        if($product->loaded()){
            $this->getCart()->total_price += $product->Price * $qty;
            $this->getCart()->items_qty += $qty;
            $this->pixie->session->flash('added_product_name', $product->name);
        }

        $this->getCart()->total_price = $this->getItemsTotal();
        $this->getCart()->save();
        
        return [
            'item' => $item,
            'product' => $product
        ];
    }

    public function getAllItems()
    {
        return $this->pixie->orm->get('cartItems')->where('cart_id',$this->getCart()->id())->order_by('created_at','asc')->find_all()->as_array();
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

        $this->getCart()->total_price = $this->getItemsTotal();
        $this->getCart()->save();
    }

    public function getItemsTotal()
    {
        $cart = $this->getCart();
        $coupon = $cart->getCoupon();
        $items = $this->getAllItems();
        $total = 0;
        foreach ($items as $item) {
            $total += $item->price * $item->qty;
        }
        $total *= $coupon ? (1.0 - $coupon->discount / 100) : 1;
        return $total;
    }

    public function __wakeup()
    {
        parent::__wakeup();
    }

    public function getItemProduct()
    {
        if ($this->loaded()) {
            return $this->product;
        } else {
            return $this->pixie->orm->get('Product')->where('productID', $this->product_id)->find();
        }
    }
}