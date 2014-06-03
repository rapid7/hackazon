<?php
namespace App\Controller;

class Home extends \App\Page {

	public function action_index(){
		$this->view->subview = 'home';
		$this->view->message = "Index page";
	}
	
        public function action_404(){
		$this->view->subview = '404';
		$this->view->message = "Index page";
	}
        
        public function action_install(){
            $this->view->subview = 'home';
            //Remove tables
            $tables = $this->pixie->db->get()->execute("SELECT GROUP_CONCAT(table_name) as tbl FROM information_schema.tables  WHERE table_schema = (SELECT DATABASE())");
            $tblRemove = "";
            foreach($tables as $table){
                if($table[0] != "")
                    $tblRemove = "DROP TABLE IF EXISTS " . $table[0];
            }            
            if($tblRemove != "") $this->pixie->db->get()->execute($tblRemove);
            
            //Install schema
            $dbScript = $this->pixie-> root_dir . "database/db.sql";
            $this->pixie->db->get()->conn->exec(file_get_contents($dbScript));
            
            $demoScript = $this->pixie-> root_dir . "database/demo_database.sql";
            $this->pixie->db->get()->conn->exec(file_get_contents($demoScript));
            
            foreach(scandir($this->pixie-> root_dir . "database/migrations") as $file){
                $file = $this->pixie-> root_dir . "database/migrations/" . $file;
                if(is_file($file)){
                    $this->pixie->db->get()->conn->exec(file_get_contents($file));
                }
            }
            $this->response->redirect('/home');
	}        
}