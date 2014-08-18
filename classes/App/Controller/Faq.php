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
        $this->view->subview = 'pages/faq';
        $this->view->entries = $this->model->getEntries();
    }

    public function action_add() {
        $mail = $this->request->post("userEmail");
        $question = $this->request->post("userQuestion");

        if (isset($mail) && isset($question)) {
            $this->checkCsrfToken('faq');

            $params = array();
            $params["userEmail"] = $mail;
            $params["userQuestion"] = $question;
            $this->model->addEntry($params);
        }        
        $this->redirect('/faq');
    }
}