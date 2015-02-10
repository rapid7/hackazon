<?php

namespace VulnModule\VulnInjection;

use App\Core\Request;
use App\Page;
use App\Pixie;
use PHPixie\Auth\Login\Provider;
use PHPixie\Auth\Role\Driver;
use PHPixie\ORM\Model;
use VulnModule\Config\Config;
use VulnModule\Config\Context;
use VulnModule\Config\FieldDescriptor;
use VulnModule\Config\ModelInfoRepository;
use VulnModule\Config\VulnerableElement;
use VulnModule\Csrf\CsrfTokenManager;
use VulnModule\Storage\IReader;
use VulnModule\Storage\PHPFileReader;
use VulnModule\Vulnerability;
use VulnModule\VulnerableField;

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
     * @var ModelInfoRepository
     */
    protected $modelInfoRepository;

    /**
     * @var IReader
     */
    protected $reader;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Constructs an Auth instance for the specified configuration
     *
     * @param Pixie $pixie Pixie dependency container
     * @param string $rootConfigName Name of the configuration.
     * @param null|string $controllerConfigName
     * @throw \Exception If no login providers were configured
     */
    public function __construct($pixie, $rootConfigName = 'default', $controllerConfigName = null) {
        $this->pixie = $pixie;

        $vulnConfigDir = __DIR__.'/../../../../../assets/config/vuln';
        $this->reader = new PHPFileReader($vulnConfigDir);
        $rootContext = $this->reader->read($rootConfigName);
        if (!$rootContext) {
            $rootContext = new Context($rootConfigName, null, Context::TYPE_APPLICATION, Context::STORAGE_ROLE_ROOT);
        }
        $this->config = new Config($pixie, $rootContext);

        if ($controllerConfigName) {
            $this->loadAndAddChildContext($controllerConfigName);
        }
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
     * @param bool $createIfNotExists
     * @return $this
     */
    public function goDown($contextName, $createIfNotExists = true) {
        $this->config->goDown($contextName, $createIfNotExists);
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
     * @return array|\VulnModule\DataType\ArrayObject
     */
    public function getContextVulnerabilities() {
        return $this->getConfig()->getContextVulnerabilities();
    }

    /**
     * @param string $name XSS, SQL and so on
     * @return Vulnerability|null
     */
    public function getContextVulnerability($name) {
        return $this->getConfig()->getVulnerability($name);
    }

    /**
     * Fetches current context fields
     * @return array
     */
    public function getFields() {
        return $this->getCurrentContext()->getFields();
    }

    /**
     * @return Context
     */
    public function getCurrentContext()
    {
        return $this->getConfig()->getCurrentContext();
    }

    /**
     * Fetches a field from current context (or null)
     * @param $field
     * @return mixed
     */
    public function getField($field)
    {
        $fields = $this->getFields();
        return $fields[$field];
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
        $csrf = $this->getContextVulnerability('csrf');
        return is_array($csrf) && $csrf['enabled'];
    }

    /**
     * Checks current referrer to be allowed
     */
    public function checkReferrer() {
        /** @var Vulnerability\Referer $vuln */
        $vuln = $this->getContextVulnerability('Referer');
        $vuln->checkReferer($this->request);
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
     * @param $name
     * @return bool
     */
    public function isVulnerableTo($name)
    {
        return $this->getConfig()->getCurrentContext()->isVulnerableTo($name);
    }

    public function loadAndAddChildContext($name)
    {
        $root = $this->config->getRootContext();
        if ($root->hasChildByName($name)) {
            return;
        }

        $context = $this->reader->read($name);
        if (!$context) {
            $context = new Context($name);
        }
        $root->addChild($context);
    }

    public function getElementByPath($path, $createOnMissing = true)
    {
        $parts = preg_split('/\|/', $path);
        $contextPart = $parts[0];

        $contextParts = preg_split('/->/', $contextPart);

        /** @var Context|null $context */
        $context = null;

        if (!$contextParts[0]) {
            return null;

        } else if ($contextParts[0] == 'default') {
            $context = $this->config->getRootContext();
            if ($contextParts[1]) {
                $this->loadAndAddChildContext($contextParts[1]);
            }

        } else {
            $this->loadAndAddChildContext($contextParts[0]);
            $context = $this->config->getRootContext()->getChildByName($contextParts[0]);
        }

        unset($contextParts[0]);
        unset($parts[0]);

        if ($contextParts[1]) {
            $context = $context->getChildByName($contextParts[1]);
            unset($contextParts[1]);

            if ($context) {
                array_unshift($parts, implode('->', $contextParts));
                return $context->getElementByPath(implode('|', $parts), $createOnMissing);

            } else {
                return null;
            }

        } else {
            if ($parts[1] || $parts[2]) {
                return $context->getElementByPath(implode('|', $parts), $createOnMissing);

            } else {
                return $context;
            }
        }
    }

    /**
     * @param string $value
     * @param string $path Absolute path to vulnerability block which is to be bound to variable.
     * @param bool $restored Indicates, whether the field is restored from serialized source.
     * @return VulnerableField
     */
    public function wrapValueByPath($value, $path, $restored = true)
    {
        $parts = preg_split('/\|/', $path);
        if (!$parts[0] || !$parts[1] || (!$parts[2] && $parts[2] != 0)) {
            throw new \InvalidArgumentException();
        }
        $element = $this->getElementByPath($path) ?: new VulnerableElement();
        $fieldParts = preg_split('/:/', $parts[1]);
        $name = $fieldParts[0];
        $source = $fieldParts[1] ?: FieldDescriptor::SOURCE_ANY;
        $result = new VulnerableField(new FieldDescriptor($name, $source), $value, $element);
        $result->setRestored($restored);
        return $result;
    }

    /**
     * @param string $key
     * @param mixed $rawValue
     * @param string $source
     * @return VulnerableField
     */
    public function wrapValue($key, $rawValue, $source = FieldDescriptor::SOURCE_ANY)
    {
        $context = $this->pixie->vulnService->getConfig()->getCurrentContext();
        $descriptor = new FieldDescriptor($key, $source);
        $field = $context->getOrCreateMatchingField($descriptor);
        if ($this->request) {
            $field->setRequest($this->request);
        }
        $vulnElement = $field->getMatchedVulnerabilityElement();
        return new VulnerableField($descriptor, $rawValue, $vulnElement);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }
}