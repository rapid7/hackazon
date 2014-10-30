<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 20.08.2014
 * Time: 13:01
 */


namespace App\Rest\Controller;


use App\Exception\HttpException;
use App\Model\Product;
use App\Rest\Controller;

/**
 * Class Order
 * @package App\Rest\Controller
 * @property \App\Model\Cart $item
 */
class CartItems extends Controller
{
    public function action_post($data = null)
    {
        if ($data === null) {
            $data = $this->request->post();
        }
        unset($data['id']);

        if (!$data['product_id'] || !$data['cart_id']) {
            throw new HttpException;
        }

        // Check whether there is already such a product in the cart
        $cartItem = $this->pixie->orm->get('CartItems')
            ->where('product_id', '=', $data['product_id'])
            ->where('cart_id', '=', $data['cart_id'])
            ->find();

        if ($cartItem->loaded()) {
            throw new HttpException;
        }

        // Check product is real
        /** @var Product $product */
        $product = $this->pixie->orm->get('product')
            ->where('productID', '=', $data['product_id'])
            ->find();

        if (!$product->loaded()) {
            throw new HttpException;
        }

        $data['price'] = $product->Price;

        return parent::action_post($data);
    }

    public function action_put($data = null)
    {
        return parent::action_put($data);
    }
}