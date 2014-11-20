<?php

namespace App\Controller;

class Contact extends \App\Page {

    public function action_index() {
        $this->view->pageTitle = "Contact us";
        if ($this->request->method == 'POST') {
					$this->checkCsrfToken('contact');
            //$post = json_decode($this->request->rawRequestData(), true);
            $post = json_decode($this->request->rawRequestData());
            //$post = json_decode($this->request->post()['data']);
            $this->pixie->orm->get('ContactMessages')->create($post);
						if ($this->request->is_ajax()) {
                //$this->jsonResponse(null);
								$this->execute = false;
						}
        }
        $this->view->subview = 'pages/contact';
    }

}

