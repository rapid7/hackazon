<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 05.11.2014
 * Time: 16:32
 */


namespace App\Cart;


class DBCartStorage implements ICartStorage
{
    /**
     * @return \App\Model\CartItems[]|array
     */
    public function getItems()
    {
        // TODO: Implement getItems() method.
    }

    public function addProduct($product, $quantity = 1)
    {
        // TODO: Implement addProduct() method.
    }

    public function removeProduct($product, $quantity = 1)
    {
        // TODO: Implement removeProduct() method.
    }

    /**
     * @return \App\Model\Product[]|array
     */
    public function getProducts()
    {
        // TODO: Implement getProducts() method.
    }

    public function reset()
    {
        // TODO: Implement reset() method.
    }

    /**
     * @return int
     */
    public function count()
    {
        // TODO: Implement count() method.
    }

    /**
     * @return int
     */
    public function productCount()
    {
        // TODO: Implement productCount() method.
    }
}