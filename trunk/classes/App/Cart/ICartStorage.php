<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 05.11.2014
 * Time: 16:15
 */


namespace App\Cart;


use App\Model\Cart;
use App\Model\Product;

/**
 * Represents the storage for cart and cart items;
 * @package App\Cart
 */
interface ICartStorage extends \Countable
{
    /**
     * @return \App\Model\CartItems[]|array
     */
    public function getItems();

    public function addProduct($product, $quantity = 1);

    public function setProductCount($product, $quantity = 1);

    public function getAddresses();

    public function getAddress($uid);

    public function addAddress($address);

    public function removeAddress($address);

    public function getShippingAddressUid();

    public function setShippingAddressUid($address);

    public function getBillingAddressUid();

    public function setBillingAddressUid($address);

    /**
     * @param Product|int $product
     * @return boolean
     */
    public function hasProduct($product);

    public function removeProduct($product, $quantity = 1);

    /**
     * @return \App\Model\Product[]|array
     */
    public function getProducts();

    public function reset();

    public function clear();

    /**
     * @return int
     */
    public function count();

    /**
     * @return int
     */
    public function productCount();

    /**
     * @return Cart|null
     */
    public function getCart();

    public function setParam($param, $value);

    public function getParam($param);

    public function hasParam($param);

    public function getLastStep();

    public function setLastStep($step);
}