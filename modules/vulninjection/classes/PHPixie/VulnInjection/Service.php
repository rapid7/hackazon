<?php

namespace PHPixie\VulnInjection;

/**
 * Authroization Service.
 * @package    Auth
 */
class Service {

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;
	
	/**
	 * Name of the ORM model that represents a user 
	 * @var string
	 */
	public $settings;
	

	
	
	/**
	 * Constructs an Auth instance for the specified configuration
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param string $config Name of the configuration.
	 * @throw \Exception If no login providers were configured
	 */
	public function __construct($pixie, $config = 'default') {
		$this->pixie = $pixie;
                $this->settings = $pixie->config->get("vulninjection/{$config}");
                
                //Manage settings
                $this->filterPost();
                $this->filterGet();
	}
	
        private function filterPost(){
            if(isset($this->settings['inputs'])){
                foreach($_POST as $key=>$value){
                    if(!array_key_exists($key, $this->settings['inputs'])){
                        //Prevent any by default
                        $_POST[$key] = mysql_real_escape_string( htmlspecialchars($value) );
                    }else{
                        if(!in_array('xss', $this->settings['inputs'][$key])){
                            $_POST[$key] = htmlspecialchars($_POST[$key]);
                        }
                        if(!in_array('sql', $this->settings['inputs'][$key])){
                            $_POST[$key] = mysql_real_escape_string($_POST[$key]);
                        }
                    }
                }
            }
        }
	
        private function filterGet(){
            
        }
        
	/**
	 * Returns the required section
	 *
	 * @return array
	 */
	public function getSection($sectionName) {
            if(isset($this->settings[$sectionName]))
		return $this->settings[$sectionName];
            return array();
	}

	/**
	 * Returns the name of the provider that the user is logged with
	 *
	 * @return string Name of the provider
	 */
	public function logged_with() {
		return $this->logged_with;
	}
	
	/**
	 * Logs the user out
	 *
	 * @return void
	 */
	public function logout() {
		$this->login_providers[$this->logged_with]->logout();
		$this->logged_with = null;
		$this->user = null;
	}
	
	/**
	 * Checks if the logged in user has the specified role
	 *
	 * @param string $role Role to check for.
	 * @return bool If the user has the specified role
	 * @throws \Exception If the role driver is not specified
	 */
	public function has_role($role) {
		if ($this->role_driver == null)
			throw new \Exception("No role configuration is present.");
		
		if ($this->user == null)
			return false;
			
		return $this->role_driver->has_role($this->user, $role);
		
	}
	
	/**
	 * Returns the login provider by name
	 *
	 * @param string $provider Name of the login provider
	 * @return \PHPixie\Auth\Login\Provider Login provider
	 */
	public function provider($provider) {
		return $this->login_providers[$provider];
	}
	
	/**
	 * Checks if the user is logged in via any of the 
	 * login providers
	 *
	 * @return bool if the user is logged in
	 */
	public function check_login() {
		foreach($this->login_providers as $provider)
			if ($provider->check_login())
				return true;
				
		return false;
	}

	/**
	 * Returns a new instance of the user model
	 *
	 * @return \PHPixie\ORM\Model Model representing the user
	 */
	public function user_model() {
		return $this->pixie->orm->get($this->model);
	}

}