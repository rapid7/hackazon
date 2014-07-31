<?php
namespace App\Controller;
use App\Model\Cart as CartModel;

class Cart extends \App\Page {

    public function action_index() {
        $this->redirect('/cart/view');
    }

    public function action_add() {
        $qty = $this->request->post('qty');
        $productId = $this->request->post('product_id');
        $this->pixie->orm->get('CartItems')->addItems($productId, $qty);
        //$this->redirect('/cart/added');//TODO page added products
        $this->redirect('/cart/view');
    }
    public function action_added() {
        if ($this->pixie->session->get('flash_added_product_name') == '') {
            $this->redirect('/cart/view');
        }
        $this->view->subview = 'cart/added';
    }

    public function action_view() {
        $cart = $this->pixie->orm->get('Cart')->getCart();
        $items = $this->pixie->orm->get('CartItems')->getAllItems();
        $this->view->subview = 'cart/view';
        $this->view->items = $items;
        $this->view->itemQty = $cart->items_qty;
        $this->view->totalPrice = $cart->total_price;
    }

    public function action_update() {
        $qty = $this->request->post('qty');
        $itemId = $this->request->post('itemId');
        $this->pixie->orm->get('CartItems')->updateItems($itemId, $qty);
        $cart = $this->pixie->orm->get('Cart')->getCart();
        $res = array('items_qty' => $cart->items_qty, 'total_price' => $cart->total_price);
        $this->execute=false;
        echo json_encode($res);
    }

    public function action_empty() {
        $cart = $this->pixie->orm->get('Cart')->getCart();
        $cart->delete();
        $this->pixie->orm->get('Cart')->getCart();
        $this->execute=false;
    }
}