<?php

namespace PHPixie;

/**
 * Vulnerabilities injection module for PHPixie.
 *
 * to your requirement definition. Or download it from
 * https://github.com/dracony/PHPixie-Auth
 * 
 * To enable it add it to your Pixie class' modules array:
 * <code>
 * 		protected $modules = array(
 * 			//Other modules ...
 * 			'vulninjection' => '\PHPixie\VulnInjection',
 * 		);
 * </code>
 *
 * This modules let's you inject vulnerabilities into custom pages.
 * @package    VulnInjection
 */
class VulnInjection {

	/**
	 * Pixie Dependency Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;
	
	/**
	 * Array of initialized \PHPixie\VulnInjection\Service instances
	 * @var array
	 */
	protected $_services;
        
	/**
	 * Constructs an VulnInjection instance for the specified configuration
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
	 * @return \PHPixie\VulnInjection\Service  VulnInjection Service
	 */
	public function build_service($config) {
		return new \PHPixie\VulnInjection\Service($this->pixie, $config);
	}
	

	
}
