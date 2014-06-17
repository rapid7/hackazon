<?php

namespace PHPixie;

/**
 * Handles client request.
 * @package Core
 */
class Request
{

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	protected $pixie;
	
	/**
	 * Stores POST data
	 * @var array
	 */
	protected $_post;

	/**
	 * Stores GET data
	 * @var array
	 */
	protected $_get;

	/**
	 * Stores GET data
	 * @var array
	 */
	protected $_param;
	
	/**
	 * Stores COOKIE data
	 * @var array
	 */
	protected $_cookie;
	
	/**
	 * Current Route
	 * @var Route
	 */
	public $route;

	/**
	 * Request method
	 * @var string
	 */
	public $method;

	/**
	 * Creates a new request
	 *
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param \PHPixie\Route  $route  Route for this request
	 * @param  string $method HTTP method for the request (e.g. GET, POST)
	 * @param  array  $post   Array of POST data
	 * @param  array  $get    Array of GET data
	 * @param  array  $server Array of SERVER data
	 * @return Request Initialized request
	 *
	 */
	public function __construct($pixie, $route, $method = "GET", $post = array(), $get = array(), $param=array(), $server = array(), $cookie = array())
	{
		$this->pixie = $pixie;
		$this->route = $route;
		$this->method = $method;
		$this->_post = $post;
		$this->_get = $get;
		$this->_param = $param;
		$this->_server = $server;
		$this->_cookie = $cookie;
	}

	/**
	 * Retrieves a value by key from a specified array with an optional XSS check.
	 *
	 * @param array  $params  Array to get the key from
	 * @param string $key     Key to retrieve
	 * @param mixed  $default Default value
	 * @param bool   $filter_xss Whether to filter input for XSS protection
	 *                           Defaults to true
	 * @return mixed Returns a value if a key is specified,
	 *               or an array of specifed parameters if it isn't.
	 */
	protected function get_filtered_value($params, $key = null, $default = null, $filter_xss=true)
	{
		if ($key == null)
			return $params;
		$val = $this->pixie->arr($params, $key, $default);
		
		if ($filter_xss)
			return $this->filter_xss($val);
			
		return $val;
	}

	/**
	 * Retrieves a GET parameter
	 *
	 * @param string $key    Parameter key
	 * @param mixed $default Default value
	 * @param bool  $filter_xss Whether to filter input for XSS protection
	 * @return mixed Returns a value if a key is specified,
	 *               or an array of GET parameters if it isn't.
	 */
	public function get($key = null, $default = null, $filter_xss=true)
	{
		return $this->get_filtered_value($this->_get, $key, $default, $filter_xss);
	}

	/**
	 * Retrieves a POST parameter
	 *
	 * @param string $key    Parameter key
	 * @param mixed $default Default value
	 * @param bool  $filter_xss Whether to filter input for XSS protection
	 * @return mixed Returns a value if a key is specified,
	 *               or an array of POST parameters if it isn't.
	 */
	public function post($key = null, $default = null, $filter_xss=true)
	{
		return $this->get_filtered_value($this->_post, $key, $default, $filter_xss);
	}

	/**
	 * Retrieves a URL parameter
	 *
	 * @param string $key    Parameter key
	 * @param mixed $default Default value
	 * @param bool  $filter_xss Whether to filter input for XSS protection
	 * @return mixed Returns a value if a key is specified,
	 *               or an array of POST parameters if it isn't.
	 */
	public function param($key = null, $default = null, $filter_xss=true)
	{
		return $this->get_filtered_value($this->_param, $key, $default, $filter_xss);
	}

	/**
	 * Filters input to prevent XSS attacks.
	 * If an array is passed, filters all its elements recursively.
	 *
	 * @param mixed $val  Input to sanitize.
	 * @return mixed Filtered values
	 */
	public function filter_xss($val) {
		if (is_null($val)) return NULL;
		
		if (is_array($val)) {
			array_walk_recursive($val, function( &$str) {
				$str = strip_tags($str);
			});
		}else {
			$val = strip_tags($val);
		}
		
		return $val;
	}
	
	/**
	 * Retrieves a SERVER parameter
	 *
	 * @param string $key    Parameter key
	 * @param mixed  $default Default value
	 * @return mixed Returns a value if a key is specified,
	 *               or an array of SERVER parameters if it isn't.
	 */
	public function server($key = null, $default = null)
	{
		if ($key == null)
			return $this->_server;
		return $this->pixie->arr($this->_server, $key, $default);
	}

	/**
	 * Initializes the routed Controller and executes specified action
	 *
	 * @return \PHPixie\Response A Response object with the body and headers set
	 */
	public function execute()
	{
		$this->pixie->cookie->set_cookie_data($this->_cookie);
		$class = $this->param('namespace',$this->pixie->app_namespace).'Controller\\'.ucfirst($this->param('controller'));
		$controller = $this->pixie->controller($class);
		$controller->request = $this;
		$controller->run($this->param('action'));
		return $controller->response;
	}

	/**
	 * Gets request URL
	 *
	 * @param bool $with_params Whether to preserve URL parameters
	 * @return string URL of this request
	 */
	public function url($with_params = false) {
		$url = $this->server('HTTPS') == 'on' ? 'https://':'http://';
		$url.= $this->server('HTTP_HOST').$this->server('REQUEST_URI');

		if (!$with_params) {
			$pos = strpos($url, '?');
			if ($pos !== false)
				$url = substr($url, 0, $pos);
		}
		return $url;
	}
	
	/**
	 * Check if the request is ajax
	 *
	 * @return bool True if it's ajax else False
	 */
	public function is_ajax()
	{
		if (strtolower($this->server('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
		   return true;
		}
		return false;
	}
	
}
