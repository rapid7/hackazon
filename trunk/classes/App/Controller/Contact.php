<?php

namespace App\Controller;

use App\Page;
use VulnModule\Config\FieldDescriptor;
use VulnModule\Config\Annotations as Vuln;

class Contact extends Page
{
    /**
     * @throws \App\Exception\HttpException
     * @Vuln\Description("View: pages/contact.")
     */
    public function action_index() {
        $this->view->pageTitle = "Contact Us";

        if ($this->request->method == 'POST') {
            $this->checkCsrfToken('contact');

            //$post = json_decode($this->request->rawRequestData());
            $postData = $this->request->post();
            $post = json_decode($postData['data']);
            $postWrapped = $this->request->wrapObject($post, FieldDescriptor::SOURCE_BODY);

            $this->pixie->orm->get('ContactMessages')->create($postWrapped);

            if ($this->request->is_ajax()) {
                //$this->jsonResponse(null);
                $this->execute = false;
                return;
            }
        }
        $this->view->subview = 'pages/contact';
    }
}

