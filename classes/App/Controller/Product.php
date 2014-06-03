<?php

namespace App\Controller;

class Product extends \App\Page {

    public function action_index() {
        $this->view->subview = 'product';
    }

    public function action_category() {
        $this->view->subview = 'product_item';
    }

    public function action_item() {
        $this->view->subview = 'product_item';
    }

}