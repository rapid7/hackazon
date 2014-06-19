<?php

namespace PHPixie;

/**
 * Authroization and access control module for PHPixie.
 *
 * This module is not included by default, download it here:
 *
 * This module is not included by default, install it using Composer
 * by adding
 * <code>
 * 		"phpixie/auth": "2.*@dev"
 * </code>
 * to your requirement definition. Or download it from
 * https://github.com/dracony/PHPixie-Auth
 * 
 * To enable it add it to your Pixie class' modules array:
 * <code>
 * 		protected $modules = array(
 * 			//Other modules ...
 * 			'auth' => '\PHPixie\Auth',
 * 		);
 * </code>
 *
 * This modules let's you log in users using different login providers.
 * Currently two login providers are supported, for the usual login/password
 * login and Facebook authentication.
 * 
 * You can also control access based on user roles. The included role drivers
 * allow you to either use a field in the users table to specify the the role
 * of the user, or to use an ORM relationship between the user and the roles.
 *
 * Please refer to the auth.php config file for instractions how to configure login
 * and role providers.
 * 
 * @link https://github.com/dracony/PHPixie-Auth Download this module from Github
 * @package    Auth
 */
class Auth {

	/**
	 * Pixie Dependency Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;
	
	/**
	 * ORM model that represents a user 
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
	 * the user logged in with
	 * @var string
	 */
	public $logged_with;
	
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
	 * Array of initialized \PHPixie\Auth\Service instances
	 * @var array
	 */
	protected $_services;
	
	/**
	 * Constructs an Auth instance for the specified configuration
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @return void
	 */
	public function __construct($pixie) {
		$this->pixie = $pixie;
		$pixie->assets_dirs[] = dirname(dirname(dirname(__FILE__))).'/assets/';
	}

	/**
	 * Gets an instance of a configured service
	 *
	 * @param string  $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return \PHPixie\Auth\Service  Driver implementation of the Connection class
	 */
	public function service($config = "default") {
		if (!isset($this->_services[$config]))
			$this->_services[$config] = $this->build_service($config);
		
		return $this->_services[$config];
	}

	/**
	 * Builds a service
	 *
	 * @param string  $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return \PHPixie\Auth\Service  Auth Service
	 */
	public function build_service($config) {
		return new \PHPixie\Auth\Service($this->pixie, $config);
	}
	
	/**
	 * Builds a login provider
	 *
	 * @param string  $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return \PHPixie\Auth\Login\Provider  Login Provider
	 */
	public function build_login($provider, $service, $config) {
		$login_class = '\PHPixie\Auth\Login\\'.ucfirst($provider);
		return new $login_class($this->pixie, $service, $config);
	}
	
	/**
	 * Builds a role driver
	 *
	 * @param string  $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return \PHPixie\Auth\Role\Driver  Role Driver
	 */
	public function build_role($driver, $config) {
		$role_class = '\PHPixie\Auth\Role\\'.ucfirst($driver);
		return new $role_class($this->pixie, $config);
	}
	
	/**
	 * Sets the logged in user.
	 * 
	 * @param \PHPixie\ORM\Model $user logged in user
	 * @param string $logged_with Name of the provider that
	 *                            performed the login
	 * @param string  $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return void
	 */
	public function set_user($user, $logged_with, $config = 'default') {
		$this->service($config)->set_user($user, $logged_with);
	}
	
	/**
	 * Logs the user out.
	 *
	 * @param string  $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return void
	 */
	public function logout($config = 'default') {
		$this->service($config)->logout();
	}
	
	/**
	 * Checks if the logged in user has the specified role
	 *
	 * @param string $role Role to check for
	 * @param string  $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return bool If the user has the specified role
	 */
	public function has_role($role, $config = 'default') {
		return $this->service($config)->has_role($role);
		
	}
	
	/**
	 * Returns the login provider by name
	 *
	 * @param string $provider Name of the login provider
	 * @param string  $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return \PHPixie\Auth\Login\Provider Login provider
	 */
	public function provider($provider, $config = 'default') {
		return $this->service($config)->provider($provider);
	}
	
	/**
	 * Returns the logged in user
	 *
	 * @param string  $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return \PHPixie\ORM\Model Logged in user
	 */
	public function user($config = 'default') {
		return $this->service($config)->user();
	}
	
	/**
	 * Returns the name of the provider that the user is logged with
	 *
	 * @param string  $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return string Name of the provider
	 */
	public function logged_with($config = 'default') {
		return $this->service($config)->logged_with();
	}
	
}
