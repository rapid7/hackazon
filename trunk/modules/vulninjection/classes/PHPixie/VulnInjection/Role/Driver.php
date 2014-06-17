<?php

namespace PHPixie\Auth\Role;

/**
 * An interface for role strategies
 *
 * @package    Auth
 */
abstract class Driver {
	
	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;
	
	/**
	 * Constructs a role driver for the specified configuration
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param string $config Name of the configuration.
	 */
	public function __construct($pixie, $config) {
		$this->pixie = $pixie;
	}
	
	/**
	 * Checks if the user belongs to the specified role.
	 * 
	 * @param \PHPixie\ORM\Model $user User to check the role for
	 * @param string $role Role name to check for
	 * @return bool If the user belongs to the specified role
	 */
	public abstract function has_role($user, $role);
}