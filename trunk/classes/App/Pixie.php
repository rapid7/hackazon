<?php

namespace App;

/**
 * Pixie dependency container
 *
 * @property-read \PHPixie\DB $db Database module
 * @property-read \PHPixie\ORM $orm ORM module
 * @property-read \PHPixie\Auth $auth Auth module
 */
class Pixie extends \PHPixie\Pixie {
	protected $modules = array(
		'db' => '\PHPixie\DB',
		'orm' => '\PHPixie\ORM',
                'auth' => '\PHPixie\Auth',
                'vulninjection' => '\PHPixie\VulnInjection',
                'email' => '\PHPixie\Email',
	);
	
	protected function after_bootstrap(){
		//Whatever code you want to run after bootstrap is done.		
            //$this->debug->display_errors = false;
	}
        
        public function handle_exception($exception){
            //If its a Page Not Found error redirect the user to 404 Page
            if ($exception instanceof \PHPixie\Exception\PageNotFound){
                header('Location: /home/404'); 
            }else{
                $http_status = "503 Service Temporarily Unavailable";
                header($_SERVER["SERVER_PROTOCOL"].' '.$http_status);
                header("Status: ".$http_status);
                var_dump($exception);
                echo("Sorry, something is wrong. " . $exception->getMessage());
            }
        }
}
