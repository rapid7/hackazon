<?php

namespace App;

use App\Exception\HttpException;
use App\Model\Category as Category;
use PHPixie\Controller;
use PHPixie\Exception\PageNotFound;
use PHPixie\ORM\Model;
use PHPixie\View;
use VulnModule\Config\Context;
use VulnModule\Csrf\CsrfToken;
use VulnModule\VulnInjection\Service as VulnService;

/**
 * Base controller
 *
 * @property-read \App\Pixie $pixie Pixie dependency container
 * @property-read \App\Core\Request $request Pixie dependency container
 */
class Page extends Controller {

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

    /**
     * @var VulnService
     */
    protected $vulninjection;
    protected $errorMessage;

    public function before() {
        $this->view = $this->pixie->view('main');
        $config = $this->pixie->config->get('page');
        $this->view->common_path = $config['common_path'];
        $this->common_path = $config['common_path'];
        $className = $this->get_real_class($this);
        $this->view->returnUrl = '';
        $this->view->controller = $this;

        $controllerName = strtolower($className);

        // Create vulnerability service.
        $this->vulninjection = $this->pixie->vulninjection->service($controllerName);
        $this->pixie->setVulnService($this->vulninjection);

        // Check referrer for system-wide level
        $this->vulninjection->checkReferrer();

        // Switch vulnerability config to the controller level
        $this->vulninjection->goDown($controllerName);

        if (!($className == 'Home' && $this->request->param('action') == 'install')) {
            $category = new Category($this->pixie);
            $this->view->sidebar = $category->getCategoriesSidebar();
            $this->view->search_category = $this->getSearchCategory($className);
            $this->view->search_subcategories = $this->getAllCategories($this->view->sidebar);

            if ($className != "Home") {
                $this->view->categories = $category->getRootCategories();
            }
        }
        $classModel = "App\\Model\\" . $className;
        if (class_exists($classModel)) {
            $this->model = new $classModel($this->pixie);
        } else {
            $this->model = null;
        }
    }

    public function after() {
        $this->response->body = $this->view->render();
        // Exit controller-level vulnerability context.
        $this->vulninjection->goUp();
    }

    /**
     * Obtains an object class name without namespaces
     */
    public function get_real_class($obj) {
        $classname = get_class($obj);

        if (preg_match('@\\\\(?<class_name>[\w]+)$@', $classname, $matches)) {
            $classname = $matches['class_name'];
        }

        return $classname;
    }

    protected function getSearchCategory($className) {
        switch ($className) {
            case 'Category':
                $category = new Category($this->pixie);
                $search_category = $category->getPageTitle($this->request->param('id'));
                $value = $this->request->param('id');
                break;
            default:
                $search_category = 'All';
                $value = '';
                break;
        }
        return  ['value' => $value, 'label' => $search_category] ;
    }

    protected function getAllCategories($categories) {
        $all_categories = array();
        foreach ($categories as $category) {
            $all_categories[$category->categoryID] = $category->name;
            foreach ($category->childs as $subcategory) {
                $all_categories[$subcategory->categoryID] = $subcategory->name;
            }
        }
        return $all_categories;
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
        $this->response->headers['Content-Type'] = 'application/json';
        $this->execute = false;
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
     * @param $value
     * @param null $field
     * @return mixed
     */
    public function filterStoredXSS($value, $field = null)
    {
        return $this->pixie->getVulnService()->filterStoredXSSIfNeeded($field, $value);
    }

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

        $fullTokenId = self::TOKEN_PREFIX . $tokenId;

        if ($value === null) {
            $value = $this->request->method == 'POST'
                ? $this->request->post($fullTokenId) : $this->request->get($fullTokenId);
        }

        // Check if we need to filter this injection
        if ($service->csrfIsEnabled()) {
            return true;
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
     * @throws Exception\HttpException
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
        $action = 'action_'.$action;

        if (!method_exists($this, $action))
            throw new PageNotFound("Method {$action} doesn't exist in ".get_class($this));

        $this->execute = true;
        $this->before();

        // Check referrer vulnerabilities
        $service = $this->pixie->getVulnService();
        $config = $service->getConfig();
        $isControllerLevel = $config->getLevel() <= 1;
        $actionName = $this->request->param('action');

        if ($isControllerLevel) {
            if (!$config->has($actionName)) {
                $context = $config->getCurrentContext();
                $context->addContext(Context::createFromData($actionName, [], $context));
            }
            $service->goDown($actionName);
        }

        if ($this->execute)
            $this->$action();
        if ($this->execute)
            $this->after();

        if ($isControllerLevel) {
            $service->goUp();
        }
    }
}
