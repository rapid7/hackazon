<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 25.08.2014
 * Time: 17:45
 */


namespace App\Core;


use App\Controller\Error;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Rest\ErrorController;
use PHPixie\Controller;
use PHPixie\DB\PDOV\Connection;
use PHPixie\ORM\Model;
use VulnModule\Config\FieldDescriptor;
use VulnModule\Csrf\CsrfToken;
use VulnModule\Vulnerability\PHPSessionIdOverflow;
use VulnModule\VulnInjection\Service as VulnService;

/**
 * Base controller for standard and REST controllers
 * @package App\Core
 * @inheritdoc
 * @property-read \App\Pixie $pixie Pixie dependency container
 * @property-read \App\Core\Request $request Pixie dependency container
 */
class BaseController extends Controller
{
    const TOKEN_PREFIX = '_csrf_';

    /**
     * @var View
     */
    protected $view;
    protected $common_path;

    /**
     * @var Model Corresponding model of the controller, i.e. Faq for Faq controller, etc.
     */
    protected $model;

    protected $errorMessage;

    /**
     * @var VulnService
     */
    protected $vulninjection;

    protected $installationProcess = false;

    protected $vulnConfigDir;

    /**
     * Whether to check the session id to prevent session id overflow.
     * @var bool
     */
    protected $checkSessionId = true;

    public function __construct($pixie)
    {
        parent::__construct($pixie);
        $this->vulnConfigDir = __DIR__.'/../../../assets/config/vuln';
    }

    public function before()
    {
        $className = $this->get_real_class($this);
        $controllerName = strtolower($className);

        // Create vulnerability service.
        if (!isset($this->pixie->vulnService)) {
            $this->vulninjection = $this->pixie->vulninjection->service($controllerName);
            $this->pixie->setVulnService($this->vulninjection);

        } else {
            $this->vulninjection = $this->pixie->vulnService;
            $this->pixie->vulnService->loadAndAddChildContext($controllerName);
        }

        $this->vulninjection->getConfig()->getCurrentContext()->setRequest($this->request);

        // Switch vulnerability config to the controller level
        $this->vulninjection->goDown($controllerName);

        if ($this->mustCheckSessionId()) {
            $actionContext = $this->vulninjection->getCurrentContext()->getOrCreateChildByName($this->request->param('action'));
            /** @var PHPSessionIdOverflow $sessVuln */
            $sessVuln = $actionContext->getVulnerability('PHPSessionIdOverflow');
            $sessVuln->fixSession();
        }

        if ($className == 'Install' && in_array($this->request->param('action'), ['index', 'login'])) {
            $this->installationProcess = true;
        }

        try {
            /** @var Connection $pdov */
            $this->pixie->db->get();
            
        } catch (\Exception $e) {
            $this->pixie->session->set('isInstalled', false);
            if (!$this->installationProcess) {
                $this->redirect('/install');
                return;
            }
        }

        // Check Hackazon is installed
        if (!$this->installationProcess && !$this->pixie->session->get('isInstalled')) {
            try {
                /** @var Connection $pdov */
                $pdov = $this->pixie->db->get();
                /** @var \PDO $conn */
                $conn = $pdov->conn;
                $res = $conn->query("SHOW TABLES");
                $dbTables = $res->fetchAll();

                if (count($dbTables) < 20) {
                    throw new \Exception("Not all tables are existing");
                }
                $this->pixie->session->set('isInstalled', true);

            } catch (\Exception $e) {
                $this->pixie->session->set('isInstalled', false);
                $this->redirect('/install');
                return;
            }
        }
    }

    public function after()
    {
        // Exit controller-level vulnerability context.
        $this->vulninjection->goUp();
    }

    /**
     * Obtains an object class name without namespaces
     * @param $obj
     * @return string
     */
    public function get_real_class($obj) {
        $classname = get_class($obj);

        if (preg_match('@\\\\(?<class_name>[\w]+)$@', $classname, $matches)) {
            $classname = $matches['class_name'];
        }

        return $classname;
    }

    /**
     * var_dump beautiful dump.
     */
    public function dumpx()
    {
        call_user_func_array([$this->pixie->debug, 'dumpx'], func_get_args());
    }

    /**
     * Dump and exit script.
     */
    public function dump()
    {
        call_user_func_array([$this->pixie->debug, 'dump'], func_get_args());
    }

    /**
     * Generates URL by given name and parameters.
     *
     * @param string $route Route name
     * @param array $params controller, action, and so on
     * @param bool $absolute Whether link is absolute or not
     * @param string $protocol
     * @return string
     */
    public function generateUrl($route = 'default', array $params = array(), $absolute = false, $protocol = 'http')
    {
        if (!isset($params['action'])) {
            $params['action'] = false;
            if (!isset($params['controller'])) {
                $params['controller'] = false;
            }
        }
        return $this->pixie->router->get($route)->url($params, $absolute, $protocol);
    }

    /**
     * Send response as JSON.
     * @param $responseData
     */
    public function jsonResponse($responseData)
    {
        $this->response->body = json_encode($responseData);
        $this->response->headers[] = 'Content-Type: application/json; charset=utf-8';
        $this->execute = false;
    }

//    /**
//     * @param $value
//     * @param null $field
//     * @return mixed
//     */
//    public function filterStoredXSS($value, $field = null)
//    {
//        return $this->pixie->getVulnService()->filterStoredXSSIfNeeded($field, $value);
//    }

    /**
     * @param $tokenId
     * @param null $value
     * @return bool
     */
    public function isTokenValid($tokenId, $value = null)
    {
        $service = $this->pixie->getVulnService();
        if (!$service) {
            return true;
        }

        $context = $service->getConfig()->getCurrentContext();

        // Check if we need to filter this injection
        if ($context->getVulnerability('CSRF')->isEnabled()) {
            return true;
        }

        $fullTokenId = self::TOKEN_PREFIX . $tokenId;

        if ($value === null) {
            $source = in_array($this->request->method, ['POST', 'PATCH', 'PUT'])
                ? FieldDescriptor::SOURCE_BODY : FieldDescriptor::SOURCE_QUERY;
            $value = $this->request->getRawValue($source, $fullTokenId);

        }

        if (!$value) {
            return false;
        }

        return $service->getTokenManager()->isTokenValid(new CsrfToken($fullTokenId, $value));
    }

    /**
     * @param $tokenId
     */
    public function removeToken($tokenId)
    {
        $service = $this->pixie->getVulnService();
        if (!$service) {
            return;
        }

        $fullTokenId = self::TOKEN_PREFIX . $tokenId;
        $service->getTokenManager()->removeToken($fullTokenId);
    }

    /**
     * @param $tokenId
     * @return string
     */
    public function renderTokenField($tokenId)
    {
        $service = $this->pixie->getVulnService();
        if (!$service) {
            return '';
        }
        return $service->renderTokenField(self::TOKEN_PREFIX . $tokenId);
    }

    /**
     * Checks whether token is real and if not - throws an exception.
     * @param $tokenId
     * @param null $tokenValue
     * @param bool $removeToken
     * @throws HttpException
     */
    public function checkCsrfToken($tokenId, $tokenValue = null, $removeToken = true)
    {
        if (!$this->isTokenValid($tokenId, $tokenValue)) {
            throw new HttpException('Invalid token!', 400, null, 'Bad Request');
        }
        if ($removeToken) {
            $this->removeToken($tokenId);
        }
    }

    /**
     * @inheritdoc
     */
    public function run($action)
    {
        $actionName = $action;
        $action = 'action_'.$action;
        $forceHyphens = $this->request->param('force_hyphens');

        if (!method_exists($this, $action)) {
            // Try to change hyphens to underscores in action name
            $underscoredAction = str_replace('-', '_', $action);
            if (!$forceHyphens || !method_exists($this, $underscoredAction)) {
                throw new NotFoundException("Action '{$actionName}' doesn't exist");
            } else {
                $action = $underscoredAction;
            }
        }

        $this->execute = true;
        $this->before();

        $service = $this->pixie->getVulnService();
        if ($this->execute) {
            $service->getConfig()->getCurrentContext()->setRequest($this->request);
            $service->setRequest($this->request);
        }

        if ($this->execute) {
            $actionName = $this->request->param('action');
            $service->goDown($actionName);
            $service->getConfig()->getCurrentContext()->setRequest($this->request);

            // Check referrer
            if (!($this instanceof Error) && !($this instanceof \App\Admin\Controller\Error) && !($this instanceof ErrorController)) {
                $this->vulninjection->checkReferrer();
            }
        }

        if ($this->execute)
            $this->$action();
        if ($this->execute)
            $this->after();

        if ($this->execute) {
            $service->goUp();
        }
    }

    /**
     * @return boolean
     */
    public function mustCheckSessionId()
    {
        return $this->checkSessionId;
    }
} 