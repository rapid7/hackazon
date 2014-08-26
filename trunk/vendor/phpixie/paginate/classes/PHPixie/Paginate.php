<?php
namespace PHPixie;

/**
 * Pagination Module for PHPixie.
 * You can use this module to quickly split your content into pages.
 * It can generate page urls using patterns, routes and callbacks.
 *
 * This module is not included by default, install it using Composer
 * by adding
 * <code>
 * 		"phpixie/paginate": "2.*@dev"
 * </code>
 * to your requirement definition. Or download it from
 * https://github.com/dracony/PHPixie-Paginate
 * 
 * To enable it add it to your Pixie class' modules array:
 * <code>
 * 		protected $modules = array(
 * 			//Other modules ...
 * 			'paginate' => '\PHPixie\Paginate',
 * 		);
 * </code>
 *
 *
 * @link https://github.com/dracony/PHPixie-Paginate Download this module from Github
 * @package    Paginate
 */
class Paginate {
	
	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;
	
	/**
	 * Initializes the Pagination module
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 */
	public function __construct($pixie) {
		$this->pixie = $pixie;
		$pixie->assets_dirs[] = dirname(dirname(dirname(__FILE__))).'/assets/';
	}
	
	/**
	 * Creates a pager for an ORM model
	 *
	 * You can optionally manually define a URL to be used
	 * for the first page instead of using a generated one.
	 *
	 * @param \PHPixie\ORM\Model $model ORM Model to paginate
	 * @pager integer $page Current page
	 * @pager integer $page_size Number of items per page
	 * @pager string $first_page_url URL of the first page
	 * @return \PHPixie\Paginate\Pager\ORM ORM Pager
	 */
	public function orm($model, $page, $page_size, $first_page_url = null) {
		return new \PHPixie\Paginate\Pager\ORM($this->pixie, $model, $page, $page_size, $first_page_url);
	}
}
