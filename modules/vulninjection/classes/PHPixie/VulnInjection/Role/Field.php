<?php

namespace PHPixie\Auth\Role;

/**
 * Manages roles based on a field in the users table.
 * The specified field must hold the name of the model
 *
 * @package    Auth
 */
class Field extends Driver {

	/**
	 * Name of the role field
	 * @var string
	 */
	protected $field;
	
	/**
	 * Constructs this role strategy for the specified configuration
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param string $config Name of the configuration
	 */
	public function __construct($pixie, $config) {
		parent::__construct($pixie, $config);
		$this->field = $pixie->config->get("auth.{$config}.roles.field");
	}
	
	/**
	 * Checks if the user belongs to the specified role
	 * 
	 * @param \PHPixie\ORM\Model $user User to check the role for
	 * @param string $role Role name to check for
	 * @return bool If the user belongs to the specified role
	 */
	public function has_role($user, $role) {
		$field = $this->field;
		return $user->$field == $role;
	}
}