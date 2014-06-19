<?php

namespace PHPixie\Auth\Login;

/**
 * Password login provider using salted password hashes.
 */
class Password extends Provider {

	/**
	 * Field in the users table where the users
	 * login is stored.
	 * @var string
	 */
	protected $login_field;

	/**
	 * Field in the users table where the users
	 * password is stored.
	 * @var string
	 */
	protected $password_field;

	/**
	 * Field in the users table where the users
	 * persistant login token is stored.
	 * @var string
	 */
	protected $login_token_field;

	/**
	 * Hash algorithm to use. If not set
	 * the passwords are saved without hashing.
	 * @var string
	 */
	protected $hash_method;

	/**
	 * Name of the login provider
	 * @var string
	 */
	protected $name = 'password';

	/**
	 * Lifetime of the login token cookie
	 * @var integer
	 */
	protected $login_token_lifetime;

	/**
	 * Allow multiple login from multiple browsers/computers for a same user
	 * @var bool
	 */
	protected $allow_multiple_login = false;

	/**
	 * Constructs password login provider for the specified configuration.
	 *
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param \PHPixie\Pixie\Service $service Service instance that this login provider belongs to.
	 * @param string $config Name of the configuration
	 */
	public function __construct($pixie, $service, $config) {
		parent::__construct($pixie, $service, $config);
		$this->login_field = $pixie->config->get($this->config_prefix."login_field");
		$this->password_field = $pixie-> config->get($this->config_prefix."password_field");
		$this->hash_method = $pixie-> config->get($this->config_prefix."hash_method", 'md5');
		
		
		$this->login_token_field = $pixie->config->get($this->config_prefix."login_token_field", null);
		if ($this->login_token_field) {
			$this->login_token_lifetime = $pixie->config->get($this->config_prefix."login_token_lifetime", 604800);
		}
	}

	/**
	 * Checks if the user is logged in.
	 *
	 * @see PHPixie\Auth\Login\Provider::check_login()
	 * @return bool If the user is logged in
	 */
	public function check_login() {
		if (parent::check_login())
			return true;
			
		$token = $this->pixie->cookie->get('login_token', null);
		if ($token === null)
			return false;
			
		return $this->login_token($token);
	}
	
	/**
	 * Attempts to log the user in using his login and password
	 *
	 * @param string $login Users login
	 * @param string $password Users password
	 * @param bool   $persist_login Whether to persist users login.
	 *                              Defalts to false.
	 * @return bool If the user exists.
	 */
	public function login($login, $password, $persist_login = false) {
		$user = $this->service->user_model()
						->where($this->login_field, $login)
						->find();
		if($user->loaded()){
			$password_field = $this->password_field;
			$challenge = $user->$password_field;

			if($this->hash_method && 'crypt'==$this->hash_method) {
				if (function_exists('password_verify')) { // PHP 5.5.0+
					$password = password_verify($password, $challenge)?$challenge:false;
				} else {
					$password = crypt($password, $challenge);
				}
			} elseif($this->hash_method) {
				$salted = explode(':', $challenge);
				$password = hash($this->hash_method, $password.$salted[1]);
				$challenge = $salted[0];
			}
			if ($challenge === $password) {
				$this->set_user($user);
				if ($this->pixie->cookie->get('login_token', null))
					$this->pixie->cookie->remove('login_token');
				if ($persist_login) {
					$token_field = $this->login_token_field;
					if (empty($token_field))
						throw new \Exception("Option 'login_token_field' not set");
					
					$token = $this->get_valid_token($user);
					
					$salt = $this->random_string();
					$user_token = crypt($token, '$2y$10$'.$salt);
					$cookie = $login . ':' . $user_token;
				    $this->pixie->cookie->set('login_token', $cookie, $this->login_token_lifetime);
				}

				return true;
			}
		}
		return false;
	}
	
	/**
	 * Regenerates users login token
	 *
	 * @param \PHPixie\ORM\Model $user User model to update
	 * @return string Generated token
	 */
	public function regenerate_login_token($user) {
		$token_field = $this->login_token_field;
		$token = $this->random_string().':'.time();
		$user->$token_field = $token;
		$user->save();
		return $token;
	}
	
	/**
	 * Checks if the current generated token is still valid
	 *
	 * @param \PHPixie\ORM\Model $user User model to check
	 * @return string Valid token
	 */
	public function get_valid_token($user) {
		$token_field = $this->login_token_field;
		$token = $user->$token_field;
		$split_token = explode(':', $token);
		
		if (count($split_token) !==2 || $split_token[1] < time() - $this->login_token_lifetime)
			$split_token = explode(':', $this->regenerate_login_token($user));
		return $split_token[0];
	}
	
	/**
	 * Login user by token
	 *
	 * @param bool $token Token to verify the user against
	 * @return bool If the user exists.
	 */
	public function login_token($user_token)
	{
		$user_token = explode(':', $user_token, 2);
		if (count($user_token) !== 2)
			return false;
		
		$login = $user_token[0];
		$user = $this->service->user_model()
						->where($this->login_field, $login)
						->find();
						
		if (!$user->loaded())
			return false;
		
        $db_token = $this->get_valid_token($user);
		if ($user_token[1] === crypt($db_token, $user_token[1])) {
			$this->service->set_user($user, $this->name);
			return true;
		}
		return false;
	}

	/**
	 * Logs the user out
	 *
	 */
	public function logout() {
		if ($this->pixie->cookie->get('login_token', null))
			$this->pixie->cookie->remove('login_token');
		return parent::logout();	
	}
	
	/**
	 * Hashes the password using the configured method
	 *
	 * @param string $password Password to hash
	 * @return string Hashed password
	 */
	public function hash_password($password){
		if(!$this->hash_method)
			return $password;
		if('crypt'==$this->hash_method) {
			if (function_exists('password_hash')) // PHP 5.5.0+
				return password_hash($password, PASSWORD_DEFAULT);
			$salt = $this->random_string();
			return crypt($password, '$2y$10$'.$salt);
		}
		$salt = uniqid(rand());
		return hash($this->hash_method, $password.$salt).':'.$salt;
	}
	
	/**
	 * Generates random string
	 *
	 * @return string Random string
	 */
	public function random_string() {
		return str_replace(array('+', '='), array('.', ''),
			        base64_encode(pack('N9', mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand())));
	}
}
