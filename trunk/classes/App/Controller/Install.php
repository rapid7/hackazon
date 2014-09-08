<?php
namespace App\Controller;

class Install extends \App\Page {

    /**
     * show overview page
     */
    public function action_index() {
		$this->redirect('/install/step1');
    }

    /**
     * Step 1
     */
	public function action_step1() {
		$this->view->subview = 'install/step1';
		$this->view->tab = 'step1';
		$this->view->step = 'Step 1';
    }

    /**
     * Step 2
     */
	public function action_step2() {
		$this->view->subview = 'install/step2';
		$this->view->tab = 'step2';
		$this->view->step = 'Step 2';
    }


}