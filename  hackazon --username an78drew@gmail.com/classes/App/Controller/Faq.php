<?php

namespace App\Controller;

class Faq extends \App\Page {

    public function action_index() {
        $this->view->subview = 'pages/faq';
        $this->view->entries = $this->model->getEntries();
    }

    public function action_add() {
        //ajax request

        if (isset($_POST["userEmail"]) && isset($_POST["userQuestion"])) {
            //Prepare params - Sanitization
            $params = array();
            $params["userEmail"] = $_POST["userEmail"];
            $params["userQuestion"] = $_POST["userQuestion"];
            $id = $this->model->addEntry($params);
            $this->view->errorMessage = $e->getMessage();
            $this->response->redirect('/faq');
        }        
    }

}