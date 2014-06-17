<?php

namespace PHPixie\Auth\Login;

/**
 * Abstract class for handling user login.
 *
 * @package    Auth
 */
abstract class Provider {

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;

	/**
	 * Service instance that this login provider belongs to.
	 * @var \PHPixie\Auth\Service
	 */
	public $service;

	/**
	 * Name of the login provider
	 * @var string
	 */
	protected $name;

	/**
	 * Prefix for fetching configuration options
	 * @var string
	 */
	protected $config_prefix;

	/**
	 * Session key of the users id
	 * @var string
	 */
	protected $user_id_key;

	/**
	 * Constructs password login provider for the specified configuration.
	 *
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param \PHPixie\Pixie\Service $service Service instance that this login provider belongs to.
	 * @param string $config Name of the configuration
	 */
	public function __construct($pixie, $service, $config) {
		$this->pixie = $pixie;
		$this->service = $service;
		$this->config_prefix = "auth.{$config}.login.{$this->name}.";
		$this->user_id_key = "auth_{$config}_{$this->name}_uid";
	}


	/**
	 * Performs user logout.
	 * The default implementation deletes the
	 * session variable holding the user id.
	 *
	 * @return void
	 */
	public function logout() {
		$this->pixie->session->remove($this->user_id_key);
	}

	/**
	 * Sets the user logged in via this provider.
	 * The default implementation stores the users id
	 * in a session variable.
	 *
	 * @param \PHPixie\ORM\Model $user Logged in user
	 * @return void
	 */
	public function set_user($user) {
		$this->pixie->session->set($this->user_id_key, $user->id());
		$this->service->set_user($user, $this->name);
	}

	/**
	 * Checks if the user is logged in with this login provider, if so
	 * notifies the associated Service instance about it.
	 * This default implementation operates based on a session key
	 * holding user id.
	 *
	 * @return bool If the user is logged in
	 */
	public function check_login() {
		$user_id = $this->pixie->session->get($this->user_id_key);
		if ($user_id) {
			$user = $this->service->user_model();
			$user = $user->where($user->id_field, $user_id)->find();
			if ($user->loaded()){
				$this->service->set_user($user, $this->name);
				return true;
			}
		}
		return false;
	}
}
