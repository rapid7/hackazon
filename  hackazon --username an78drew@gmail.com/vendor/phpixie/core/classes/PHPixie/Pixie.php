<?php

namespace PHPixie;

/**
 * The core of the framework and it's dependancy container.
 * It holds references to all framework wide instances, like Config,
 * Session, Debug etc. Instead of calling a class constructor you call 
 * a wrapping function of this class to construct the object for you.
 * You can extend this class adding porperties that you want to be accessible
 * all around your app.
 *
 * @property-read \PHPixie\Config $config Configuration handler
 * @property-read \PHPixie\Debug $debug Error handler and logger
 * @property-read \PHPixie\Router $router Router
 * @property-read \PHPixie\Session $session Session handler
 */

 class Pixie {
	
  	/**
	 * Instance definitions
	 * @var array
	 */
	protected $instance_classes = array(
		'config'  => '\PHPixie\Config',
		'cookie' => '\PHPixie\Cookie',
		'debug'   => '\PHPixie\Debug',
		'router'  => '\PHPixie\Router',
		'session' => '\PHPixie\Session'
	);
	
 	/**
	 * Instanced classes
	 * @var array
	 */
	protected $instances = array();
	
	/**
	 * Module definitions
	 * @var array
	 */
	protected $modules = array();
	
 	/**
	 * Directories to look for assets in
	 * @var array
	 */
	public $assets_dirs = array();
	
 	/**
	 * Root directory of the application
	 * @var array
	 */
	public $root_dir;
	
	/**
	 * Namespace of the application
	 * @var array
	 */
	public $app_namespace;
	
	/**
	 * Base URL of the application
	 * @var string
	 */
	public $basepath = '/';
	
	/**
	 * Gets a property by name. Returns defined class and module instances
	 *
	 * @param string $name Property namw
	 * @return mixed Instance of defined class or module
	 */
	public function __get($name) {
		if (isset($this->instances[$name]))
			return $this->instances[$name];
			
		if (isset($this->instance_classes[$name]))
			return $this->instances[$name] = new $this->instance_classes[$name]($this);
			
		if (isset($this->modules[$name]))
			return $this->instances[$name] = new $this->modules[$name]($this);
			
		throw new \Exception("Property {$name} not found on ".get_class($this));
	}		

	/**
	 * Constructs a controller by class name
	 *
	 * @param string $class Controller class
	 * @return \PHPixie\Controller
	 * @throw  \PHPixie\Exception\PageNotFound If the controller class is not found
	 */
	public function controller($class) {
		if (!class_exists($class))
			throw new \PHPixie\Exception\PageNotFound("Class {$class} doesn't exist");
			
		return new $class($this);
	}
	
	/**
	 * Constructs a request
	 *
	 * @param  Route  $route  Route for this request
	 * @param  string $method HTTP method for the request (e.g. GET, POST)
	 * @param  array  $post   Array of POST data
	 * @param  array  $get    Array of GET data
	 * @param  array  $server Array of SERVER data
	 * @param  array  $cookie Array of COOKIE data
	 * @return \PHPixie\Request
	 */
	public function request($route, $method = "GET", $post = array(), $get = array(), $param=array(), $server = array(), $cookie = array()) {
		return new \PHPixie\Request($this, $route, $method, $post, $get, $param, $server, $cookie);
	}
	
	/**
	 * Constructs a response
	 *
	 * @return \PHPixie\Response
	 */
	public function response() {
		return new \PHPixie\Response($this);
	}
	
	/**
	 * Constructs a route
	 *
	 * @param string $name Name of the route
	 * @param mixed $rule Rule for this route
	 * @param array $defaults Default parameters for the route
	 * @param mixed $methods Methods to restrict this route to.
	 *                       Either a single method or an array of them.
	 * @return \PHPixie\Route
	 */
	public function route($name, $rule, $defaults, $methods = null) {
		return new \PHPixie\Route($this->basepath, $name, $rule, $defaults, $methods);
	}
	
	/**
	 * Constructs a view
	 *
	 * @param string   $name The name of the template to use
	 * @return \PHPixie\View
	 */
	public function view($name) {
		return new \PHPixie\View($this, $this->view_helper(), $name);
	}
	
	/**
	 * Constructs a view helper
	 *
	 * @return \PHPixie\View\Helper
	 */
	public function view_helper() {
		return new \PHPixie\View\Helper($this);
	}
	
	/**
	 * Retrieve value from array by key, with default value support.
	 *
	 * @param array  $array   Input array
	 * @param string $key     Key to retrieve from the array
	 * @param mixed  $default Default value to return if the key is not found
	 * @return mixed An array value if it was found or default value if it is not
	 */
	public function arr($array, $key, $default = null)
	{
		if (isset($array[$key]))
			return $array[$key];
		return $default;
	}
	
	/**
	 * Finds full path to a specified file in the /assets folders.
	 * It will search in the application folder first, then in all enabled modules
	 * and then the /assets folder of the framework.
	 *
	 * @param string  $subfolder  Subfolder to search in e.g. 'classes' or 'views'
	 * @param string  $name       Name of the file without extension
	 * @param string  $extension  File extension
	 * @param boolean $return_all If 'true' returns all mathced files as array,
	 *                            otherwise returns the first file found
	 * @return mixed  Full path to the file or False if it is not found
	 */
	public function find_file($subfolder, $name, $extension = 'php', $return_all = false)
	{
		
		$fname = $name.'.'.$extension;
		$found_files = array();
		foreach ($this->assets_dirs as $folder)
		{
			$file = $folder.$subfolder.'/'.$fname;
			if (file_exists($file))
			{
				if (!$return_all)
					return($file);
					
				$found_files[] = $file;
			}
		}
		
		if (!empty($found_files))
			return $found_files;

		return false;
	}
	
	/**
	 * Creates a Request representing current HTTP request.
	 *
	 * @return \PHPixie\Request
	 */
	public function http_request()
	{
		$uri = $_SERVER['REQUEST_URI'];
		$uri = preg_replace("#^{$this->basepath}(?:index\.php/?)#i", '/', $uri);
		$url_parts = parse_url($uri);
		$route_data = $this->router->match($url_parts['path'], $_SERVER['REQUEST_METHOD']);
		return $this->request($route_data['route'], $_SERVER['REQUEST_METHOD'], $_POST, $_GET, $route_data['params'], $_SERVER, $_COOKIE);
	}
	
	/**
	 * Processes HTTP request, executes it and sends back the response.
	 *
	 * @return void
	 */
	public function handle_http_request() {
		try {
		
			$request =  $this->http_request();
			$response = $request->execute();
			$response->send_headers()->send_body();
			
		}catch (\Exception $e) {
			$this->handle_exception($e);
		}
		
	}
	
	/**
	 * Exception handler. By default displays the error page.
	 * If you want your exceptions to be handled in a specific way
	 * you should override this method.
	 *
	 * @param \Exception $exception Exception to handle
	 * @return void
	 */
	public function handle_exception($exception) {
		$this->debug->render_exception_page($exception);
	}
	
	/**
	 * Register assets directories
	 * 
	 * @return void
	 */
	protected function set_asset_dirs() {
		$this->assets_dirs = array(
			$this->root_dir.'assets/',
			dirname(dirname(dirname(__FILE__))).'/assets/'
		);
	}
	
	/**
	 * Bootstraps the project
	 *
	 * @param  string $root_dir Root directory of the application
	 * @return void
	 */
	public function bootstrap($root_dir) {
		if (substr($root_dir, -1) != '/')
			$root_dir.= '/';
			
		$this->root_dir = $root_dir;
		
		if ($this->app_namespace === null) {
			$class_name = get_class($this);
			$this->app_namespace = substr($class_name, 0, strpos($class_name, "\\")+1);
		}

		$this->set_asset_dirs();

		$this->debug->init();
		foreach($this->modules as $name=>$class) {
			$this->$name = new $class($this);
		}
		foreach($this->config->get('routes') as $name => $rule) 
			$this->router->add($this->route($name, $rule[0], $rule[1], $this->arr($rule, 2, null)));
			
		$this->after_bootstrap();
		
		return $this;
	}
	
	/**
	 * Perform some initialization after bootstrap finished
	 *
	 * @return void
	 */
	protected function after_bootstrap() {}
	
		
}
