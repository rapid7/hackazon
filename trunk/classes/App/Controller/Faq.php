<?php

namespace App\Controller;
use App\Page;

/**
 * Class Faq
 * @property \App\Model\Faq $model
 * @package App\Controller
 */
class Faq extends Page
{
    public function action_index() {
        $this->view->pageTitle = "Frequently Asked Questions";
        if ($this->request->is_ajax()) {
            $this->checkCsrfToken('faq');

            $post = $this->request->post();
            $item = $this->pixie->orm->get('Faq')->create($post);
            $this->response->body = json_encode(array($item->as_array()));
            $this->execute = false;
        }        
        $this->view->subview = 'pages/faq';
        $this->view->entries = $this->model->getEntries();
    }
}