<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 03.09.2014
 * Time: 19:36
 */

namespace GWTModule;

use App\Model\BaseModel;
use App\Model\Enquiry;
use App\Pixie;
use PHPixie\ORM\Model;

class PHPixieORMRepository
{
    /**
     * @var Pixie
     */
    protected $pixie;

    public function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    public function findOne($modelName, $id = null)
    {
        $data = $this->pixie->orm->get($modelName, $id);
        if (!$data->loaded()) {
            return null;
        }

        return $this->transform($data);
    }

    /**
     * @param Model $model PHPixie's  model
     * @param null $rpcClassName GWT name of the entity
     * @return null|\IsSerializable
     */
    public function transform(Model $model, $rpcClassName = null)
    {
        if ($model === null || !$model->loaded()) {
            return null;
        }

        if ($rpcClassName === null) {
            $rpcClassName = preg_replace('|^.*\\\\|', '', get_class($model));
        }

        $fields = array_keys(get_class_vars($rpcClassName));
        $columns = $model->columns();
        $meta = self::loadMetaData($rpcClassName);

        $object = new $rpcClassName;
        foreach ($fields as $field) {
            if (!in_array($field, $columns)) {
                continue;
            }
            $value = $model->__get($field);
            if (@$meta['fields'][$field]['type'] == 'Date') {
                $value = $value ? new \Date(strtotime($value)) : null;
            }

            $object->$field = $value;
        }

        return $object;
    }

    public function transformCollection($collection)
    {
        $result = [];
        if ($collection === null
            ||$collection instanceof Model && !$collection->loaded()
        ) {
            return $result;
        }

        foreach ($collection as $item) {
            $result[] = $this->transform($item);
        }

        return $result;
    }

    public function transformCollectionAsArrayList($collection)
    {
        $result = $this->transformCollection($collection);

        $list = new \ArrayList();
        $list->addAll($result);

        return $list;
    }

    public function getUserEnquiries()
    {
        $user = $this->getCurrentUser();
        $data = $this->pixie->orm->get('enquiry')->where('created_by', $user->id())->order_by('created_on', 'desc')->find_all();

        return $this->transformCollectionAsArrayList($data);
    }

    public function getEnquiryMessages($enquiryId)
    {
        $user = $this->getCurrentUser();
        /** @var Enquiry $enquiry */
        $enquiry = $this->pixie->orm->get('enquiry')
            ->where('and', [['created_by', $user->id()], ['id', $enquiryId]])
            ->order_by('created_on', 'desc')->find();

        if (!$enquiry->loaded()) {
            throw new \IllegalArgumentException();
        }
        $data = $this->pixie->orm->get('enquiryMessage')
            ->where('enquiry_id', $enquiry->id())
            ->order_by('created_on', 'asc')
            ->find_all();

        /** @var \ArrayList|\EnquiryMessage[] $list */
        $list = $this->transformCollectionAsArrayList($data);
        if ($list->count() == 0) {
            return $list;
        }

        // Fetch message users
        $userIds = [];

        foreach ($list as $message) {
            if (!in_array($message->author_id, $userIds)) {
                $userIds[] = $message->author_id;
            }
        }

        $userData = $this->pixie->orm->get('user')
            ->where('id', 'IN', $this->pixie->db->expr('(' . implode(',', $userIds) . ')'))
            ->find_all();

        /** @var \ArrayList|\User[] $userData */
        $userData = $this->transformCollectionAsArrayList($userData);

        $users = [];
        /** @var \User $usr */
        foreach ($userData as $usr) {
            $users[$usr->id] = $usr;
        }

        foreach ($list as $message) {
            $message->author = $users[$message->author_id];
        }

        return $list;
    }

    public function getCurrentUser()
    {
        return $this->pixie->auth->user();
    }

    public function persistObject($object = null)
    {
        if ($object === null) {
            return null;
        }

        $className = get_class($object);
        $meta = self::loadMetaData($className);

        /** @var BaseModel $model */
        $model = $this->pixie->orm->get($className);
        $columns = $model->columns();
        $values = (array) $object;

        foreach ($values as $key => $value) {
            if (!in_array($key, $columns)) {
                unset($values[$key]);
                continue;
            }
            if ($value instanceof \Date) {
                $values[$key] = date('Y-m-d H:i:s', $value->getTime());
                continue;
            }

            if (@$meta['fields'][$key]['constraints']['key'] && $value == 0) {
                $values[$key] = $this->pixie->db->expr('NULL');
            }
        }

        $model->values($values);
        $model->save();
        $model->refresh();
        $object->{$model->id_field} = $model->id();

        return $object;
    }

    public static function loadMetaData($className = null) {
        $meta = [
            'User' => [],
            'Enquiry' => [
                'fields' => [
                    'assigned_to' => [
                        'constraints' => [
                            'key' => true
                        ]
                    ],
                    'created_on' => [
                        'type' => 'Date'
                    ],
                    'updated_on' => [
                        'type' => 'Date'
                    ]
                ]
            ],
            'EnquiryMessage' => [
                'fields' => [
                    'enquiry_id' => [
                        'constraints' => [
                            'key' => true
                        ]
                    ],
                    'created_on' => [
                        'type' => 'Date'
                    ],
                    'updated_on' => [
                        'type' => 'Date'
                    ]
                ]
            ]
        ];

        return $className !== null ? $meta[$className] : $meta;
    }
} 