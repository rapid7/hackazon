<?php
namespace App\Controller;
use App\Model\Cart as CartModel;

class Checkout extends \App\Page {

    /**
     * require auth
     */
    public function before()
    {
        if (is_null($this->pixie->auth->user())) {
            $this->redirect('/user/login?return_url=' . rawurlencode($this->request->server('REQUEST_URI')));
        }
        parent::before();
    }

    /**
     * validate restrict for actions by last step checkout
     * @param int $actionStep
     */
    protected function restrictActions($actionStep)
    {
        $lastStep = $this->pixie->orm->get('Cart')->getCart()->last_step;
        if ($lastStep == CartModel::STEP_ORDER && $actionStep != CartModel::STEP_ORDER) {
            $this->redirect('/checkout/order');
        }
        if ($actionStep > $lastStep) {
            $this->redirect('/cart/view');
        }
    }

    /**
     * 1. Set cart customer
     * 2. if ajax create/update customer addresses
     * 3. default show shipping step
     */
    public function action_shipping() {
        $this->restrictActions(CartModel::STEP_SHIPPING);
        $customerAddresses = $this->pixie->orm->get('CustomerAddress')->getAll();
        $cartModel = $this->pixie->orm->get('Cart');
        $cart = $cartModel->getCart();
        if (!$cart->customer_id) {
            $cartModel->setCustomer();
        }
        if ($this->request->is_ajax()) {
            $this->checkCsrfToken('checkout_step2', null, false);

            $post = $this->request->post();
            $addressId = isset($post['address_id']) ? $post['address_id'] : 0;
            if (!$addressId) {
                $addressId = $this->pixie->orm->get('CustomerAddress')->create($post);
            }
            $this->pixie->orm->get('Cart')->updateAddress($addressId, 'shipping');
            $this->execute = false;
        } else {
            $this->view->subview = 'cart/shipping';
            $this->view->tab = 'shipping';//active tab
            $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
            $this->view->customerAddresses = $customerAddresses;
        }
    }

    /**
     * if ajax create/update customer addresses
     * default show billing step
     */
    public function action_billing() {
        $this->restrictActions(CartModel::STEP_BILLING);
        $customerAddresses = $this->pixie->orm->get('CustomerAddress')->getAll();

        if ($this->request->is_ajax()) {
            $this->checkCsrfToken('checkout_step3', null, false);

            $post = $this->request->post();
            $addressId = isset($post['address_id']) ? $post['address_id'] : 0;
            if (!$addressId) {
                $addressId = $this->pixie->orm->get('CustomerAddress')->create($post);
            }
            $this->pixie->orm->get('Cart')->updateAddress($addressId, 'billing');
            $this->execute = false;

        } else {
            $this->view->subview = 'cart/billing';
            $this->view->tab = 'billing';
            $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
            $this->view->customerAddresses = $customerAddresses;
        }
    }

    /**
     * ajax action return CustomerAddress json by address id
     */
    public function action_getAddress()
    {
        $post = $this->request->post();
        $addressId = $post['address_id'];
        $address = $this->pixie->orm->get('CustomerAddress')->getById($addressId);
        $this->jsonResponse($address);
    }

    /**
     * delete address by id
     */
    public function action_deleteAddress()
    {
        $post = $this->request->post();
        $addressId = $post['address_id'];
        $this->pixie->orm->get('CustomerAddress')->deleteById($addressId);
        $this->execute = false;
    }

    /**
     * show confirmation step
     */
    public function action_confirmation()
    {
        $this->restrictActions(CartModel::STEP_CONFIRM);
        $this->view->subview = 'cart/confirmation';
        $this->view->tab = 'confirmation';
        $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
        $this->view->cart = $this->pixie->orm->get('Cart')->getCart();
    }

    /**
     * ajax action which create order
     */
    public function action_placeOrder()
    {
        $this->restrictActions(CartModel::STEP_ORDER);
        $this->checkCsrfToken('checkout_step4', null, false);
        $this->pixie->orm->get('Cart')->placeOrder();
        $this->execute = false;
    }

    /**
     * show order step success
     */
    public function action_order()
    {
        $this->restrictActions(CartModel::STEP_ORDER);
        $this->view->subview = 'cart/order';
        $this->view->tab = 'order';
        $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
        $this->view->cart = $this->pixie->orm->get('Cart')->getCart();
        $this->pixie->orm->get('Cart')->getCart()->delete();
    }
}