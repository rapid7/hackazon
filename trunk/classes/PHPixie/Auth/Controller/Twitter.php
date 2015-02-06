<?php

namespace PHPixie\Auth\Controller;

/**
 * Abstract Twitter login controller. To use it you need to extend this class
 * and override the new_user() method, which handles the situation when a user
 * logs in with your app for the very first time (basically youi need to register him
 * at that point).
 *
 * It can be used both for popup login and page login.
 * To use the page login, make a link pointing to the controllers 'index' action,
 * for pupup login open a popup that points to its 'popup' action.
 * Optionally you can pass a ?return_url =<url> parameter to specify where to redirect the
 * user after he is logged in. You can also specify a default redirect url in the auth.php config file.
 */
abstract class Twitter extends \PHPixie\Controller {

    /**
     * Twitter login provider to log the user in
     * @var \PHPixie\Auth\Login\Twitter
     */
    protected $provider;

    /**
     * Session key to save the CSRF protection state value.
     * @var string
     */
    protected $state_key;

    /**
     * Session key to save return URL in.
     * @var string
     */
    protected $return_url_key;

    /**
     * Default url to return to.
     * @var string
     */
    protected $default_return_url;

    /*
     * Initializes the controller oarameters
     *
     * @return void
     */
    public function before() {
        $config = $this->request->param('config', 'default');
        $this->provider = $this->pixie->auth->provider('twitter', $config);
        $this->default_return_url = $this->pixie->config->get("auth.{$config}.login.twitter.return_url", null);
        $this->state_key = "auth_{$config}_twitter_state";
        $this->return_url_key = "auth_{$config}_twitter_return";
    }

    /*
     * Used to login user using a popup window.
     *
     * @return void
     */
    public function action_popup() {
        $this->handle_request('popup');
    }

    /**
     * Used to login user using the 'page' mode.
     * E.g redirecting him to twitter and back.
     *
     * @return void
     */
    public function action_index() {
        $this->handle_request('page');
    }

    /**
     * Handles facebook login.
     *
     * @param string $display_mode Display mode of the facebook login.
     *                             Either 'page' or 'popup'
     * @return void
     */
    public function handle_request($display_mode) {

        if ($error = $this->request->get('error'))
            return $this->error($display_mode);

        $state = md5(uniqid(rand(), true));
        $timestamp = time();

        if (!empty($_GET['oauth_token']) && !empty($_GET['oauth_verifier'])){
            $params = $this->provider->exchange_code($state, $timestamp,$_GET['oauth_token'],$_GET['oauth_verifier']);
            $this->pixie->session->remove($this->state_key);

            return $this->success($params, $display_mode);
        }

        $this->pixie->session->set($this->state_key, $state);
        $return_url = $this->request->get('return_url', $this->default_return_url);

        if (!$return_url && $display_mode == 'page'){
            $return_url = $this->request->server('HTTP_REFERER');
            if (empty($return_url))
                $return_url = 'http://'.$this->request->server('HTTP_HOST');
        }

        $this->pixie->session->set($this->return_url_key, $return_url);
        $url = $this->provider->login_url($state, $this->request->url(), $timestamp /*, $display_mode*/);
        $this->response->redirect($url);
    }

    /**
     * Called upon error received from twitter. E.g. if the user declines access.
     *
     * @param string $display_mode Display mode of the facebook login.
     *                             Either 'page' or 'popup'
     * @return void
     */
    public function error($display_mode) {

        $this->pixie->session->remove($this->state_key);
        $this->return_to_url($display_mode);
    }


    public function success($access_token, $display_mode) {

        $return_url = $this->pixie->session->get($this->return_url_key);
        if ($this->provider->login($access_token)) {
            $this->return_to_url($display_mode, $return_url);
        }else {
            $this->new_user($access_token, $return_url, $display_mode);
        }
    }

    /**
     * Handles redirecting the user for both 'page' and 'popup' modes.
     * If the $return_url is empty closes the popup and refreshes the parent window
     * (for popup display mode), or redirects the user to '/' (for page display mode).
     *
     * @param string $display_mode Display mode of the facebook login.
     *                             Either 'page' or 'popup'
     * @param string $return_url Return URL to redirect the user after.
     * @return void
     */
    public function return_to_url($display_mode, $return_url = null) {
        if ($display_mode == 'popup') {
            $view = $this->pixie->view('auth/oauth');
            $view->return_url = $return_url;
            $this->response-> body = $view->render();
        }else {
            if ($return_url == null)
                $return_url = $this->pixie->basepath;
            $this->response->redirect($return_url);
        }
    }

    /**
     * Handles the situation when a new user logs in.
     * Usually you will have to add him tot he database at this point,
     *
     * @param string $access_token Users access token
     * @param string $return_url Return URL to redirect the user after.
     * @param string $display_mode Display mode of the facebook login.
     *                             Either 'page' or 'popup'
     * @return void
     */
    public abstract function new_user($access_token, $return_url, $display_mode);

}
