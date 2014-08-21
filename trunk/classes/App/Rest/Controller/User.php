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
} 