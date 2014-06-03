<?php

namespace PHPixie;

/**
 * Handles the response that is sent back to the client.
 * @package Core
 */
class Response
{

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	protected $pixie;
	
	/**
	 * Headers for the response
	 * @var array
	 */
	public $headers = array(
		'Content-Type: text/html; charset=utf-8'
	);

	/**
	 * Response body
	 * @var string
	 */
	public $body;

	/**
	 * Creates a new Response
	 *
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 */
	public function __construct($pixie) {
		$this->pixie = $pixie;
	}
	
	/**
	 * Add header to the response
	 *
	 * @param string $header Header content
	 * @return void
	 */
	public function add_header($header) {
		$this->headers[] = $header;
	}

	/**
	 * Add redirection header
	 *
	 * @param string $url URL to redirect the client to
	 * @return void
	 */
	public function redirect($url) {
		$this->add_header("Location: $url");
	}

	/**
	 * Sends headers to the client
	 *
	 * @return \PHPixie\Response Resturns itself
	 */
	public function send_headers() {
		foreach ($this->headers as $header)
			header($header);
			
		foreach($this->pixie->cookie->get_updates() as $key => $params)
			setcookie($key,
					$params['value'],
					$params['expires'],
					$params['path'],
					$params['domain'],
					$params['secure'],
					$params['http_only']
			);
		
		return $this;
	}

	/**
	 * Send response body to the client
	 *
	 * @return \PHPixie\Response Resturns itself
	 */
	public function send_body() {
		echo $this->body;
		return $this;
	}

}
