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
        }
        $this->view->subview = 'cart/shipping';
        $this->view->tab = 'shipping';
        $this->view->step = 'shipping';
        $this->view->customerAddresses = $customerAddresses;
    }

}