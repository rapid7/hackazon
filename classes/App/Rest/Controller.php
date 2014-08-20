<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 10:49
 */


namespace App\Rest;
use App\Core\Response;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Model\BaseModel;
use App\Model\User;
use App\Pixie;
use PHPixie\ORM\Model;

/**
 * Base REST Controller
 * @package App\RestTest
 * @property Response $response
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

    protected $_columns = null;

    /**
     * @var array Additional links and information
     */
    protected $meta = [];

    protected $responseFormat = self::FORMAT_JSON;

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

    public function after()
    {
        $this->response->setHeader('Content-Type', $this->responseFormat . '; charset=utf-8');

        if ($this->request->method == 'HEAD') {
            $this->response->body = '';
            return;
        }

        if ($this->response->body instanceof Model) {
            $this->response->body = $this->response->body->as_array();

            foreach ($this->response->body as $key => $field) {
                if (!in_array($key, $this->exposedFields()) && $key != $this->model->id_field) {
                    unset($this->response->body[$key]);
                }
            }
        }
        $this->response->body = $this->response->body ? json_encode($this->response->body) : '';

    }

    public function action_get()
    {
        $this->response->body = $this->item;
    }

    public function action_post()
    {
        $data = $this->request->post();
        $this->checkUpdateData($data);

        $this->model->values($data);
        $this->model->save();
        $this->item = $this->model;

        $this->response->body = $this->item;
    }

    public function action_put()
    {
        $data = $this->request->post();
        $this->checkUpdateData($data);

        $this->item->values($data);
        $this->item->save();

        $this->response->body = $this->item;
    }

    public function action_delete()
    {
        $this->item->delete();
    }

    public function action_options()
    {
        $allMethods = self::allowedMethods();
        unset($allMethods[array_search('OPTIONS', $allMethods)]);
        $this->response->add_header('Allow: '.implode(',', $allMethods));
    }

    public function action_get_collection()
    {

    }

    public static function allowedMethods()
    {
        return [
            'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH' //, 'HEAD'
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
        if ($this->request->server('HTTP_ACCEPT')) {
            $accepts = $this->request->server('HTTP_ACCEPT');
            $accepts = preg_split('/\\s*,\\s*/', $accepts, -1, PREG_SPLIT_NO_EMPTY);

            $formats = [];
            foreach ($accepts as $accept) {
                $cleaned = preg_replace('#^([^/]+/)(.*?\+)?(.*?)(;.*)?$#i', '$1$3', $accept);
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

    public function exposedFields()
    {
        return $this->modelFields();
    }

    public function modelFields()
    {
        if ($this->_columns === null) {
            $this->_columns = $this->model->columns();
        }
        return $this->_columns;
    }

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
        $keys = array_keys($data);
        $dataFields = array_diff($this->modelFields(), [$this->model->id_field]);
        $excessRequestFields = array_diff($keys, $dataFields);

        if (count($excessRequestFields)) {
            throw new HttpException('Remove excess fields: '.implode(', ', $excessRequestFields), 400, null, 'Bad Request');
        }

        $notEnoughFields = array_diff($dataFields, $keys);

        if (count($notEnoughFields)) {
            throw new HttpException('Please provide next fields: '.implode(', ', $notEnoughFields), 400, null, 'Bad Request');
        }
    }
}