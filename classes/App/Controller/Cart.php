<?php
namespace App\Controller;

use App\Model\Cart as CartModel;
use App\Page;
use VulnModule\Config\Annotations as Vuln;

/**
 * Class Cart
 * @package App\Controller
 * @Vuln\Description("Cart operations.")
 */
class Cart extends Page {

    /**
     * show overview page
     */
    public function action_index() {
        $this->redirect('/cart/view');
    }

    /**
     * Add product to cart
     * @Vuln\Description("No views used. It's AJAX method.")
     */
    public function action_add() {
        $ids = [];
        if ($this->request->is_ajax()) {
            $ids = $this->getProductsInCartIds();
        }

        $qty = $this->request->postWrap('qty', 1);
        if (is_numeric($qty->getFilteredValue())) {
            if ($qty->getFilteredValue() > 1000) {
                $qty = $qty->copy(1000);
            } else if ($qty->getFilteredValue() <= 0) {
                $qty = $qty->copy(1);
            }
        }

        $productId = $this->request->postWrap('product_id');
        $result = $this->pixie->cart->addProductWithResult($productId, $qty);

        if ($this->request->is_ajax()) {
            $this->jsonResponse([
                'success' => 1,
                'productId' => $productId->getFilteredValue(),
                'newProduct' => !in_array($productId->getFilteredValue(), $ids),
                'product' => $result['product']->getFields([
                    'productID', 'name', 'Price'
                ]),
                'item' => $result['item']->getFields([
                    'id', 'qty', 'price'
                ])
            ]);

        } else {
            $this->redirect('/cart/view');
        }
    }

    /**
     * show overview page
     * @Vuln\Description("View: cart/view")
     */
    public function action_view() {
        $cartService = $this->pixie->cart;
        /** @var \App\Model\Cart $cart */
        $cart = $cartService->getCart();
        $items = $cartService->getItems();
        $this->view->subview = 'cart/view';
        $this->view->items = $items;
        $this->view->creditCardNumber = $cartService->getParam('credit_card_number', '');
        $this->view->creditCardYear = $cartService->getParam('credit_card_year', '');
        $this->view->creditCardMonth = $cartService->getParam('credit_card_month', '');
        $this->view->creditCardCVV = $cartService->getParam('credit_card_cvv', '');
        $this->view->paymentMethod = $cart->payment_method;
        $this->view->shippingMethod = $cart->shipping_method;
        $this->view->itemQty = count($cartService);
        $this->view->totalPrice = $cartService->getTotalPrice();
        $this->view->tab = 'overview';
        $this->view->coupon = $cartService->getCoupon();
        $this->view->step = $cartService->getStepLabel();
    }

    /**
     * update cart items qty
     * @Vuln\Description("No view. It's AJAX method.")
     */
    public function action_update() {
        $quantity = $this->request->postWrap('qty');
        $productId = $this->request->postWrap('productId');
        $service = $this->pixie->cart;
        $service->setProductCount($productId, $quantity);

        $res = ['items_qty' => count($service), 'total_price' => $service->getTotalPrice()];
        $this->jsonResponse($res);
    }

    /**
     * clean cart
     * @Vuln\Description("No view. It's AJAX method.")
     */
    public function action_empty() {
        $this->pixie->cart->clear();
        $this->execute=false;
    }

    /**
     * set shipping & payment methods
     * @Vuln\Description("No view.")
     */
    public function action_setMethods() {
        $this->checkCsrfToken('checkout_step_1', null, !$this->request->is_ajax());
        $service = $this->pixie->cart;
        $cart = $service->getCart();
        $cart->shipping_method = $this->request->postWrap('shipping_method');
        $cart->payment_method = $this->request->postWrap('payment_method');

        if ($cart->payment_method == 'creditcard') {
            $service->setParam('credit_card_number', $this->request->postWrap('credit_card_number'));
            $service->setParam('credit_card_year', $this->request->postWrap('credit_card_year'));
            $service->setParam('credit_card_month', $this->request->postWrap('credit_card_month'));
            $service->setParam('credit_card_cvv', $this->request->postWrap('credit_card_cvv'));
        }

        $this->execute = false;
        $service->updateLastStep(CartModel::STEP_SHIPPING);
    }
}