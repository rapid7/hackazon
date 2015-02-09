<?php

namespace App\Controller;
use App\Page;

/**
 * Class Faq
 * @property \App\Model\Faq $model
 * @package App\Controller
 */
class Bestprice extends Page
{
    public function action_index() {
        if ($this->request->method == 'POST') {
            $this->checkCsrfToken('bestprice', null, !$this->request->is_ajax());

            if ($this->request->is_ajax()) {
                $this->jsonResponse([]);

            } else {
                $this->redirect('/bestprice');
            }
            return;
        }
        $this->view->subview = 'pages/bestprice';
    }
}