<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 10:49
 */


namespace App\Rest;
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

/**
 * Base REST Controller.
 * @package App\RestTest
 * @property Response $response
 * @property Request $request
 * @property Pixie $pixie
 */
class Controller extends \PHPixie\Controller
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

    /**
     * @var array Additional links and information
     */
    protected $meta = [];

    protected $responseFormat = self::FORMAT_JSON;

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

        if ($this->modelName) {
            if (class_exists($this->pixie->app_namespace."Model\\".$this->modelName)) {
                $this->model = $this->pixie->orm->get($this->modelName);
            }
        }

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

        if ($this->responseFormat == self::FORMAT_XML) {
            $this->response->body = $this->asXML(is_array($this->response->body) ? $this->response->body : []);
           // echo $this->response->body;exit;
        } else {
            $this->response->body = $this->response->body || is_array($this->response->body)
                ? json_encode($this->response->body) : '';
        }

        if (!is_string($this->response->body)) {
            $this->response->body = (string) $this->response->body;
        }
    }

    /**
     * Get one item by GET request.
     */
    public function action_get()
    {
        return $this->item;
    }

    /**
     * Create new item by POST request.
     * All fields must be provided.
     * @param null|array $data
     * @throws \App\Exception\HttpException
     * @return BaseModel|null
     */
    public function action_post($data = null)
    {
        if ($this->request->param('id')) {
            throw new HttpException('You can\'t create already existing object.', 400, null, 'Bad Request');
        }
        if ($data === null) {
            $data = $this->request->post();
        }
        $this->prepareData($data);
        $this->checkUpdateData($data);

        $this->model->values($data);
        $this->model->save();
        $this->item = $this->model;

        return $this->item;
    }

    /**
     * Update existing item by PUT request.
     * All fields must be provided.
     * @param null|array $data
     * @return BaseModel|null
     */
    public function action_put($data = null)
    {
        if ($data === null) {
            $data = $this->request->put();
        }
        $this->prepareData($data);
        $this->checkUpdateData($data);

        $this->item->values($data);
        $this->item->save();

        return $this->item;
    }

    /**
     * Update certain fields of item by PATCH request.
     * @param array|null $data
     * @return \App\Model\BaseModel|null
     */
    public function action_patch($data = null)
    {
        if ($data === null) {
            $data = $this->request->put();
        }
        $this->prepareData($data);
        $this->checkPatchData($data);

        $this->item->values($data);
        $this->item->save();

        return $this->item;
    }

    /**
     * Remove item with DELETE request.
     */
    public function action_delete()
    {
        $this->item->delete();
    }

    /**
     * Fetch all possible methods on resource with OPTIONS method.
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
     */
    public function action_get_collection()
    {
        $page = $this->request->get('page', 1);
        $pager = $this->pixie->paginate->orm($this->model, $page, $this->perPage);
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
        if (count($notEnoughFields)) {
            throw new HttpException('Please provide next fields: '.implode(', ', $notEnoughFields), 400, null, 'Bad Request');
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
            throw new HttpException('Remove excess fields: '.implode(', ', $excessRequestFields), 400, null, 'Bad Request');
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
        $action = 'action_'.$action;

        if (!method_exists($this, $action)) {
            throw new NotFoundException("Method {$action} doesn't exist in " . get_class($this), 404, null, 'Not Found');
        }

        $this->execute = true;
        $this->before();
        if ($this->execute) {
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

        $this->response->addLinkUrl($pager->url($pager->page), 'current');
        $this->response->addLinkUrl($pager->url(1), 'first');
        $this->response->addLinkUrl($pager->url($pager->num_pages), 'last');
        if ($pager->page > 1) {
            $this->response->addLinkUrl($pager->url($pager->page - 1), 'prev');
        }
        if ($pager->page < $pager->num_pages) {
            $this->response->addLinkUrl($pager->url($pager->page + 1), 'next');
        }
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
}