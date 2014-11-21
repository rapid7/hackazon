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
use App\Rest\Controller;
use PHPixie\ORM\Model;

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
            return parent::action_get();
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
        return $this->removeValues($fields, ['password']);
    }

    protected function checkUpdateData(array $data)
    {
        $data['password'] = '';
        parent::checkUpdateData($data);
    }

    public function action_put($data = null)
    {
        if ($data === null) {
            $data = $this->request->put();
        }
        unset($data['password']);
        return parent::action_put($data);
    }

    public function action_post($data = null)
    {
        if ($data === null) {
            $data = $this->request->post();
        }
        unset($data['password']);
        return parent::action_post($data);
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