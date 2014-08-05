<?php
namespace App\Controller;
use App\Model\Cart as CartModel;

class Checkout extends \App\Page {

    public function before()
    {
        if (is_null($this->pixie->auth->user())) {
            $this->redirect('/user/login?return_url=' . rawurlencode($this->request->server('REQUEST_URI')));
        }
        parent::before();
    }

    public function restrictActions($actionStep)
    {
        $lastStep = $this->pixie->orm->get('Cart')->getCart()->last_step;
        if ($actionStep > $lastStep) {
            $this->redirect('/cart/view');
        }
    }

    public function action_shipping() {
        $this->restrictActions(CartModel::STEP_SHIPPING);
        $customerAddresses = $this->pixie->orm->get('CustomerAddress')->getAll();
        if ($this->request->is_ajax()) {
            $post = $this->request->post();
            $addressId = isset($post['address_id']) ? $post['address_id'] : 0;
            if (!$addressId) {
                $addressId = $this->pixie->orm->get('CustomerAddress')->create($post);
            }
            $this->pixie->orm->get('Cart')->updateAddress($addressId, 'shipping');
            $this->execute=false;
        } else {
            $this->view->subview = 'cart/shipping';
            $this->view->tab = 'shipping';//active tab
            $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
            $this->view->customerAddresses = $customerAddresses;
        }
    }

    public function action_billing() {
        $this->restrictActions(CartModel::STEP_BILLING);
        $customerAddresses = $this->pixie->orm->get('CustomerAddress')->getAll();
        if ($this->request->is_ajax()) {
            $post = $this->request->post();
            $addressId = isset($post['address_id']) ? $post['address_id'] : 0;
            if (!$addressId) {
                $addressId = $this->pixie->orm->get('CustomerAddress')->create($post);
            }
            $this->pixie->orm->get('Cart')->updateAddress($addressId, 'billing');
            $this->execute=false;
        } else {
            $this->view->subview = 'cart/billing';
            $this->view->tab = 'billing';
            $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
            $this->view->customerAddresses = $customerAddresses;
        }
    }

    public function action_getAddress()
    {
        $post = $this->request->post();
        $addressId = $post['address_id'];
        $address = $this->pixie->orm->get('CustomerAddress')->getById($addressId);
        $this->execute=false;
        echo json_encode($address);
    }

    public function action_deleteAddress()
    {
        $post = $this->request->post();
        $addressId = $post['address_id'];
        $this->pixie->orm->get('CustomerAddress')->deleteById($addressId);
        $this->execute=false;
    }

    public function action_confirmation()
    {
        $this->restrictActions(CartModel::STEP_CONFIRM);
        $this->view->subview = 'cart/confirmation';
        $this->view->tab = 'confirmation';
        $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
    }
}