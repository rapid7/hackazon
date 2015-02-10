<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 16:27
 */


namespace App\Admin\Controller;


use App\Admin\Controller;
use App\Admin\CRUDController;
use App\Admin\FieldFormatter;
use App\Exception\NotFoundException;
use App\Model\BaseModel;
use App\Model\Role;
use App\Pixie;

/**
 * Class User
 * @package App\Admin\Controller
 * @property \App\Model\User $model
 */
class User extends CRUDController
{
    public $modelNamePlural = 'Users';

    public function action_login()
    {
        $this->view = $this->view('user/login');

        if (!is_null($this->pixie->auth->user()) && $this->pixie->auth->has_role('admin')) {
            $this->redirect('/admin/');
        }

        $returnUrl =  $this->request->get('return_url', '');
        $this->view->returnUrl = $returnUrl;

        if ($this->request->method == 'POST') {

            $login = $this->model->checkLoginUser($this->request->post('username'));
            $password = $this->request->post('password');

            $user = $this->model->loadUserModel($login);

            if($user && $user->active){
                //Attempt to login the user using his
                //username and password
                $logged = $this->pixie->auth
                    ->provider('password')
                    ->login($login, $password);

                if ($logged){

                    if ($this->pixie->auth->has_role('admin')) {
                        $user->last_login = date('Y-m-d H:i:s');
                        $user->save();

                        //On successful login redirect the user to
                        //our protected page, or return_url, if specified
                        if ($this->view->returnUrl) {
                            $this->redirect($this->view->returnUrl);
                            return;
                        }

                        $this->redirect($returnUrl ?: '/admin/');
                        return;

                    } else {
                        $this->pixie->session->flash('error', 'You don\'t have enough permissions to access admin area.');
                        $this->redirect('/admin/user/login' . ($returnUrl ? '?return_url=' . rawurlencode($returnUrl) : ''));
                        return;
                    }
                }
            }
            $this->view->username = $this->request->post('username');
            $this->view->errorMessage = "Username or password are incorrect.";

        } else {
            $this->view->errorMessage = $this->pixie->session->flash('error');
        }

        $this->view->subview = 'user/login';
    }

    public function action_logout() {
        $this->pixie->auth->logout();
        $this->redirect('/admin/user/login');
    }

    public function action_edit()
    {
        $id = $this->request->param('id');
        $roles = self::getRoleOptions($this->pixie);

        if ($this->request->method == 'POST') {
            $user = null;
            if ($id) {
                /** @var BaseModel $user */
                $user = $this->pixie->orm->get($this->model->model_name, $id);
            }

            if (!$user || !$user->loaded()) {
                throw new NotFoundException();
            }

            $data = $this->request->post();
            $this->processRequestFilesForItem($user, $data);
            $user->values($user->filterValues($data));
            $user->save();

            $requestUserRoles = $data['roles'] ?: [];
            $userRoles = array_intersect_key($roles, array_flip($requestUserRoles));

            foreach ($this->pixie->orm->get('role')->find_all() as $role) {
                if (array_key_exists($role->id(), $userRoles)) {
                    $user->add('roles', $role);
                } else {
                    $user->remove('roles', $role);
                }
            }

            if ($user->loaded()) {
                $this->redirect('/admin/' . strtolower($user->model_name) . '/edit/'.$user->id());
                return;
            }

        } else {

            if (!$id) {
                throw new NotFoundException();
            }

            $user = $this->pixie->orm->get($this->model->model_name, $id);
            if (!$user || !$user->loaded()) {
                throw new NotFoundException();
            }
        }

        $editFields = $this->prepareEditFields();
        $this->view->pageTitle = $this->modelName;
        $this->view->pageHeader = $this->view->pageTitle;
        $this->view->modelName = $this->model->model_name;
        $this->view->item = $user;
        $this->view->editFields = $editFields;
        $this->view->formatter = new FieldFormatter($user, $editFields);
        $this->view->formatter->setPixie($this->pixie);

        $this->view->roles = self::getRoleOptions($this->pixie);
        $this->view->subview = 'user/edit';
        $this->view->userRoles = $this->getUserRolesOptions($user);
    }

    public function action_new()
    {
        /** @var \App\Model\User $user */
        $user = $this->pixie->orm->get($this->model->model_name);
        $roles = self::getRoleOptions($this->pixie);

        if ($this->request->method == 'POST') {
            $data = $this->request->post();
            $user->values(array_merge($user->filterValues($data), [
                'created_on' => $data['created_on'] ?: date('Y-m-d H:i:s')
            ]));
            $user->save();

            if ($user->loaded()) {
                $this->processRequestFilesForItem($user, $data);

                $requestUserRoles = $data['roles'] ?: [];
                $userRoles = array_intersect_key($roles, array_flip($requestUserRoles));

                foreach ($this->pixie->orm->get('role')->find_all() as $role) {
                    if (array_key_exists($role->id(), $userRoles)) {
                        $user->add('roles', $role);
                    }
                }

                $this->redirect('/admin/' . strtolower($user->model_name) . '/edit/'.$user->id());
                return;
            }
        } else {
            $userRoles = [];
        }

        $editFields = $this->prepareEditFields();
        $this->view->pageTitle = 'Add new ' . $this->modelName;
        $this->view->pageHeader = $this->view->pageTitle;
        $this->view->modelName = $this->model->model_name;
        $this->view->item = $user;
        $this->view->editFields = $editFields;
        $this->view->formatter = new FieldFormatter($user, $editFields);
        $this->view->formatter->setPixie($this->pixie);
        $this->view->roles = self::getRoleOptions($this->pixie);
        $this->view->subview = 'user/edit';
        $this->view->userRoles = $userRoles;
    }

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'id',
                'username' => [
                    'max_length' => 30,
                    'type' => 'link'
                ],
                'first_name' => [
                    'max_length' => 30
                ],
                'last_name' => [
                    'max_length' => 30
                ],
                'email',
                'oauth_provider',
                'created_on',
                'last_login',
                'photo' => [
                    'type' => 'image',
                    'max_width' => 40,
                    'max_height' => 30,
                    'dir_path' => $this->pixie->getParameter('parameters.use_external_dir') ? '/upload/download.php?image=' : '/user_pictures/',
                    'is_link' => true
                ]
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function getEditFields()
    {
        return [
            'id' => [],
            'username' => [
                'required' => true,
                'max_length' => 64
            ],
            'first_name',
            'last_name',
            'email' => [
                'required' => true,
                'data_type' => 'email'
            ],
            'active' => [
                'type' => 'boolean'
            ],
            'user_phone',
            'oauth_provider',
            'oauth_uid',
            'rest_token',
            'photo' => [
                'type' => 'image',
                'use_external_dir' => $this->pixie->getParameter('parameters.use_external_dir')
            ],
            'created_on' => [
                'data_type' => 'date'
            ],
            'last_login',
        ];
    }

    public static function getAvailableUsers(Pixie $pixie, $options)
    {
        $results = ['' => '—'];
        /** @var \App\Model\User $userModel */
        $userModel = $pixie->orm->get('user');
        /** @var \App\Model\User[] $items */
        $items = $userModel->order_by('username', 'asc')->find_all();
        foreach ($items as $user) {
            $addons = trim(implode(' ', [$user->first_name, $user->last_name]));
            $results[$user->id()] = $user->username . ($addons ? ' (' . $addons . ')' : '');
        }
        return $results;
    }

    public static function getRoleOptions(Pixie $pixie)
    {
        $results = [];
        /** @var Role $roleModel */
        $roleModel = $pixie->orm->get('role');
        /** @var Role[] $roles */
        $roles = $roleModel->order_by('name', 'asc')->find_All();
        foreach ($roles as $role) {
            $results[$role->id()] = $role->name;
        }
        return $results;
    }

    public function getUserRolesOptions(\App\Model\User $user)
    {
        $result = [];
        $roles = $user->roles->find_all()->as_array(true);
        foreach ($roles as $role) {
            $result[$role->id] = $role->name;
        }
        return $result;
    }
} 