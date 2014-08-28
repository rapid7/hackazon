<?php

namespace App;

use App\Core\BaseController;
use App\Exception\HttpException;
use App\Model\Cart;
use App\Model\Category as Category;
use App\Model\Product;
use PHPixie\Exception\PageNotFound;
use PHPixie\ORM\Model;
use PHPixie\View;
use VulnModule\Config\Context;
use VulnModule\Csrf\CsrfToken;

/**
 * Base controller
 * @inheritdoc
 */
class Page extends BaseController {

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

    public function before() {
        parent::before();

        $this->view = $this->pixie->view('main');
        $config = $this->pixie->config->get('page');
        $this->view->common_path = $config['common_path'];
        $this->common_path = $config['common_path'];
        $className = $this->get_real_class($this);
        $this->view->returnUrl = '';
        $this->view->controller = $this;

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

        parent::after();
    }

    protected function getSearchCategory($className) {
        switch ($className) {
            case 'Category':
                $category = new Category($this->pixie);
                $search_category = $category->getPageTitle($this->request->param('id'));
                $value = $this->request->param('id');
                break;
            case 'Search':
                $value = $this->request->get("id");
                $category = new Category($this->pixie);
                $search_category = $category->getPageTitle($this->request->get('id'));
				$search_category = ($search_category == "") ? "All" : $search_category;
                break;
            default:
                $search_category = 'All';
                $value = '';
                break;
        }
        return ['value' => $value, 'label' => $search_category];
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
     * Send response as JSON.
     * @param $responseData
     */
    public function jsonResponse($responseData) {
        $this->response->body = json_encode($responseData);
        $this->response->headers[] = 'Content-Type: application/json; charset=utf-8';
        $this->execute = false;
    }

    /**
     * @param $value
     * @param null $field
     * @return mixed
     */
    public function filterStoredXSS($value, $field = null) {
        return $this->pixie->getVulnService()->filterStoredXSSIfNeeded($field, $value);
    }

    /**
     * @param $tokenId
     * @param null $value
     * @return bool
     */
    public function isTokenValid($tokenId, $value = null) {
        $service = $this->pixie->getVulnService();
        if (!$service) {
            return true;
        }

        $fullTokenId = self::TOKEN_PREFIX . $tokenId;

        if ($value === null) {
            $value = $this->request->method == 'POST' ? $this->request->post($fullTokenId) : $this->request->get($fullTokenId);
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
    public function removeToken($tokenId) {
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
    public function renderTokenField($tokenId) {
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
    public function checkCsrfToken($tokenId, $tokenValue = null, $removeToken = true) {
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
    public function run($action) {
        $action = 'action_' . $action;

        if (!method_exists($this, $action))
            throw new PageNotFound("Method {$action} doesn't exist in " . get_class($this));

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

    /**
     * @return Product
     */
    protected function getProductsInCart() {
        $cart = $this->getCart();
        return $cart->products->find_all();
    }

    protected function getCart() {
        /** @var Cart $model */
        $model = $this->pixie->orm->get('Cart');
        return $model->getCart();
    }

    protected function getProductsInCartIds() {
        $items = $this->getProductsInCart()->as_array();
        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item->id();
        }
        return $ids;
    }

}
