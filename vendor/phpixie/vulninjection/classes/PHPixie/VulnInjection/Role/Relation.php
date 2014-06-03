<?php

namespace PHPixie\Auth\Role;

/**
 * Manages roles based on an ORM relationship.
 * Supports belongs_to and has_many relationships.
 *
 * @package    Auth
 */
class Relation extends Driver {

	/**
	 * Name of the role relation
	 * @var string
	 */
	protected $relation;
	
	/**
	 * Name of the field holding role name.
	 * @var string
	 */
	protected $name_field;
	
	/**
	 * Relationship type. 
	 * Either belongs_to or has_many
	 * @var string
	 */
	protected $type;
	
	/**
	 * Constructs this role strategy for the specified configuration.
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param string $config Name of the configuration
	 */
	public function __construct($pixie, $config) {
		parent::__construct($pixie, $config);
		$this->relation = $pixie->config->get("auth.{$config}.roles.relation");
		$this->name_field = $pixie->config->get("auth.{$config}.roles.name_field");
		$this->type = $pixie->config->get("auth.{$config}.roles.type");
	}
	
	/**
	 * Checks if the user belongs to the specified role.
	 * 
	 * @param \PHPixie\ORM\Model $user User to check the role for
	 * @param string $role Role name to check for
	 * @return bool If the user belongs to the specified role
	 * @throws \Exception If the relationship type is not belongs_to or has_many
	 */
	public function has_role($user, $role) {
		$relation = $this->relation;
		$field = $this->name_field;
		
		if($this->type == 'has_many')
			return $user->$relation
					->where($this->name_field, $role)
					->count_all() > 0;
					
		if ($this->type == 'belongs_to')
			return $user->$relation->$field == $role;

		throw new \Exception("The relationship must be either of has_many or has_one type");
	}
}
