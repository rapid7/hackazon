<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 05.11.2014
 * Time: 15:35
 */


namespace App\Cart;


use App\Exception\NotFoundException;
use App\Exception\RedirectException;
use App\Model\Cart;
use App\Model\Coupon;
use App\Model\CustomerAddress;
use App\Model\Order;
use App\Model\OrderAddress;
use App\Model\OrderItems;
use App\Model\Product;
use App\Model\User;
use App\Pixie;

/**
 * Class CartService
 * @package App\Cart
 */
class CartService implements ICartStorage
{
    /**
     * @var ICartStorage
     */
    protected $storage;

    /**
     * @var Pixie
     */
    protected $pixie;

    public function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
        $this->storage = new SessionCartStorage($this->pixie);
    }


    /**
     * Get cart items list
     * @return \App\Model\CartItems[]|array
     */
    public function getItems()
    {
        return $this->storage->getItems();
    }

    /**
     * Inserts new cart item for product, or increases its quantity, if item exists already.
     * @param $product
     * @param int $quantity
     * @throws \App\Exception\NotFoundException
     * @throws \IllegalArgumentException
     */
    public function addProduct($product, $quantity = 1)
    {
        if (!is_numeric($quantity) || $quantity <= 0) {
            return;
        }

        if (is_numeric($product)) {
            $product = $this->pixie->orm->get('Product')->where('productID', $product)->find();
        }

        if (!($product instanceof Product)) {
            throw new \IllegalArgumentException("Product must be of class App\\Model\\Product");
        }

        if (!$product->loaded()) {
            throw new NotFoundException("Product does not exist");
        }

        $this->storage->addProduct($product, $quantity);
    }

    /**
     * Removes cart item for given product.
     * @param $product
     * @param int $quantity
     */
    public function removeProduct($product, $quantity = 1)
    {
        if (!is_numeric($quantity)) {
            return;
        }

        if (is_numeric($product)) {
            $product = $this->pixie->orm->get('Product')->where('productID', $product)->find();
        }

        if (!($product instanceof Product) || !$product->loaded()) {
            return;
        }

        $this->storage->removeProduct($product, $quantity);
    }

    /**
     * Get all products contained by the cart.
     * @return \App\Model\Product[]|array
     */
    public function getProducts()
    {
        return $this->storage->getProducts();
    }

    /**
     * Reset cart service to default
     */
    public function reset()
    {
        $this->storage->reset();
    }

    /**
     * Get total item quantity in the cart.
     * @return int
     */
    public function count()
    {
        return $this->storage->count();
    }

    /**
     * Get product number.
     * @return int
     */
    public function productCount()
    {
        return $this->storage->productCount();
    }

    /**
     * Helper method for convenience in controller.
     * @param $productId
     * @param $quantity
     * @return array
     */
    public function addProductWithResult($productId, $quantity)
    {
        $result = [
            'item' => null,
            'product' => null
        ];

        if (!is_numeric((string) $quantity)) {
            return $result;
        }

        $product = $this->pixie->orm->get('Product')->where('productID', $productId)->find();

        if (!($product instanceof Product) || !$product->loaded()) {
            return $result;
        }

        $this->storage->addProduct($product, $quantity);

        $items = $this->storage->getItems();

        $productItem = null;
        foreach ($items as $item) {
            if ($item->product_id == (string) $productId) {
                $productItem = $item;
                break;
            }
        }

        $result['product'] = $product;
        $result['item'] = $productItem;
        return $result;
    }

    /**
     * @param int $couponId
     */
    public function useCoupon($couponId)
    {
        $coupons = $this->pixie->session->get('_coupons', []);
        $coupons['__session__'] = $couponId;
        $this->pixie->session->set('_coupons', $coupons);
    }

    public function unsetCoupon()
    {
        $coupons = $this->pixie->session->get('_coupons', []);
        unset($coupons['__session__']);
        $this->pixie->session->set('_coupons', $coupons);
    }

    /**
     * @return Coupon|null
     */
    public function getCoupon()
    {
        $coupons = $this->pixie->session->get('_coupons', []);
        if (!($couponId = $coupons['__session__'])) {
            return null;
        }

        /** @var Coupon $coupon */
        $coupon = $this->pixie->orm->get('coupon', $couponId);
        if (!$coupon->loaded()) {
            return null;
        }

        return $coupon;
    }

    /**
     * @return Cart|null
     */
    public function getCart()
    {
        return $this->storage->getCart();
    }

    public function getTotalPrice()
    {
        $coupon = $this->getCoupon();
        $total = 0;
        foreach ($this->getItems() as $item) {
            $quantity = is_infinite($item->qty) || is_null($item->qty) || !is_numeric($item->qty) ? 1 : $item->qty;
            $total += $item->price * $quantity;
        }
        $total *= $coupon ? (1.0 - $coupon->discount / 100) : 1;
        return $total;
    }

    public function setProductCount($product, $quantity = 1)
    {
        if (!is_numeric((string) $quantity)) {
            return;
        }

        if (is_numeric((string) $product)) {
            $product = $this->pixie->orm->get('Product')->where('productID', $product)->find();
        }

        if (!($product instanceof Product)) {
            throw new \IllegalArgumentException("Product must be of class App\\Model\\Product");
        }

        if (!$product->loaded()) {
            throw new NotFoundException("Product does not exist");
        }

        $this->storage->setProductCount($product, $quantity);
    }

    /**
     * @param Product|int $product
     * @throws \IllegalArgumentException
     * @return boolean
     */
    public function hasProduct($product)
    {
        if (is_numeric($product)) {
            $product = $this->pixie->orm->get('Product')->where('productID', $product)->find();
        }

        if (!($product instanceof Product)) {
            throw new \IllegalArgumentException("Product must be of class App\\Model\\Product");
        }

        if (!$product->loaded()) {
            return false;
        }
        return $this->storage->hasProduct($product);
    }

    public function clear()
    {
        $this->storage->clear();
    }

    public function setLastStep($lastStep)
    {
        $this->storage->setLastStep($lastStep);
    }

    public function updateLastStep($lastStep)
    {
        if ($lastStep > $this->getLastStep()) {
            $this->setLastStep($lastStep);
        }
    }

    /**
     * @return int
     */
    public function getLastStep()
    {
        return $this->storage->getLastStep();
    }

    public function setParam($param, $value)
    {
        $this->storage->setParam($param, $value);
    }

    public function getParam($param, $default = null)
    {

        return $this->hasParam($param) ? $this->storage->getParam($param) : $default;
    }

    public function hasParam($param)
    {
        return $this->storage->hasParam($param);
    }

    public function getStepLabel() {
        return $this->getCart()->getStepLabel($this->getCart(), $this->getLastStep());
    }

    public function getAddresses()
    {
        return $this->storage->getAddresses();
    }

    /**
     * @param $uid
     * @return CustomerAddress|null
     */
    public function getAddress($uid)
    {
        return $this->storage->getAddress($uid);
    }

    public function addAddress($address)
    {
        return $this->storage->addAddress($address);
    }

    public function removeAddress($address)
    {
        $this->storage->removeAddress($address);
    }

    public function getShippingAddressUid()
    {
        return $this->storage->getShippingAddressUid();
    }

    /**
     * @return CustomerAddress|null
     */
    public function getShippingAddress()
    {
        return $this->storage->getShippingAddress();
    }

    /**
     * @return CustomerAddress|null
     */
    public function getBillingAddress()
    {
        return $this->storage->getBillingAddress();
    }

    public function setShippingAddressUid($address)
    {
        $this->storage->setShippingAddressUid($address);
    }

    public function getBillingAddressUid()
    {
        return $this->storage->getBillingAddressUid();
    }

    public function setBillingAddressUid($address)
    {
        $this->storage->setBillingAddressUid($address);
    }

    public function checkCart()
    {
        $cart = $this->getCart();

        if (count($this->getItems()) == 0) {
            throw new RedirectException('/cart/view');
        }

        if (!$cart->shipping_method || !$cart->payment_method) {
            throw new RedirectException('/cart/view');
        }

        if ($cart->payment_method == 'creditcard' && (
            !$this->getParam('credit_card_number') || !$this->getParam('credit_card_number')->raw()
            || !$this->getParam('credit_card_year') || !$this->getParam('credit_card_year')->raw()
            || !$this->getParam('credit_card_month') || !$this->getParam('credit_card_month')->raw()
            || !$this->getParam('credit_card_cvv') || !$this->getParam('credit_card_cvv')->raw()
            )
        ) {
            throw new RedirectException('/cart/view');
        }

        if (!$this->getShippingAddress()) {
            throw new RedirectException('/checkout/shipping');
        }

        if (!$this->getBillingAddress()) {
            throw new RedirectException('/checkout/billing');
        }
    }

    public function getDiscount()
    {
        return $this->getCoupon() ? $this->getCoupon()->discount : 0;
    }

    public function placeOrder()
    {
        /** @var Order $order */
        $order = $this->pixie->orm->get('Order');//set order
        /** @var User $customer */
        $customer = $this->pixie->orm->get('User')->where('id',$this->pixie->auth->user()->id())->find();
        $coupon = $this->getCoupon();
        $cart = $this->getCart();

        if ($cart->payment_method == 'creditcard') {
            $customer->credit_card = $this->getParam('credit_card_number');
            $customer->credit_card_expires = sprintf("%02d/%04d", $this->getParam('credit_card_month'),
                    $this->getParam('credit_card_year'));
            $customer->credit_card_cvv = $this->getParam('credit_card_cvv');
            $customer->save();
        }

        $order->created_at = date('Y-m-d H:i:s');
        $order->customer_firstname = $customer->username;
        $order->customer_email = $customer->email;
        $order->customer_id = $customer->id();
        $order->payment_method = $this->getCart()->payment_method;
        $order->shipping_method = $this->getCart()->shipping_method;
        $order->status = 'complete';
        $order->discount = $coupon ? (int)$coupon->discount : 0;
        $order->save();

        $items = $this->getItems();
        foreach ($items as $item) {
            /** @var OrderItems $orderItems */
            $orderItems = $this->pixie->orm->get('OrderItems');//set order items
            //$orderItems->cart_id = $item->cart_id;
            $orderItems->created_at = date('Y-m-d H:i:s');
            $orderItems->product_id = $item->product_id;
            $orderItems->qty = $item->qty;
            $orderItems->price = $item->price;
            $orderItems->name= $item->name;
            $orderItems->order_id = $order->id();
            $orderItems->save();
        }

        $this->getShippingAddress()->save();
        $this->getBillingAddress()->save();

        $addresses = array(
            'shipping' => $this->getShippingAddress(),
            'billing' => $this->getBillingAddress()
        );
        foreach ($addresses as $type => $address) {//set order addresses
            /** @var OrderAddress $orderAddress */
            $orderAddress = $this->pixie->orm->get('OrderAddress');
            $orderAddress->full_name = $address->full_name;
            $orderAddress->address_line_1 = $address->address_line_1;
            $orderAddress->address_line_2 = $address->address_line_2;
            $orderAddress->city = $address->city;
            $orderAddress->region = $address->region;
            $orderAddress->zip = $address->zip;
            $orderAddress->country_id = $address->country_id;
            $orderAddress->phone = $address->phone;
            $orderAddress->customer_id = $this->pixie->auth->user()->id();
            $orderAddress->address_type = $type;
            $orderAddress->order_id = $order->id();
            $orderAddress->save();
        }
        $this->updateLastStep(Cart::STEP_ORDER);
        $this->unsetCoupon();

        // Remove Addresses
        foreach ($this->getRemovedAddresses() as $addrForRemove) {
            $addrForRemove->delete();
        }

        //$this->reset();
    }

    public function setShippingAddress($address)
    {
        $this->storage->setShippingAddress($address);
    }

    public function setBillingAddress($address)
    {
        $this->storage->setBillingAddress($address);
    }

    public function findSimilar($address)
    {
        return $this->storage->findSimilar($address);
    }

    /**
     * @return array|CustomerAddress[]
     */
    public function getRemovedAddresses()
    {
        return $this->storage->getRemovedAddresses();
    }
}