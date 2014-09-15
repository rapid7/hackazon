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

        $this->view->returnUrl = $this->request->get('return_url', '');

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

                        $this->redirect('/admin/');
                        return;

                    } else {
                        $this->pixie->session->flash('error', 'You don\'t have enough permissions to access admin area.');
                        $this->redirect('/admin/user/login');
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

    public function action_index()
    {
        parent::action_index();
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
            'username',
            'first_name',
            'last_name',
            'email',
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
            'created_on',
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
} 