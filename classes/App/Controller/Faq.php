<?php

namespace App\Controller;

class Faq extends \App\Page {

    public function action_index() {
        $this->view->subview = 'pages/faq';
        $this->view->entries = $this->model->getEntries();
    }

    public function action_add() {
        if (isset($_POST["userEmail"]) && isset($_POST["userQuestion"])) {
            $params = array();
            $params["userEmail"] = $_POST["userEmail"];
            $params["userQuestion"] = $_POST["userQuestion"];
            $id = $this->model->addEntry($params);
        }        
        $this->redirect('/faq');
    }

}