<?php

namespace App\Controller;

class Contact extends \App\Page {

    public function action_index() {
        $this->view->pageTitle = "Contact us";
        if ($this->request->method == 'POST') {
            $this->checkCsrfToken('contact');
            $post = $this->request->post();
            $this->pixie->orm->get('ContactMessages')->create($post);
            if ($this->request->is_ajax()) 
                $this->execute = false;
        }
        $this->view->subview = 'pages/contact';
    }
}