<?php

namespace App\Controller;

class Account extends \App\Page {

    public function action_index() {
        $this->view->subview = 'account';
    }

}