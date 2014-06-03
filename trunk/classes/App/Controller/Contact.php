<?php
namespace App\Controller;

class Contact extends \App\Page {

	public function action_index(){
		$this->view->subview = 'contact';
	}
	
}