<?php
namespace App\Controller;
use App\Model\Cart as CartModel;

class Cart extends \App\Page {

    /**
     * show overview page
     */
    public function action_index() {
        $this->redirect('/cart/view');
    }

    /**
     * Add product to cart
     */
    public function action_add() {
        $qty = $this->request->post('qty');
        $productId = $this->request->post('product_id');
        $this->pixie->orm->get('CartItems')->addItems($productId, $qty);
        $this->redirect('/cart/view');
    }

    /**
     * show overview page
     */
    public function action_view() {
        $cart = $this->pixie->orm->get('Cart')->getCart();
        $items = $this->pixie->orm->get('CartItems')->getAllItems();
        $this->view->subview = 'cart/view';
        $this->view->items = $items;
        $this->view->itemQty = $cart->items_qty;
        $this->view->totalPrice = $cart->total_price;
        $this->view->tab = 'overview';
        $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
    }

    /**
     * update cart items qty
     */
    public function action_update() {
        $qty = $this->request->post('qty');
        $itemId = $this->request->post('itemId');
        $this->pixie->orm->get('CartItems')->updateItems($itemId, $qty);
        $cart = $this->pixie->orm->get('Cart')->getCart();
        $res = array('items_qty' => $cart->items_qty, 'total_price' => $cart->total_price);
        $this->execute=false;
        echo json_encode($res);
    }

    /**
     * clean cart
     */
    public function action_empty() {
        $cart = $this->pixie->orm->get('Cart')->getCart();
        $cart->delete();
        $this->pixie->orm->get('Cart')->getCart();
        $this->execute=false;
    }

    /**
     * set shipping & payment methods
     */
    public function action_setMethods() {
        $cart = $this->pixie->orm->get('Cart')->getCart();
        $cart->shipping_method = $this->request->post('shipping_method');
        $cart->payment_method = $this->request->post('payment_method');
        $cart->save();
        $this->execute=false;
        $this->pixie->orm->get('Cart')->updateLastStep(CartModel::STEP_SHIPPING);
    }
}