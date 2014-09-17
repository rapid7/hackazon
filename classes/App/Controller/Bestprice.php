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
        $this->view->subview = 'pages/bestprice';

    }
}