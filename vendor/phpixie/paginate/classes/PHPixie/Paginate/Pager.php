<?php

namespace PHPixie\Paginate;

/**
 * Abstract Pager class. 
 * You can paginate any kind of content by extending this class
 *
 * @package Paginate
 */
abstract class Pager {

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	protected $pixie;

	/**
	 * Current page number
	 * @var integer
	 */
	public $page;
	
	/**
	 * Number of items per page
	 * @var integer
	 */
	public $page_size;
	
	/**
	 * Total number of pages
	 * @var integer
	 */
	public $num_pages;
	
	/**
	 * Total number of items
	 * @var integer
	 */
	public $num_items;
	
	/**
	 * Optional URL for the first page
	 * @var string
	 */
	public $first_page_url;

	/**
	 * Items to paginate
	 * @var mixed
	 */
	protected $items;
	
	/**
	 * Offset of the first item of the current page
	 * @var integer
	 */
	protected $offset;
	
	/**
	 * Pattern to use for URL generation
	 * @var string
	 */
	protected $pattern;
	
	/**
	 * Callback function to use for URL generation
	 * @var callable
	 */
	protected $callback;
	
	/**
	 * Initializes a pager
	 *
	 * You can optionally manually define a URL to be used
	 * for the first page instead of using a generated one.
	 *
	 * @param mixed $items Items to paginate
	 * @pager integer $page Current page
	 * @pager integer $page_size Number of items per page
	 * @pager string $first_page_url URL of the first page
	 */
	public function __construct($pixie, $items, $page, $page_size, $first_page_url = null) {
		$this->pixie = $pixie;
		$this->items =  $items;
		$this->page = $page;
		$this->page_size = $page_size;
			
		$this->offset = $page_size * ($page - 1);
		$this->num_items = $this->item_count();
		$this->num_pages = ceil($this->num_items / $page_size);
		
		$this->first_page_url = $first_page_url;
	}

	/**
	 * Sets a pattern for generating page URLs.
	 * 
	 * The pattern should be a generic page URL with #page#
	 * substituted in place where the page number should be,
	 * e.g. /page-#page#
	 *
	 * @param string $pattern Page URL pattern
	 */	
	public function set_url_pattern($pattern) {
		$this->pattern = $pattern;
	}
	
	/**
	 * Sets a route for generating page URLs.
	 * 
	 * @param string $name Name of the route to use
	 * @param array $params Parameters to pass to the route
	 * @param $page_param Name of the route parameter that represents page number. 
	 *                    Defauls to 'page'.
	 */	
	public function set_url_route($name, $params = array(), $page_param = 'page') {
		$route = $this->pixie->router->get($name);
		$params[$page_param] = '#page#';
		$this->pattern = $route->url($params);
	}

	/**
	 * Sets a callback function for generating page URLs.
	 *
	 * The function should accept a single $page parameter,
	 * which is the number of the page to generate the URL for,
	 * and a return a string URL.
	 * 
	 * @param callable $callback Function to be used for URL generation
	 */	
	public function set_url_callback($callback) {
		$this->callback = $callback;
	}
	
	/**
	 * Get a url for a specific page
	 *
	 * @param integer $page Page number
	 * @return string Page URL
	 */
	public function url($page) {
		if ($page == 1 && $this->first_page_url !== null)
			return $this->first_page_url;
			
		return $this->generate_url($page);
	}

	/**
	 * Generate a url for a specific page
	 *
	 * @param integer $page Page number
	 * @return string Page URL
	 * @throws \Exception If neither a pattern, route nor a callback for url generation has been set
	 */
	protected function generate_url($page) {
		if ($this->pattern)
			return str_replace('#page#', $page, $this->pattern);
			
		if ($this->callback)
			return call_user_func($this->callback, $page);
			
		throw new \Exception("Neither a pattern, route nor a callback for url generation was set");
	}
	
	/**
	 * Get the items for the current page
	 *
	 * @return mixed Items for the current page
	 */
	public abstract function current_items();
	
	/**
	 * Get the total number of items
	 *
	 * @return integer Total number of items
	 */
	protected abstract function item_count();
}
