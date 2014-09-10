<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 19:42
 */


namespace App\Admin;
use PHPixie\ORM\Model;
use PHPixie\Paginate;


/**
 * Controller which provides basic CRUD features for Model instances.
 * For detailed tuning of features, just derive fom it and override methods.
 * @package App\Admin
 */
class CRUDController extends Controller
{
    /**
     * @var string Plural name of the model to be shown in UI.
     */
    public $modelNamePlural = '';

    /**
     * @var string Singular name of the model
     */
    public $modelName = '';

    protected $modelFields;

    public function before()
    {
        parent::before();

        if (!$this->modelName) {
            $this->modelName = $this->get_real_class($this);;
        }

        if (!$this->modelNamePlural) {
            $this->modelNamePlural = $this->modelName . 's';
        }

        $this->view->pageTitle = $this->modelNamePlural;
        $this->view->pageHeader = $this->modelNamePlural;

        $this->prepareModelFields();
    }

    /**
     * List items.
     */
    public function action_index()
    {
        $listFields = $this->prepareListFields();

        if ($this->request->is_ajax()) {
            $perPage = $this->request->get('length', 10);
            if ($perPage < 1 || $perPage > 100) {
                $perPage = 10;
            }
            $start = $this->request->get('start', 0);
            if ($start < 0) {
                $start = 0;
            }

            $page = floor($start / $perPage) + 1;
            if ($page < 1) {
                $page = 1;
            }

            $columns = $this->request->get('columns', []);
            $this->tuneModelForList();

            $totalCount = $this->model->count_all();
            $this->model->prepare_relations();

            // Set ordering
            $order = $this->request->get('order', [['column' => 0, 'dir' => 'asc']]);
            $order = $order[0];
            $orderColumn = $columns[$order['column']] ?: [];
            $orderColumn = $orderColumn['data'] ?: key($listFields);
            if (strpos($orderColumn, '___') === false) {
                $this->model->order_by($orderColumn, $order['dir'] ? : 'asc');
            } else {
                $orderColumn = str_replace('___', '.', $orderColumn);
                $this->model->order_by($orderColumn, $order['dir'] ? : 'asc');
            }

            // Set filtering
            $search = $this->request->get('search', ['value' => '']);
            $searchValue = $search['value'];
            $searchValues = preg_split('/\s+/', $searchValue, -1, PREG_SPLIT_NO_EMPTY);

            if ($searchValues) {
                $searchConditions = [];
                foreach ($listFields as $lf => $lfData) {
                    if (!$lfData['searching']) {
                        continue;
                    }
                    $fieldSearchConditions = [];
                    foreach ($searchValues as $sVal) {
                        if (!is_numeric($sVal) && $lfData['data_type'] == 'integer' ) {
                            continue;
                        }
                        $fieldSearchConditions[] = ['and', [str_replace('___', '.', $lf), 'LIKE', "%$sVal%"]];
                    }
                    if ($fieldSearchConditions) {
                        $searchConditions[] = ['or', $fieldSearchConditions];
                    }
                }
                if ($searchConditions) {
                    $this->model->where('and', $searchConditions);
                }
            }

            // Query for items
            $items = $this->pixie->paginate->orm($this->model, $page, $perPage);
            $result = [
                'data' => $this->filterPaginator($items, $listFields),
                'recordsTotal' => (int) $totalCount,
                'recordsFiltered' => (int) $items->num_items
            ];

            $this->jsonResponse($result);
            return;

        } else {
            $this->view->subview = 'crud/list';
            $this->view->listFields = $listFields;
            $this->view->modelName = $this->model->model_name;
        }
    }

    protected function tuneModelForList()
    {
    }

    /**
     * Shows single item
     */
    public function action_show()
    {
        $this->view->subview = 'crud/show';
    }

    /**
     * Edit existing item
     */
    public function action_edit()
    {

    }

    /**
     * Create new item
     */
    public function action_new()
    {

    }

    protected function prepareModelFields()
    {
        $this->modelFields = $this->model->columns();
    }

    protected function prepareListFields()
    {
        $listFields = $this->getListFields();

        $result = [];
        foreach ($listFields as $field => &$data) {
            if (is_numeric($field) && is_string($data)) {
                $field = $data;
                $data = [];
            }

            $data['original_field_name'] = $field;

            if (!$data['type']) {
                $data['type'] = 'text';
            }

            if (!$data['title']) {
                $data['title'] = ucwords(implode(' ', preg_split('/_+/', $field, -1, PREG_SPLIT_NO_EMPTY)));
            }

            $this->checkSubProp($field, $data);

            if ($data['type'] == 'link' || $data['is_link']) {
                $data['is_link'] = true;
                if (!$data['template']) {
                    $data['template'] = '/admin/' . $this->model->model_name . '/%' . $this->model->id_field . '%';
                }
            }

            if ($data['type'] == 'image') {
                if (!$data['max_width']) {
                    $data['max_width'] = 40;
                }

                if (!$data['max_height']) {
                    $data['max_height'] = 30;
                }

                if (!$data['image_base']) {
                    $data['image_base'] = '/images/';
                }

                if (!array_key_exists('orderable', $data)) {
                    $data['orderable'] = false;
                }

                if (!array_key_exists('searching', $data)) {
                    $data['searching'] = false;
                }
            }

            if (!array_key_exists('orderable', $data)) {
                $data['orderable'] = true;
            }

            if (!array_key_exists('searching', $data)) {
                $data['searching'] = true;
            }

            if (strpos($field, '.') !== false) {
                $field = $data['model'] . '___' . $data['model_prop'];
            }

            $result[$field] = $data;
        }
        $listFields = $result;
        unset($data);
        $listFields[$this->model->id_field]['type'] = 'link';
        $listFields[$this->model->id_field]['template'] = '/admin/'.$this->model->model_name.'/%'.$this->model->id_field.'%';
        $listFields[$this->model->id_field]['width'] = '60';

        return $listFields;
    }

    protected function getListFields()
    {
        return array_combine($this->modelFields, array_fill(0, count($this->modelFields), []));
    }

    protected function getEditFields()
    {

        return $this->modelFields;
    }

    /**
     * @param $items Paginate\Pager\ORM
     * @param $fields
     * @return array
     */
    private function filterPaginator($items, $fields)
    {
        /** @var Model[] $data */
        $data = $items->current_items()->as_array();
        $result = [];
        foreach ($data as $item) {
            $resultItem = [];
            foreach ($fields as $field => $info) {
                if ($info['model']) {
                    $modelName = $info['model'];
                    $modelProp = $info['model_prop'];
                    $resultItem[$field] = $this->fieldFormatter($item->$modelName->$modelProp, $item, $info);

                } else if (isset($item->$field)) {
                    $resultItem[$field] = $this->fieldFormatter($item->$field, $item, $info);
                }
            }
            $result[] = $resultItem;
        }

        return $result;
    }

    /**
     * @param $value
     * @param $item Model
     * @param array $format
     * @return string
     */
    public function fieldFormatter($value, $item = null, array $format = [])
    {

        if ($format['max_length']) {
            $length = strlen($value);
            if ($length > $format['max_length']) {
                $value = substr($value, 0, $format['max_length']).'...';
            }
        }

        if ($format['strip_tags']) {
            $value = strip_tags($value);
        }

        $value = $format['value_prefix'] . $value;
        $value = htmlspecialchars($value);

        if ($format['type'] == 'image') {
            if ($value) {
                $value = '<img src="' . $format['image_base'] . $value . '" style="max-width: ' . $format['max_width'] . 'px; '
                    . ' max-height: ' . $format['max_height'] . 'px;" />';
            } else {
                $value = '';
            }
        }

        if ($format['type'] == 'boolean') {
            $value = '<span class="fa-boolean fa fa-circle' . ($value ? '' : '-o') . '"></span>';
        }

        $isLink = $format['type'] == 'link' || $format['is_link'];

        if ($isLink) {
            preg_match('/%(.+?)%/', $format['template'], $matches);
            $linkProp = $matches[1];
            if ($linkProp) {
                $this->checkSubProp($linkProp, $lpMatches);
                if ($lpMatches['model']) {
                    $lpModel = $lpMatches['model'];
                    $lpModelProp = $lpMatches['model_prop'];
                    $linkPropValue = $item->$lpModel->$lpModelProp;

                } else {
                    $linkPropValue = $item->$linkProp;
                }
                $value = '<a href="' . str_replace('%' . $linkProp . '%', $linkPropValue, $format['template']) . '">' . $value . '</a>';
            }
        }

        return $value;
    }

    private function checkSubProp($field, &$data)
    {
        if (strpos($field, '.') !== false) {
            preg_match('/^(?<model>[^\.]*)\.(?<prop>.*)/', $field, $matches);
            $data['model'] = $matches['model'];
            $data['model_prop'] = $matches['prop'];
        }
    }
} 