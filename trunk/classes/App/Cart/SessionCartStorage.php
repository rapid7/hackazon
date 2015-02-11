<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 05.11.2014
 * Time: 16:16
 */


namespace App\Cart;


use App\Helpers\Session;
use App\Model\Cart;
use App\Model\CartItems;
use App\Model\CustomerAddress;
use App\Model\Product;
use App\Pixie;
use VulnModule\VulnerableField;

class SessionCartStorage implements ICartStorage
{
    /**
     * @var \App\Pixie
     */
    protected $pixie;

    function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
        Session::checkSessionStarted();
        if (!is_array($_SESSION['cart_service'])) {
            $_SESSION['cart_service'] = [
                'cart' => null,
                'items' => [],
                'addresses' => [],
                'shipping_address' => null,
                'billing_address' => null,
                'shipping_address_object' => null,
                'billing_address_object' => null,
                'params' => [],
                'removed_addresses' => [],
                'last_step' => Cart::STEP_OVERVIEW
            ];

            $this->reset();
        }
    }

    /**
     * @return \App\Model\CartItems[]|array
     */
    public function getItems()
    {
        return $_SESSION['cart_service']['items'];
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return bool
     */
    public function setProductCount($product, $quantity = 1)
    {
        $filteredQuantity = $quantity instanceof VulnerableField ? $quantity->getFilteredValue() : $quantity;
        if ($filteredQuantity <= 0) {
            $this->removeProduct($product);
            return;
        }

        if ($this->hasProduct($product)) {
            /** @var CartItems $item */
            foreach ($_SESSION['cart_service']['items'] as $item) {
                if ($item->product_id == $product->id()) {
                    if (!is_infinite($filteredQuantity) && !is_null($filteredQuantity) && is_numeric($filteredQuantity)) {
                        $item->qty = $quantity;
                    }
                    return;
                }
            }

        } else {
            $this->addProduct($product, $quantity);
        }
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function hasProduct($product)
    {
        /** @var CartItems $item */
        foreach ($_SESSION['cart_service']['items'] as $item) {
            if ($item->product_id == $product->id()) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param Product $product
     * @param int $quantity
     */
    public function addProduct($product, $quantity = 1)
    {
        $filteredQuantity = $quantity instanceof VulnerableField ? $quantity->getFilteredValue() : $quantity;

        if (is_infinite($filteredQuantity) || is_null($filteredQuantity) || !is_numeric($filteredQuantity) || $filteredQuantity <= 0) {
            return;
        }

        $added = false;
        /** @var CartItems $item */
        foreach ($_SESSION['cart_service']['items'] as $item) {
            // If product already exists, just increase quantity
            if ($item->product_id == $product->id()) {
                $newQuantity = $item->qty + $filteredQuantity;
                if (!is_infinite($newQuantity) && !is_null($newQuantity) && is_numeric($newQuantity)) {
                    $item->qty = $newQuantity;
                    $added = true;
                }
                break;
            }
        }

        // Else - add new item
        if ($added === false) {
            $item = new CartItems($this->pixie);
            $item->created_at = date('Y-m-d H:i:s');
            $item->product_id = $product->id();
            $item->qty = $quantity;
            $item->price = $product->Price;
            $item->name = $product->name;
            $this->getCart()->items_count += $filteredQuantity;
            $_SESSION['cart_service']['items'][] = $item;
        }

        $this->getCart()->items_qty += $filteredQuantity;
    }

    /**
     * @param Product $product
     * @param int $quantity
     */
    public function removeProduct($product, $quantity = 1)
    {
        if ($quantity == 0) {
            return;
        }

        /** @var CartItems $item */
        foreach ($_SESSION['cart_service']['items'] as $key => $item) {
            // If product already exists, just increase quantity
            if ($item->product_id == $product->id()) {
                if ($quantity >= 0 && $item->qty > $quantity) {
                    $item->qty -= $quantity;

                } else {
                    unset($_SESSION['cart_service']['items'][$key]);
                }
                return;
            }
        }
    }

    /**
     * @return \App\Model\Product[]|array
     */
    public function getProducts()
    {
        $products = [];
        /** @var CartItems $item */
        foreach ($_SESSION['cart_service']['items'] as $item) {
            $products[$item->product_id] = $item->getItemProduct();
        }

        return $products;
    }

    public function reset()
    {
        $_SESSION['cart_service']['items'] = [];
        $_SESSION['cart_service']['addresses'] = [];
        $_SESSION['cart_service']['removed_addresses'] = [];
        $_SESSION['cart_service']['params'] = [];
        $_SESSION['cart_service']['last_step'] = Cart::STEP_OVERVIEW;
        $_SESSION['cart_service']['shipping_address'] = null;
        $_SESSION['cart_service']['billing_address'] = null;
        $_SESSION['cart_service']['shipping_address_object'] = null;
        $_SESSION['cart_service']['billing_address_object'] = null;

        $cart = new Cart($this->pixie);
        $cart->billing_address_id = 0;
        $cart->shipping_address_id = 0;
        $cart->created_at = date('Y-m-d H:i:s');
        $cart->customer_id = 0;
        $cart->items_count = 0;
        $cart->items_qty = 0;
        $cart->customer_email = '';
        $cart->customer_is_guest = 0;
        $cart->last_step = 0;
        $cart->payment_method = null;
        $cart->shipping_method = null;
        $cart->uid = session_id();
        $_SESSION['cart_service']['cart'] = $cart;

        if ($this->pixie->auth->user()) {
            /** @var CustomerAddress $customerAddressModel */
            $customerAddressModel = $this->pixie->orm->get('CustomerAddress');
            $addresses = $customerAddressModel->getAll();
            // Add addresses via loop in order to add a uid to each address
            foreach ($addresses as $address) {
                $this->addAddress($address);
            }
        }
    }

    public function count()
    {
        $itemQuantity = 0;
        /** @var CartItems $item */
        foreach ($_SESSION['cart_service']['items'] as $item) {
            $itemQuantity += $item->qty;
        }
        return $itemQuantity;
    }

    public function productCount()
    {
        return count($_SESSION['cart_service']['items']);
    }

    /**
     * @return Cart
     */
    public  function getCart()
    {
        if (!$_SESSION['cart_service']['cart']) {
            $this->reset();
        }
        return $_SESSION['cart_service']['cart'];
    }

//    public function getItemsTotal()
//    {
//        $cart = $this->getCart();
//        $coupon = $cart->getCoupon();
//        $items = $this->getAllItems();
//        $total = 0;
//        foreach ($items as $item) {
//            $total += $item->price * $item->qty;
//        }
//        $total *= $coupon ? (1.0 - $coupon->discount / 100) : 1;
//        return $total;
//    }

    public function clear()
    {
        $_SESSION['cart_service']['items'] = [];
        $cart = $this->getCart();
        $cart->items_count = 0;
        $cart->items_qty = 0;
    }

    public function setParam($param, $value)
    {
        if (!is_array($_SESSION['cart_service']['params'])) {
            $_SESSION['cart_service']['params'] = [];
        }
        $_SESSION['cart_service']['params'][$param] = $value;
    }

    public function getParam($param)
    {
        return $_SESSION['cart_service']['params'][$param];
    }

    public function hasParam($param)
    {
        if (!is_array($_SESSION['cart_service']['params'])) {
            $_SESSION['cart_service']['params'] = [];
        }
        return array_key_exists($param, $_SESSION['cart_service']['params']);
    }

    /**
     * @return array|CustomerAddress[]
     */
    public function getAddresses()
    {
        if (!is_array($_SESSION['cart_service']['addresses'])) {
            $_SESSION['cart_service']['addresses'] = [];
        }
        return $_SESSION['cart_service']['addresses'];
    }

    public function getAddress($uid)
    {
        /** @var CustomerAddress $address */
        foreach ($this->getAddresses() as $address) {
            if ($address->getUid() == $uid) {
                return $address;
            }
        }

        return null;
    }

    /**
     * @param CustomerAddress $address
     * @return string
     */
    public function addAddress($address)
    {
        $uid = uniqid();
        $address->setUid($uid);
        $_SESSION['cart_service']['addresses'][] = $address;
        return $uid;
    }

    public function removeAddress($uid)
    {
        if ($uid instanceof CustomerAddress) {
            $uid = $uid->getUid();
        }

        if (!$uid) {
            return;
        }

        $this->getRemovedAddresses();

        /** @var CustomerAddress $address */
        foreach ($this->getAddresses() as $key => $address) {
            if ($address->getUid() == $uid) {
                $_SESSION['cart_service']['removed_addresses'][] = $_SESSION['cart_service']['addresses'][$key];
                unset($_SESSION['cart_service']['addresses'][$key]);
                break;
            }
        }
    }

    public function getShippingAddressUid()
    {
        return $_SESSION['cart_service']['shipping_address'];
    }

    public function setShippingAddressUid($uid)
    {
        $_SESSION['cart_service']['shipping_address'] = $uid;
    }

    public function getBillingAddressUid()
    {
        return $_SESSION['cart_service']['billing_address'];
    }

    public function setBillingAddressUid($address)
    {
        $_SESSION['cart_service']['billing_address'] = $address;
    }

    public function getShippingAddress()
    {
        return $_SESSION['cart_service']['shipping_address_object'];
    }

    public function setShippingAddress($address)
    {
        $_SESSION['cart_service']['shipping_address_object'] = $address;
    }

    public function getBillingAddress()
    {
        return $_SESSION['cart_service']['billing_address_object'];
    }

    public function setBillingAddress($address)
    {
        $_SESSION['cart_service']['billing_address_object'] = $address;
    }

    public function getLastStep()
    {
        return $_SESSION['cart_service']['last_step'];
    }

    public function setLastStep($step) {
        $_SESSION['cart_service']['last_step'] = $step;
    }

    public function findSimilar($address)
    {
        if (!is_array($_SESSION['cart_service']['addresses'])) {
            return null;
        }

        foreach ($this->getAddresses() as $addr) {
            if ($address->isSimilarTo($addr)) {
                return $addr->getUid();
            }
        }

        return null;
    }

    public function getRemovedAddresses()
    {
        if (!is_array($_SESSION['cart_service']['removed_addresses'])) {
            $_SESSION['cart_service']['removed_addresses'] = [];
        }
        return $_SESSION['cart_service']['removed_addresses'];
    }
}