<?php

namespace VulnModule\VulnInjection;

use App\Exception\ForbiddenException;
use App\Page;
use App\Pixie;
use PHPixie\Auth\Login\Provider;
use PHPixie\Auth\Role\Driver;
use PHPixie\ORM\Model;
use VulnModule\Config;
use VulnModule\Config\Context;
use VulnModule\Csrf\CsrfTokenManager;

/**
 * Authorization Service.
 * @package    Auth
 */
class Service {

    const PATTERN_XSS = '/<script.+?<\\/script>|\s+?on\w+=([\'"])[^\\1]*\\1|href=([\'"])javascript:[^\\2]*\\2/i';

    /**
     * Pixie Dependency Container
     * @var Pixie
     */
    public $pixie;

    /**
     * Name of the ORM model that represents a user 
     * @var string
     */
    public $settings;

    /**
     * Name of controller settings
     * @var string
     */
    public $controllerSettings;

    /**
     * @var null|Config
     */
    protected $config = null;

    /**
     * Name of the provider that the user is logged with.
     * @var string
     */
    protected $logged_with;

    /**
     * Array of existing login providers.
     * @var array|Provider[]
     */
    protected $login_providers;

    /**
     * @var null|Driver
     */
    protected $role_driver;

    /**
     * @var Model
     */
    protected $user;

    /**
     * Model name.
     * @var string
     */
    protected $model;

    /**
     * @var CsrfTokenManager
     */
    protected $csrfTokenManager;

    /**
     * @var SQLInjectionFiltrator
     */
    protected $sqlFiltrator;

    /**
     * @var Config\ModelInfoRepository
     */
    protected $modelInfoRepository;

    /**
     * Constructs an Auth instance for the specified configuration
     *
     * @param \PHPixie\Pixie $pixie Pixie dependency container
     * @param string $rootConfig Name of the configuration.
     * @param null|string $controllerConfigName
     * @throw \Exception If no login providers were configured
     */
    public function __construct($pixie, $rootConfig = 'default', $controllerConfigName = null) {
        $this->pixie = $pixie;
        $this->settings = $pixie->config->get("vulninjection/{$rootConfig}");
        $this->config = new Config($this->pixie);
        $this->config->createFromData($this->settings);

        if ($controllerConfigName !== null) {
            $this->addControllerContext($controllerConfigName);
        }
    }

    /**
     * Add controller context as a child of root.
     * @param $name
     * @return $this
     */
    public function addControllerContext($name) {
        $this->controllerSettings = $this->pixie->config->get("vulninjection/{$name}");
        if (!is_array($this->controllerSettings)) {
            $this->controllerSettings = array();
        }
        $controllerContext = Context::createFromData($name, $this->controllerSettings, $this->config->getRootContext(), Context::TYPE_DEFAULT, $this->pixie);
        $this->config->addControllerContext($controllerContext);

        return $this;
    }

    /**
     * Returns the required section
     *
     * @param $sectionName
     * @return array
     */
    public function getSection($sectionName) {
        if (isset($this->settings[$sectionName]))
            return $this->settings[$sectionName];
        return array();
    }

    /**
     * Returns the name of the provider that the user is logged with
     *
     * @return string Name of the provider
     */
    public function logged_with() {
        return $this->logged_with;
    }

    /**
     * Logs the user out
     *
     * @return void
     */
    public function logout() {
        $this->login_providers[$this->logged_with]->logout();
        $this->logged_with = null;
        $this->user = null;
    }

    /**
     * Checks if the logged in user has the specified role
     *
     * @param string $role Role to check for.
     * @return bool If the user has the specified role
     * @throws \Exception If the role driver is not specified
     */
    public function has_role($role) {
        if ($this->role_driver == null)
            throw new \Exception("No role configuration is present.");

        if ($this->user == null)
            return false;

        return $this->role_driver->has_role($this->user, $role);
    }

    /**
     * Returns the login provider by name
     *
     * @param string $provider Name of the login provider
     * @return \PHPixie\Auth\Login\Provider Login provider
     */
    public function provider($provider) {
        return $this->login_providers[$provider];
    }

    /**
     * Checks if the user is logged in via any of the 
     * login providers
     *
     * @return bool if the user is logged in
     */
    public function check_login() {
        foreach ($this->login_providers as $provider)
            if ($provider->check_login())
                return true;

        return false;
    }

    /**
     * Returns a new instance of the user model
     *
     * @return \PHPixie\ORM\Model Model representing the user
     */
    public function user_model() {
        return $this->pixie->orm->get($this->model);
    }

    /**
     * @param $contextName
     * @return $this
     */
    public function goDown($contextName) {
        $this->config->goDown($contextName);
        $this->checkReferrer();
        return $this;
    }

    /**
     * Finishes current context.
     * @return $this
     */
    public function goUp() {
        $this->config->goUp();
        return $this;
    }

    /**
     * @return null|Config
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getVulnerabilities() {
        return $this->config ? $this->config->getVulnerabilities() : array();
    }

    /**
     * @param string $type xss, sql and so on
     * @return array
     */
    public function getVulnerability($type) {
        $vulns = $this->getVulnerabilities();
        return $vulns[$type] ? $vulns[$type] : array();
    }

    /**
     * @return array
     */
    public function getFields() {
        return $this->config ? $this->config->getFields() : array();
    }

    /**
     * Removes script tags and tag attributes JS from string.
     * @param $value String to filter
     * @return mixed
     */
    public function filterXSS($value) {
        return preg_replace(self::PATTERN_XSS, '', $value);
    }

    /**
     * @param $key
     * @param $value
     * @param $table
     * @return mixed
     */
    public function filterStoredXSSIfNeeded($key, $value, $table = null) {
        $fields = $this->getFields();

        if (!($key = $this->getCanonicalKey($key, $table))) {
            return $this->filterXSS($value);
        }

        if (in_array('xss', $fields[$key])) {
            $vuln = $this->getVulnerability('xss');

            if ($vuln['stored'] && !is_numeric($value)) {
                return $value;
            }
        }

        return $this->filterXSS($value);
    }

    /**
     * @return SQLInjectionFiltrator
     */
    public function getSqlFiltrator() {
        if (!$this->sqlFiltrator) {
            $this->sqlFiltrator = new SQLInjectionFiltrator();
        }

        return $this->sqlFiltrator;
    }

    /**
     * Retrieves parameters for sql injection for given column
     * @param $key
     * @param $table
     * @return mixed
     */
    public function getSqlInjectionParams($key, $table) {
        $params = $this->getVulnerability('sql');

        if (!($key = $this->getCanonicalKey($key, $table))) {
            return $params;
        }

        $fields = $this->getFields();

        // And if field contains SQL Injection, enable it.
        if (in_array('sql', $fields[$key])) {
            $params['is_vulnerable'] = true;
        }

        return $params;
    }

    /**
     * @return CsrfTokenManager
     */
    public function getTokenManager() {
        if (!$this->csrfTokenManager) {
            $this->csrfTokenManager = new CsrfTokenManager();
        }

        return $this->csrfTokenManager;
    }

    /**
     * Renders hidden input with CSRF-token.
     * @param $tokenId
     * @param bool $refreshToken
     * @return string
     */
    public function renderTokenField($tokenId, $refreshToken = true) {
        if ($refreshToken) {
            $value = $this->getTokenManager()->refreshToken($tokenId);
        } else {
            $value = $this->getTokenManager()->getToken($tokenId);
        }
        return '<input type="hidden" name="' . $tokenId . '" value="' . $value->getValue() . '">';
    }

    /**
     * Check whether CSRF Injection is enabled.
     * @return bool
     */
    public function csrfIsEnabled() {
        $csrf = $this->getVulnerability('csrf');
        return is_array($csrf) && $csrf['enabled'];
    }

    /**
     * Checks current referrer to be allowed
     */
    public function checkReferrer() {
        $vuln = $this->getVulnerability('referrer');
        if ($vuln['enabled']) {
            return;
        }

        $referrer = $_SERVER['HTTP_REFERER'];
        $parts = parse_url($referrer);

        $host = $parts['host'];
        $method = $_SERVER['REQUEST_METHOD'];
        $proto = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
        $path = $parts['path'];

        $isFilterable = $this->checkIsIn($method, $vuln['methods']) && $this->checkIsIn(strtolower($proto), $vuln['protocols']);

        if ($isFilterable
            && ((!$path || !$this->referrerPathIsAllowed($path, $vuln['paths']))
                || (!$host || !$this->checkIsIn($host, $vuln['hosts'])))
        ) {
            throw new ForbiddenException();
        }
    }

    /**
     * Checks that given value is really in array.
     * @param $value
     * @param $array
     * @return bool
     */
    private function checkIsIn($value, $array) {
        return is_array($array) && count($array) && in_array($value, $array);
    }

    /**
     * Check that referrer is allowed for given paths.
     * @param $path
     * @param $paths
     * @return bool
     */
    private function referrerPathIsAllowed($path, $paths) {
        if (!is_array($paths) || !count($paths)) {
            return true;
        }

        foreach ($paths as $item) {
            if (strpos($path, $item) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get or create (if empty) a fresh CSRF-token for a certain name.
     * @param $tokenId
     * @return string
     */
    public function getToken($tokenId) {
        $token = $this->getTokenManager()->getToken(Page::TOKEN_PREFIX . $tokenId);
        return $token->getValue();
    }

    /**
     * Refresh and retrieve new CSRF-token for a given name.
     * @param $tokenId
     * @return string
     */
    public function refreshToken($tokenId) {
        $token = $this->getTokenManager()->refreshToken(Page::TOKEN_PREFIX . $tokenId);
        return $token->getValue();
    }

    /**
     * Get DB fields which map on vulnerability fields in current context.
     * @return array
     */
    private function getDbFields() {
        return $this->getContextParams('db_fields');
    }

    /**
     * Get all context params, or single one.
     * @param null $name
     * @return array
     */
    public function getContextParams($name = null) {
        $params = $this->config ? $this->config->getContextParams() : array();
        if (!$name) {
            return $params;
        }

        if (array_key_exists($name, $params)) {
            return $params[$name];
        }

        return [];
    }

    /**
     * Get canonical key name from vulnerability fields config
     * @param $key
     * @param $table
     * @return null
     */
    public function getCanonicalKey($key, $table) {
        $fields = $this->getFields();
        $dbFields = $this->getDbFields();

        $dbKey = trim(preg_replace('/`/', '', $key));

        $fieldExistsInConfig = false;

        if (array_key_exists($key, $fields)) {
            $fieldExistsInConfig = true;
        } else if (array_key_exists($dbKey, $dbFields)) {
            $fieldExistsInConfig = true;
            $key = $dbFields[$dbKey];
        } else if ($table) {
            if (is_array($table)) {
                $dbKey = $table[0] . '.' . $dbKey;
            } else {
                $dbKey = $table . '.' . $dbKey;
            }

            if (array_key_exists($dbKey, $dbFields)) {
                $fieldExistsInConfig = true;
                $key = $dbFields[$dbKey];
            }
        }

        return $fieldExistsInConfig ? $key : null;
    }

}