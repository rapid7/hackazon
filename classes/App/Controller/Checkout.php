<?php
namespace App\Controller;

class Checkout extends \App\Page {

    public function before()
    {
        if (is_null($this->pixie->auth->user())) {
            $this->redirect('/user/login?return_url=' . rawurlencode($this->request->server('REQUEST_URI')));
        }
        parent::before();
    }

    public function action_shipping() {
        $customerAddresses = $this->pixie->orm->get('CustomerAddress')->getAll();
        if ($this->request->is_ajax()) {
            $post = $this->request->post();
            $this->pixie->orm->get('CustomerAddress')->create($post);
            $this->execute=false;
        } else {
            $this->view->subview = 'cart/shipping';
            $this->view->tab = 'shipping';
            $this->view->step = 'shipping';
            $this->view->customerAddresses = $customerAddresses;
        }
    }

    public function action_billing() {
        $customerAddresses = $this->pixie->orm->get('CustomerAddress')->getAll();
        $this->view->subview = 'cart/billing';
        $this->view->tab = 'billing';
        $this->view->step = 'billing';
        $this->view->customerAddresses = $customerAddresses;
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
        $address = $this->pixie->orm->get('CustomerAddress')->deleteById($addressId);
        $this->execute=false;
    }

}