<?php
namespace App\Controller;

class About extends \App\Page {

	public function action_index(){
		$this->view->subview = 'pages/about';
		$this->view->message = "Index page";
	}
	
}