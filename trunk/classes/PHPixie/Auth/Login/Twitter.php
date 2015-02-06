<?php

namespace PHPixie\Auth\Login;

/**
 * Twitter login provider
 *
 * @package    Auth
 */
class Twitter extends Provider {

    const URL_SEPARATOR = '&';
    const REQUEST_TOKEN_URL = 'https://api.twitter.com/oauth/request_token';
    const ACCESS_TOKEN_URL = 'https://api.twitter.com/oauth/access_token';
    const ACCOUNT_DATA_URL = 'https://api.twitter.com/1.1/users/show.json';

    /**
     * API key Twitter Application
     * @var string
     */
    protected $oauth_consumer_key;

    /**
     * API secret Twitter Application
     */
    protected $oauth_consumer_secret;


    protected $oauth_signature_method;


    protected $oauth_version;


    protected $oauth_token;
    protected $oauth_token_secret;
    protected $screen_name;



    /**
     * Field in the users table where the users
     * twitter id is stored.
     * @var string
     */
    protected $twid_field;

    /**
     * Users access token
     * @var string
     */
    public $access_token;

    /**
     * Token expiry time
     * @var int
     */
    public $token_expires;

    /**
     * Session key to store the token in
     * @var string
     */
    protected $access_token_key;

    /**
     * Session key to store token expiry time in
     * @var string
     */
    protected $token_expires_key;

    /**
     * Name of the login provider
     * @var string
     */
    protected $name = 'twitter';

    /**
     * Constructs password login provider for the specified configuration.
     *
     * @param \PHPixie\Pixie $pixie Pixie dependency container
     * @param \PHPixie\Pixie\Service $service Service instance that this login provider belongs to.
     * @param string $config Name of the configuration
     */
    public function __construct($pixie, $service, $config) {
        parent::__construct($pixie, $service, $config);
        $this->oauth_consumer_key = $pixie->config->get($this->config_prefix."oauth_consumer_key");
        $this->oauth_consumer_secret = $pixie->config->get($this->config_prefix."oauth_consumer_secret");
        $this->oauth_signature_method = $pixie->config->get($this->config_prefix."oauth_signature_method");
        $this->oauth_version = $pixie->config->get($this->config_prefix."oauth_version");
        $this->twid_field = $pixie->config->get($this->config_prefix."twid_field");

        $this->access_token_key = "auth_{$config}_twitter_token";
        $this->token_expires_key = "auth_{$config}_twitter_token_expires";
    }

    /**
     * Attempts to log the user in using his access token.
     *
     * @param string $access_token Users access token
     * @param int $token_lifetime Amount of seconds until the token expires
     * @return bool If the user exists.
     */
    public function login($access_token, $token_lifetime = null) {

        $user = json_decode($this->getTwitterUser($access_token));
        if(isset($user->id)){
            $user = $this->service->user_model()->where($this->twid_field, $user->id)->find();
            if ($user->loaded()) {
                $this->set_user($user, $access_token, $token_lifetime);
                return true;
            }
        }

        return false;
    }

    /**
     * Sets the user logged in via this provider.
     * Stores users id, his token and token expiry time
     * inside the session.
     *
     * @param \PHPixie\ORM\Model $user Logged in user
     * @param string $access_token Users access token
     * @param int $token_lifetime Token lifetime
     * @return void
     */
    public function set_user($user, $access_token = null, $token_lifetime = null) {
        parent::set_user($user);

        if ($access_token){
            $this->access_token = $access_token;
            $this->token_expires = $token_lifetime?(time() + $token_lifetime):null;
        }

        $this->pixie->session->set($this->access_token_key, $this->access_token);
        $this->pixie->session->set($this->token_expires_key, $this->token_expires);
    }

    /**
     * Performs user logout.
     * Also clears session variables associated with
     * the users access token.
     *
     * @return void
     */
    public function logout() {
        parent::logout();
        $this->pixie->session->remove($this->access_token_key);
        $this->pixie->session->remove($this->token_expires_key);
    }



    /**
     * Checks if the user is logged in with twitter, if so
     * notifies the associated Service instance about it.
     *
     * @return bool If the user is logged in
     */
    public function check_login() {

        if (parent::check_login()) {
            $this->access_token = $this->pixie->session->get($this->access_token_key);
            $this->token_expires = $this->pixie->session->get($this->token_expires_key);
            return true;
        }

        return false;
    }

    /**
     * Returns login url for the server-side twitter login flow.
     *
     * @param string $state A persistent code to prevent CSRF
     * @param string $return_url URL to return the user after he authorizes the app.
     * @param string $display_mode Determines the twitter page look.
     *                             Can be either 'page' or 'popup'
     * @return string Login url.
     */
    public function login_url($state, $return_url, $timestamp/*,$display_mode*/) {

        $url = self::REQUEST_TOKEN_URL
        ."?oauth_callback={$return_url}"
        ."&oauth_consumer_key={$this->oauth_consumer_key}"
        ."&oauth_nonce={$state}"
        ."&oauth_signature=".urlencode($this->getSignature($state,$return_url,$timestamp))
        ."&oauth_signature_method={$this->oauth_signature_method}"
        ."&oauth_timestamp={$timestamp}"
        ."&oauth_version={$this->oauth_version}";

        $response = $this->request($url);

        parse_str($response, $response);

        $this->oauth_token = $response['oauth_token'];
        $this->oauth_token_secret = $response['oauth_token_secret'];
        return 'https://api.twitter.com/oauth/authorize?oauth_token='.$this->oauth_token;
    }

    private function getSignature($state, $return_url,$timestamp){

        $token_params = array(
            'oauth_callback='.urlencode($return_url).self::URL_SEPARATOR,
            'oauth_consumer_key='.$this->oauth_consumer_key.self::URL_SEPARATOR,
            'oauth_nonce='.$state.self::URL_SEPARATOR,
            'oauth_signature_method='.$this->oauth_signature_method.self::URL_SEPARATOR,
            'oauth_timestamp='.$timestamp.self::URL_SEPARATOR,
            'oauth_version='.$this->oauth_version
        );

        $oauth_base_text = implode('', array_map('urlencode', $token_params));
        $key = $this->oauth_consumer_secret.self::URL_SEPARATOR;
        $oauth_base_text = 'GET'.self::URL_SEPARATOR.urlencode(self::REQUEST_TOKEN_URL).self::URL_SEPARATOR.$oauth_base_text;
        return base64_encode(hash_hmac('sha1', $oauth_base_text, $key, true));
    }

    /**
     * Exchanges OAuth code for the access token.
     *
     * @param string $code OAuth code
     * @param string $return_url URL to return the user after he authorizes the app.
     * @return array Parsed result of the facebook call.
     */
    public function exchange_code($state, $timestamp, $oauth_token, $oauth_verifier) {

        $params = array(
            'oauth_consumer_key='.$this->oauth_consumer_key.self::URL_SEPARATOR,
            'oauth_nonce='.$state.self::URL_SEPARATOR,
            'oauth_signature_method='.$this->oauth_signature_method.self::URL_SEPARATOR,
            'oauth_token='.$oauth_token.self::URL_SEPARATOR,
            'oauth_timestamp='.$timestamp.self::URL_SEPARATOR,
            'oauth_verifier=' . $oauth_verifier .self::URL_SEPARATOR,
            'oauth_version='.$this->oauth_version
        );

        $key = $this->oauth_consumer_secret.self::URL_SEPARATOR.$this->oauth_token_secret;
        $oauth_base_text = 'GET'.self::URL_SEPARATOR.urlencode(self::ACCESS_TOKEN_URL).self::URL_SEPARATOR . implode('', array_map('urlencode', $params));
        $oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        $params = array(
            'oauth_nonce='.$state,
            'oauth_signature_method='.$this->oauth_signature_method,
            'oauth_timestamp='.$timestamp,
            'oauth_consumer_key='.$this->oauth_consumer_key,
            'oauth_token='.urlencode($oauth_token),
            'oauth_verifier=' . urlencode($oauth_verifier),
            'oauth_signature=' . urlencode($oauth_signature),
            'oauth_version='.$this->oauth_version
        );

        $url = self::ACCESS_TOKEN_URL.'?'.implode('&', $params);
        $response = $this->request($url);
        parse_str($response, $params);
        return $params;
    }

    public  function getTwitterUser($access_token){
        $this->oauth_token = $access_token['oauth_token'];
        $this->oauth_token_secret = $access_token['oauth_token_secret'];
        $this->screen_name = $access_token['screen_name'];
        $state = md5(uniqid(rand(), true));
        $timestamp = time();

        $params = array(
            'oauth_consumer_key='.$this->oauth_consumer_key.self::URL_SEPARATOR,
            'oauth_nonce='.$state.self::URL_SEPARATOR,
            'oauth_signature_method='.$this->oauth_signature_method.self::URL_SEPARATOR,
            'oauth_timestamp='.$timestamp.self::URL_SEPARATOR,
            'oauth_token='.$this->oauth_token.self::URL_SEPARATOR,
            'oauth_version='.$this->oauth_version.self::URL_SEPARATOR,
            'screen_name='.$this->screen_name
        );

        $key = $this->oauth_consumer_secret.'&'. $this->oauth_token_secret;
        $oauth_base_text = 'GET'.self::URL_SEPARATOR .urlencode(self::ACCOUNT_DATA_URL).self::URL_SEPARATOR.implode('', array_map('urlencode', $params));
        $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        $params = array(
            'oauth_consumer_key='.$this->oauth_consumer_key,
            'oauth_nonce='.$state,
            'oauth_signature='.urlencode($signature),
            'oauth_signature_method='.$this->oauth_signature_method,
            'oauth_timestamp='.$timestamp,
            'oauth_token=' . urlencode($this->oauth_token),
            'oauth_version='.$this->oauth_version,
            'screen_name=' . $this->screen_name
        );

        $url = self::ACCOUNT_DATA_URL.'?'.implode(self::URL_SEPARATOR, $params);
        return $this->request($url);
    }

    /**
     * Requests a url using CURL
     *
     * @param string $url URL to fetch
     * @param string $return_url URL to return the user after he authorizes the app.
     * @return array Parsed result of the facebook call.
     * @throws \Exception If the request failed
     */
    public function request($url) {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_HTTPHEADER     => array('Expect:'),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_URL            => $url
        ));
        $response = curl_exec($ch);
        if($response === false)
            throw new \Exception("URL request failed:".curl_error($ch));

        return $response;
    }
}