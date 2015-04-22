<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 20.08.2014
 * Time: 19:50
 */


namespace App\Rest\Controller;


use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Model\File;
use App\Rest\Controller;
use PHPixie\ORM\Model;
use VulnModule\Config\FieldDescriptor;

/**
 * Class User
 * @package App\Rest\Controller
 * @property \App\Model\User $item
 */
class User extends Controller
{
    public function action_get()
    {
        if ($this->item->id() == $this->user->id()) {
            $result = parent::action_get();
            $result->photoUrl = $this->item->getPhotoPath();
            return $result;

        } else {
            throw new NotFoundException();
        }
    }

    public function action_delete()
    {
        throw new ForbiddenException();
    }

    public function exposedFields()
    {
        $fields = parent::exposedFields();
        $fields[] = 'photoUrl';
        return $this->removeValues($fields, ['password', 'credit_card', 'credit_card_expires', 'credit_card_cvv', 'rest_token', 'recover_passw']);
    }

    protected function checkUpdateData(array $data)
    {
        $data['password'] = '';
        unset($data['photoUrl']);
        parent::checkUpdateData($data);
    }

    public function action_put($data = null)
    {
        if ($data === null) {
            $data = $this->request->put();
        }
        unset($data['password']);
        unset($data['photoUrl']);
        unset($data[$this->model->id_field]);

        $this->prepareData($data);
        $this->checkUpdateData($data);

        if ($this->item->photo && !$data['photo']) {
            if (is_numeric($this->item->photo)) {
                /** @var File $photo */
                $photo = $this->pixie->orm->get('file', $this->item->photo);
                if ($photo->loaded() && $photo->user_id == $this->user->id()) {
                    $photo->delete();
                }
            }
        }

        $this->item->values($this->request->wrapArray($data, FieldDescriptor::SOURCE_BODY));
        $this->item->save();

        return $this->item;
    }

    public function action_post($data = null)
    {
        if ($data === null) {
            $data = $this->request->post();
        }
        unset($data['password']);
        unset($data['photoUrl']);
        return parent::action_post($data);
    }

    public function action_get_collection()
    {
        $username = $this->request->getWrap('username');
        if ($username) {
            $this->model->where('and', ['username', '=', $username]);
        }
        return parent::action_get_collection();
    }

    protected function preloadModel()
    {
        if ($this->model && $this->request->param('id')) {
            $id = $this->request->param('id');
            if ($id == 'me') {
                $id = $this->user->id();
            }
            /** @var Model $model */
            $model = $this->model
                ->where($this->model->id_field, $id)
                ->find();
            if ($model->loaded()) {
                $this->item = $model;
            } else {
                throw new NotFoundException();
            }
        }
    }
} 