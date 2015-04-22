<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 10:49
 */


namespace App\Rest;


use App\Core\BaseController;
use App\Core\Request;
use App\Core\Response;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Helpers\HttpHelper;
use App\Model\BaseModel;
use App\Model\User;
use App\Pixie;
use PHPixie\ORM\Model;
use PHPixie\ORM\Result;
use PHPixie\Paginate\Pager\ORM as ORMPager;
use VulnModule\Config\Context;
use VulnModule\Config\Annotations as Vuln;
use VulnModule\Config\FieldDescriptor;
use VulnModule\Vulnerability\XSS;
use VulnModule\VulnerableField;
use VulnModule\VulnInjection\Service;

/**
 * Base REST Controller.
 * @package App\RestTest
 * @property Response $response
 * @property Request $request
 * @property Pixie $pixie
 * @Vuln\Route("rest")
 */
class Controller extends BaseController
{
    const FORMAT_JSON = 'application/json';
    const FORMAT_XML = 'application/xml';
    const FORMAT_HTML = 'text/html';

    protected $acceptedFormats = [
        self::FORMAT_JSON,
        self::FORMAT_XML,
        self::FORMAT_HTML
    ];

    protected $prefix = '/api/';

    protected $modelName = null;

    /**
     * @var null|BaseModel
     */
    protected $model = null;

    /**
     * @var null|BaseModel
     */
    protected $item = null;

    /**
     * @var User
     */
    protected $user;

    /**
     * Cached all field names of item.
     * @var null
     */
    protected $_columns = null;

    protected $defaultPerPage = 10;

    protected $maxPerPage = 100;

    protected $minPerPage = 1;

    protected $perPage = 10;

    protected $isSubRequest = false;

    protected $isCollectionRequested = false;

    /**
     * @var array Additional links and information
     */
    protected $meta = [];

    protected $responseFormat = self::FORMAT_JSON;

    protected $originalActionName;

    public static function createController($controllerName, Request $request, Pixie $pixie, $isSubRequest = false)
    {
        if (!$controllerName || $controllerName == 'Default') {
            $className = $request->param('namespace', $pixie->app_namespace).'Rest\\NoneController';
        } else {
            $className = $request->param('namespace', $pixie->app_namespace).'Rest\\Controller\\'.$controllerName;
        }

        if (!class_exists($className)) {
            if (!in_array($controllerName, $pixie->restService->getExcludedModels())
                && class_exists($pixie->app_namespace.'Model\\'.$controllerName)
            ) {
                $className = $request->param('namespace', $pixie->app_namespace) . 'Rest\\Controller';

            } else {
                throw new NotFoundException();
            }
        }

        $controller = $pixie->controller($className);
        $controller->request = $request;
        $controller->setIsSubRequest($isSubRequest);

        // Inject model into the controller.
        if (!$controller->getModelName()) {
            $controller->setModelName($controllerName);
        }

        return $controller;
    }

    /**
     * @inheritdoc
     * @throws \App\Exception\NotFoundException
     */
    public function before()
    {
        $this->prepareContentType();

        if ($this instanceof ErrorController) {
            return;
        }

        // Create vulnerability service.
        if (!$this->vulninjection) {
            $this->vulninjection = $this->pixie->vulninjection->service('rest');
            $this->pixie->setVulnService($this->vulninjection);
        }

        // Switch vulnerability config to the controller level
        $this->vulninjection->goDown('rest');

        $restContext = $this->vulninjection->getConfig()->getCurrentContext();
        $controllerName = strtolower($this->modelName);

        if (!$restContext->hasChildByName($controllerName)) {
            $restContext->addChild(new Context($controllerName, null, Context::TYPE_CONTROLLER));
        }

        $this->vulninjection->goDown($controllerName);

        if (!($this instanceof ErrorController)) {
            // Check referrer vulnerabilities
            $service = $this->pixie->getVulnService();
            //$action = $this->request->param('action');

            $context = $service->getConfig()->getCurrentContext();
            if (!$context->hasChildByName($this->originalActionName)) {
                $context->addChild(new Context($this->originalActionName, null, Context::TYPE_ACTION));
            }

            $service->goDown($this->originalActionName);
        }

        $this->request->adjustRequestContentType();

        if ($this->modelName) {
            if (class_exists($this->pixie->app_namespace."Model\\".$this->modelName)) {
                $this->model = $this->pixie->orm->get($this->modelName);
            }
        }

        $this->preloadModel();
    }

    /**
     * @inheritdoc
     */
    public function after()
    {
        $this->response->setHeader('Content-Type', $this->responseFormat . '; charset=utf-8');

        if ($this->request->method == 'HEAD') {
            $this->response->body = '';
            return;
        }

        if ($this->response->body instanceof Model) {
            /** @var Model $tmpData */
            $tmpData = $this->response->body;
            $tmpData = $tmpData->as_array();

            foreach ($tmpData as $key => $field) {
                if (!in_array($key, $this->exposedFields()) && $key != $this->model->id_field) {
                    unset($tmpData[$key]);
                }
            }
            $this->response->body = $tmpData;
        }

        if ($this->isCollectionRequested) {
            $this->response->body = array_merge([
                'data' => $this->response->body,
            ], $this->meta);
        }

        if ($this->responseFormat == self::FORMAT_XML) {
            $this->response->body = $this->asXML(is_array($this->response->body) ? $this->response->body : []);

        } else {
            $this->response->body = $this->response->body || is_array($this->response->body)
                ? json_encode($this->response->body) : '';
        }

        if (!is_string($this->response->body)) {
            $this->response->body = (string) $this->response->body;
        }

        //file_put_contents(__DIR__.'/../../../rest.log', date('Y-m-d H:i:s') . "\n" . $this->response->body . "\n\n\n", FILE_APPEND);

        if (!($this instanceof ErrorController)) {
            parent::after();
        }
    }

    /**
     * Get one item by GET request.
     * @Vuln\Route("rest", params={"action": "get", "id": "_id_"})
     */
    public function action_get()
    {
        return $this->item;
    }

    /**
     * @return BaseModel|null
     * @Vuln\Route("rest", params={"action": "head"})
     */
    public function action_head()
    {
        return $this->action_get();
    }

    /**
     * Create new item by POST request.
     * All fields must be provided.
     * @param null|array $data
     * @throws \App\Exception\HttpException
     * @return BaseModel|null
     * @Vuln\Route("rest", params={"action": "post"})
     */
    public function action_post($data = null)
    {
        if ($this->request->param('id')) {
            throw new HttpException('You can\'t create already existing object.', 400, null, 'Bad Request');
        }
        if ($data === null) {
            $data = $this->request->post();
        }
        unset($data[$this->model->id_field]);
        $this->prepareData($data);
        $this->checkUpdateData($data);

        $this->model->values($this->request->wrapArray($data, FieldDescriptor::SOURCE_BODY));
        $this->model->save();
        $this->item = $this->model;

        return $this->item;
    }

    /**
     * Update existing item by PUT request.
     * All fields must be provided.
     * @param null|array $data
     * @return BaseModel|null
     * @Vuln\Route("rest", params={"action": "put", "id": "_id_"})
     */
    public function action_put($data = null)
    {
        if ($data === null) {
            $data = $this->request->put();
        }
        unset($data[$this->model->id_field]);
        $this->prepareData($data);
        $this->checkUpdateData($data);
        $values = $this->filterXSS($this->request->wrapArray($data, FieldDescriptor::SOURCE_BODY));
        $this->item->values($values);
        $this->item->save();

        return $this->item;
    }

    /**
     * Update certain fields of item by PATCH request.
     * @param array|null $data
     * @return \App\Model\BaseModel|null
     * @Vuln\Route("rest", params={"action": "patch", "id": "_id_"})
     */
    public function action_patch($data = null)
    {
        if ($data === null) {
            $data = $this->request->put();
        }
        $this->prepareData($data);
        $this->checkPatchData($data);

        $this->item->values($this->request->wrapArray($data, FieldDescriptor::SOURCE_BODY));
        $this->item->save();

        return $this->item;
    }

    /**
     * Remove item with DELETE request.
     * @Vuln\Route("rest", params={"action": "delete"})
     */
    public function action_delete()
    {
        $this->item->delete();
    }

    /**
     * Fetch all possible methods on resource with OPTIONS method.
     * @Vuln\Route("rest", params={"action": "options"})
     */
    public function action_options()
    {
        $allMethods = self::allowedMethods();
        unset($allMethods[array_search('OPTIONS', $allMethods)]);
        $this->response->add_header('Allow: '.implode(',', $allMethods));
    }

    /**
     * Get collection of items by GET request.
     * @return array
     * @Vuln\Route("rest", params={"action": "get"})
     */
    public function action_get_collection()
    {
        $page = 1;
        $perPage = $this->perPage;
        if ($this->request->get('page') !== null) {
            $page = $this->request->getWrap('page', 1);
        }
        if ($this->request->get('per_page') !== null) {
            $perPage = $this->request->getWrap('per_page', $this->perPage);
        }

        $this->adjustOrder();
        $pager = $this->pixie->paginate->orm($this->model, $page, $perPage);
        $currentItems = $pager->current_items()->as_array(true);
        $this->addLinksForCollection($pager);
        return $currentItems;
    }

    /**
     * All possible methods for current resource.
     * @return array
     */
    public static function allowedMethods()
    {
        return [
            'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH', 'HEAD', 'TRACE'
        ];
    }

    /**
     * @return null|string
     */
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * @param string|null $modelName
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @throws \App\Exception\HttpException
     */
    protected function prepareContentType()
    {
        $format = $this->request->get('_format');
        if (in_array($format, ['xml', 'json'])) {
            if ($format == 'xml') {
                $this->responseFormat = 'application/xml';
            } else {
                $this->responseFormat = 'application/json';
            }
            return;
        }

        if ($this->request->server('HTTP_ACCEPT')) {
            $accepts = $this->request->server('HTTP_ACCEPT');
            $accepts = preg_split('/\\s*,\\s*/', $accepts, -1, PREG_SPLIT_NO_EMPTY);

            $formats = [];
            foreach ($accepts as $accept) {
                $cleaned = HttpHelper::cleanContentType($accept);
                $formats[] = $cleaned;
                if (in_array($cleaned, $this->acceptedFormats)) {
                    // Temporarily forbid html format.
                    if ($cleaned == self::FORMAT_HTML) {
                        $cleaned = self::FORMAT_JSON;
                    }
                    $this->responseFormat = $cleaned;
                    return;
                }
            }

            if (in_array('*/*', $formats)) {
                $this->responseFormat = self::FORMAT_JSON;
                return;
            }

            throw new HttpException('Please use another value for Accept header.', 406, null, 'Not Acceptable');
        }
    }

    /**
     * Fields that are exposed to the user.
     * By default it is all fields of item.
     * @return array|null
     */
    public function exposedFields()
    {
        return $this->modelFields();
    }

    /**
     * Fetch all possible fields of item.
     * @return array|null
     */
    public function modelFields()
    {
        if ($this->_columns === null) {
            $this->_columns = $this->model->columns();
        }
        return $this->_columns;
    }

    /**
     * Remove certain elements from array.
     * @param array $fields
     * @param array $toRemove
     * @return array
     */
    public function removeValues(array $fields, array $toRemove = [])
    {
        return array_diff($fields, $toRemove);
    }

    /**
     * Ensures only valid fields are to update.
     * @param array $data
     * @throws \App\Exception\HttpException
     */
    protected function checkUpdateData(array $data)
    {
        $dataFields = array_diff($this->modelFields(), [$this->model->id_field]);
        $this->checkHasExcessFields($data, $dataFields);

        $notEnoughFields = array_diff($dataFields, array_keys($data));
        $exposedFields = $this->exposedFields();
        foreach ($notEnoughFields as $key => $field) {
            if (!in_array($field, $exposedFields)) {
                unset($notEnoughFields[$key]);
            }
        }
        if (count($notEnoughFields)) {
            $exception = new HttpException('Please provide next fields: '.implode(', ', $notEnoughFields), 400, null, 'Bad Request');
            throw $exception;
        }
    }

    protected function checkPatchData(array $data)
    {
        $dataFields = array_diff($this->modelFields(), [$this->model->id_field]);
        $this->checkHasExcessFields($data, $dataFields);
    }

    protected function checkHasExcessFields($data)
    {
        $keys = array_keys($data);
        $dataFields = array_diff($this->modelFields(), [$this->model->id_field]);
        $excessRequestFields = array_diff($keys, $dataFields);

        if (count($excessRequestFields)) {
            $exception = new HttpException('Remove excess fields: '.implode(', ', $excessRequestFields), 400, null, 'Bad Request');

            // Inject XMLExternalEntity vulnerability
            $isVulnerable = $this->pixie->vulnService->getConfig()->getCurrentContext()->isVulnerableTo('XMLExternalEntity');
            if ($isVulnerable) {
                $exception->setParameter('invalidFields', $data);
            }

            throw $exception;
        }
    }

    public function prepareData(array &$data)
    {
        $modelFields = $this->modelFields();
        foreach ($data as $key => $value) {
            if (!in_array($key, $modelFields) && isset($this->model->$key)) {
                unset($data[$key]);

            } else if (in_array($key, $modelFields) && is_array($data[$key])) {
                $data[$key] = '';
            }
        }
    }

    /**
     * Add possibility to return data from actions as a response.
     *
     * @inheritdoc
     * @throws \App\Exception\NotFoundException
     */
    public function run($action, array $params = [])
    {
        $originalActionName = $action;
        $action = 'action_'.$action;
        $this->originalActionName = $originalActionName;

        if (!method_exists($this, $action)) {
            throw new NotFoundException("Method {$action} doesn't exist in " . get_class($this), 404, null, 'Not Found');
        }

        $this->execute = true;
        $this->before();

        if ($this->execute) {
            //error_log("Action: " . $action);
            $result = call_user_func_array([$this, $action], $params);
            if (empty($this->response->body) && !is_numeric($this->response->body) && $result !== null) {
                $this->response->body = $result;
            }
        }
        if ($this->execute) {
            $this->after();
        }
    }

    protected function addLinksForCollection(ORMPager $pager)
    {
        $pager->set_url_pattern($this->prefix . $this->underscorifyName($this->modelName) . '?page=#page#');

        $this->meta['page'] = $pager->page instanceof VulnerableField ? $pager->page->getFilteredValue() : $pager->page;
        $this->meta['page_url'] = $pager->url($pager->page);
        $this->response->addLinkUrl($pager->url($pager->page), 'current');
        $this->meta['first_page'] = 1;
        $this->meta['first_page_url'] = $pager->url(1);
        $this->response->addLinkUrl($pager->url(1), 'first');
        $this->meta['last_page'] = 1;
        $this->meta['last_page_url'] = $pager->url(1);
        $this->response->addLinkUrl($pager->url($pager->num_pages), 'last');
        if ($pager->page > 1) {
            $this->meta['prev_page'] = $pager->page - 1;
            $this->meta['prev_page_url'] = $pager->url($pager->page - 1);
            $this->response->addLinkUrl($pager->url($pager->page - 1), 'prev');
        }
        if ($pager->page < $pager->num_pages) {
            $this->meta['next_page'] = $pager->page + 1;
            $this->meta['next_page_url'] = $pager->url($pager->page + 1);
            $this->response->addLinkUrl($pager->url($pager->page + 1), 'next');
        }
        $this->meta['total_items'] = (int)$pager->num_items;
        $this->meta['pages'] = (int)$pager->num_pages;
        $this->meta['per_page'] = (int)$pager->page_size;
    }

    /**
     * Convert CamelCase to underscored_case.
     * @param $name
     * @return string
     */
    public function underscorifyName($name)
    {
        return strtolower(preg_replace('/(?<=.)([A-Z]+)/', '_$1', $name));
    }

    /**
     * @param Model|Result|BaseModel $current_items
     * @param array $relations
     * @return \App\Model\BaseModel[]|void
     */
    protected function asArrayWith($current_items, array $relations = [])
    {
        if ($current_items instanceof Model) {
            return $this->modelAsArrayWith($current_items, $relations);
        }

        /** @var BaseModel[] $result */
        $result = $current_items->as_array();
        foreach ($result as $key => $item) {
            $result[$key] = $this->modelAsArrayWith($item, $relations);
        }

        return $result;
    }

    /**
     * @param Model $item
     * @param array $relations
     * @return array
     */
    protected function modelAsArrayWith($item, $relations) {
        $result = $item->as_array();
        if (count($relations)) {
            foreach ($relations as $rel) {
                /** @var BaseModel $related */
                $related = $item->$rel;
                $result[$rel] = $related->find_all()->as_array(true);
            }
        }

        return $result;
    }

    /**
     * @return boolean
     */
    public function getIsSubRequest()
    {
        return $this->isSubRequest;
    }

    /**
     * @param boolean $isSubRequest
     */
    public function setIsSubRequest($isSubRequest)
    {
        $this->isSubRequest = $isSubRequest;
    }

    public function asXML(array $data)
    {
        $rootName = $this->model ? $this->underscorifyName($this->modelName) : 'root';
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><$rootName></$rootName>");
        $this->toXML($data, $xml);
        return $xml->asXML();
    }
    
    /**
     * @param array $data
     * @param \SimpleXMLElement $xml
     */
    public function toXML(array $data, &$xml)
    {
        foreach ($data as $key => $value) {
            if ($value instanceof \stdClass) {
                $value = (array) $value;
            }
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subNode = $xml->addChild("$key");
                    $this->toXML($value, $subNode);

                } else {
                    $subNode = $xml->addChild("item{$key}");
                    $this->toXML($value, $subNode);
                }
            } else {
                $xml->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    protected function preloadModel()
    {
        if ($this->model && $this->request->param('id')) {
            /** @var Model $model */
            $model = $this->model
                ->where($this->model->id_field, $this->request->param('id'))
                ->find();
            if ($model->loaded()) {
                $this->item = $model;
            } else {
                throw new NotFoundException();
            }
        }
    }

    protected function adjustOrder()
    {
        $order = 'asc';
        if (in_array(strtolower($this->request->get('order')), ['asc', 'desc'])) {
            $order = strtolower($this->request->get('order'));
        }

        $orderBy = $this->request->get('order');
        if ($orderBy && !in_array($orderBy, $this->exposedFields())) {
            $orderBy = '';
        }

        if ($order && $orderBy) {
            $this->model->order_by($orderBy, $order);
        }
    }

    /**
     * @param boolean $isCollectionRequested
     */
    public function setIsCollectionRequested($isCollectionRequested)
    {
        $this->isCollectionRequested = $isCollectionRequested;
    }

    private function filterXSS($wrappedValues)
    {
        /** @var VulnerableField $val */
        foreach ($wrappedValues as $val) {
            if ($val->isVulnerableTo('XSS')) {
                /** @var XSS $xss */
                $xss = $val->getVulnerability('XSS');
                if (!$xss->isStored()) {
                    $val->setRaw(preg_replace(Service::PATTERN_XSS, '', $val->raw()));
                }
            } else {
                $val->setRaw(preg_replace(Service::PATTERN_XSS, '', $val->raw()));
            }
        }

        return $wrappedValues;
    }
}