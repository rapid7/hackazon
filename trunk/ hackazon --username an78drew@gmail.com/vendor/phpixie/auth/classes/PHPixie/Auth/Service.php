<?php

namespace PHPixie\Auth;

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
	protected $model;
	
	/**
	 * Logged in user
	 * @var \PHPixie\ORM\Model
	 */
	protected $user;
	
	/**
	 * Name of the login provider that
	 * the user logged in with.
	 * @var string
	 */
	protected $logged_with;
	
	/**
	 * Login providers array
	 * @var array
	 */
	protected $login_providers = array();
	
	/**
	 * User role driver
	 * @var \PHPixie\Auth\Role\Driver
	 */
	protected $role_driver;
	
	
	/**
	 * Constructs an Auth instance for the specified configuration
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param string $config Name of the configuration.
	 * @throw \Exception If no login providers were configured
	 */
	public function __construct($pixie, $config = 'default') {
		$this->pixie = $pixie;
		$this->model = $pixie->config->get("auth.{$config}.model");
		
		$login_providers = $pixie->config->get("auth.{$config}.login", false);
		if (!$login_providers)
			throw new \Exception("No login providers have been configured.");
			
		foreach(array_keys($login_providers) as $provider) 
			$this->login_providers[$provider] = $pixie->auth->build_login($provider, $this, $config);
		
		$role_driver = $pixie->config->get("auth.{$config}.roles.driver", false);
		if ($role_driver)
			$this->role_driver = $pixie->auth->build_role($role_driver, $config);
		
		$this->check_login();
	}
	
	/**
	 * Sets the logged in user
	 * 
	 * @param \PHPixie\ORM\Model $user logged in user
	 * @param strong $logged_with Name of the provider that
	 *                            performed the login.
	 * @return void
	 */
	public function set_user($user, $logged_with) {
		$this->user = $user;
		$this->logged_with = $logged_with;
	}

	
	/**
	 * Returns the logged in user
	 *
	 * @return \PHPixie\ORM\Model Logged in user
	 */
	public function user() {
		return $this->user;
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