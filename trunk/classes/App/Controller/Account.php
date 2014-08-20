<?php

namespace App\Controller;

class Account extends \App\Page {

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

    public function action_index() {
        $this->view->subview = 'account/account';
    }

    public function action_orders() {
        $myOrders = $this->pixie->orm->get('Order')->getMyOrders();
        $this->view->myOrders = $myOrders;
        $this->view->subview = 'account/orders';
    }
    
    public function action_documents() {
        $myOrders = $this->pixie->orm->get('Order')->getMyOrders();
        $this->view->myOrders = $myOrders;
        $this->view->subview = 'account/documents';
    }
}