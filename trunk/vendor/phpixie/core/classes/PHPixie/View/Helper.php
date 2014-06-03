<?php

namespace PHPixie\View;

/**
 * View helper class.
 * An instance of this class is passed automatically
 * to every View.
 *
 * You can extend it to make your own methods available in view templates.
 *
 * @package Core
 */
class Helper {

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	*/
	protected $pixie;
	
	/**
	 * Constructs the view helper
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 */
	public function __construct($pixie) {
		$this->pixie = $pixie;
		
	}
	
	/**
	 * List of aliases to create for methods
	 * @var array
	 */
	protected $aliases = array(
		'_' => 'output'
	);
	
	/**
	 * Gets the array of aliases to helper methods
	 * 
	 * @return array Associative array of aliases mapped to their methods
	 */
	public function get_aliases() {
		$aliases = array();
		foreach($this->aliases as $alias => $method)
			$aliases[$alias] = array($this, $method);
		return $aliases;
	}
	
	/**
	 * Escapes string to safely display HTML entities
	 * like < > & without breaking layout and prevent XSS attacks.
	 *
	 * @param string $str String to escape
	 * @return string Escaped string.
	 */
	public function escape($str) {
		return htmlentities($str);
	}
	
	/**
	 * Escapes and prints a string.
	 *
	 * @param string $str String to escape
	 * @see \PHPixie\View\Helper::escape
	 */
	public function output($str) {
		echo $this->escape($str);
	}
}
