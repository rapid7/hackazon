<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Page;
use PHPixie\View;
use VulnModule\VulnerableField;
use VulnModule\Config\Annotations as Vuln;

/**
 * Class User
 * @package App\Controller
 * @property \App\Model\User model
 */
class User extends Page
{
    /**
     * @Vuln\Description("View: user/login.")
     */
    public function action_login() {
        $this->view->pageTitle = "Login";
        if (!is_null($this->pixie->auth->user())) {
            $this->redirect('/account');
        }

        $returnUrl = $this->request->getWrap('return_url', '');
        $this->view->returnUrl = $returnUrl;

        if ($this->request->method == 'POST') {

            $login = $this->model->checkLoginUser($this->request->postWrap('username'));
            $password = $this->request->postWrap('password');

            $user = $this->model->loadUserModel($login);

            if($user && $user->active){
                //Attempt to login the user using his
                //username and password
                $logged = $this->pixie->auth
                    ->provider('password')
                    ->login($login, $password);

                if ($logged){

                    $user->last_login = date('Y-m-d H:i:s');
                    $user->save();

                    //On successful login redirect the user to
                    //our protected page, or return_url, if specified
                    if ($returnUrl->raw()) {
                        $this->redirect($returnUrl->escapeXSS());
                        return;
                    }

                    $this->redirect('/account');
                    return;
                }
            }
            $this->view->username = $this->request->postWrap('username');
            $this->view->errorMessage = "Username or password are incorrect.";
        }

        //Include 'login.php' subview
        $this->view->subview = 'user/login';
    }
    
    public function action_logout() {
        if (!is_null($this->pixie->auth->user())) {
            $this->pixie->auth->logout();
        }
        $this->redirect('/');
    }

    /**
     * @Vuln\Description("View: user/password.")
     */
    public function action_password() {
        $this->view->pageTitle = "Restore password";
        if ($this->request->method == 'POST') {
            $email = $this->request->postWrap('email');

            if($email->raw()){
                $emailData = $this->model->getEmailData($email);
                if(!empty($emailData)){
                    $this->pixie->email->send($emailData['to'], $emailData['from'],$emailData['subject'],$emailData['text']);
                    $this->view->successMessage = "Check your email and restore password.";

                } else {
                    $this->view->errorMessage = "Email is incorrect.";
                }
            }
        }
        $this->view->subview = 'user/password';
    }

    /**
     * @Vuln\Description("View: user/register.")
     */
    public function action_register() {
        $this->view->pageTitle = "Registration";
        if (!is_null($this->pixie->auth->user())) {
            $this->redirect('/account');
        }

        $errors = [];
        
        if ($this->request->method == 'POST') {
            $dataUser = $this->getDataUser();
            $valid = true;

            if ($this->model->checkExistingUser($dataUser)) {
                $errors[] = "User already registered";
                $valid = false;
            }

            if ($valid) {
                if (!$dataUser['username']->raw()) {
                    $valid = false;
                    $errors[] = 'Please enter your username.';
                }

                if (!$dataUser['email']->raw()) {
                    $valid = false;
                    $errors[] = 'Please enter your email.';
                }

                if (!$dataUser['password']->raw() || $dataUser['password']->raw() != $dataUser['password_confirmation']->raw()) {
                    $valid = false;
                    $errors[] = 'Passwords are missing or not equal.';
                }

                if ($valid) {
                    $this->model->RegisterUser($dataUser);
                    $this->pixie->auth
                        ->provider('password')
                        ->login($dataUser['username'], $dataUser['password']->raw());

                    $emailView = $this->pixie->view('user/register_email');
                    $emailView->data = $dataUser;

                    $emailData = $this->model->getEmailData($dataUser['email']);
                    $this->pixie->email->send(
                        $emailData['to'],
                        $emailData['from'],
                        'You have successfully registered on hackazon.com',
                        $emailView->render()
                    );

                    $this->redirect('/account');
                }
            }

            if (!$valid) {
                foreach ($dataUser as $key => $value) {
                    $this->view->$key = $value;
                }
            }
        }
        $this->view->errorMessage = implode('<br>', $errors);
        $this->view->subview = 'user/register';
    }

    /**
     * @throws NotFoundException
     * @Vuln\Description("View: user/recover.")
     */
    public function action_recover(){
        if ($this->request->method == 'GET') {
            $recover_passw = $this->request->getWrap('recover');
            if (!$recover_passw->raw()) {
                throw new NotFoundException;
            }

            $user = $this->model->getUserByRecoveryPass($recover_passw->raw());

            if($user){
                $this->view->username = $user->username;
                $this->view->recover_passw = $recover_passw;
                $this->view->subview = 'user/recover';

            } else {
                throw new NotFoundException;
            }
        } else {
            throw new NotFoundException;
        }
    }

    /**
     * @throws NotFoundException
     * @Vuln\Description("View: user/recover.")
     */
    public function action_newpassw(){
        if ($this->request->method == 'POST'){
            $username = $this->request->postWrap('username');
            $recover_passw = $this->request->postWrap('recover');
            $new_passw = $this->request->postWrap('password');
            $confirm_passw = $this->request->postWrap('cpassword');

            if($username->raw() && $recover_passw->raw() && $new_passw->raw() && $confirm_passw->raw()) {
                if($confirm_passw->raw() === $new_passw->raw() && $this->model->checkRecoverPass($username, $recover_passw->raw())){
                    if($this->model->changeUserPassword($username, $new_passw->raw())) {
                        $this->view->successMessage = "The password has been changed successfully";
                        $this->pixie->auth
                            ->provider('password')
                            ->login($username, $new_passw->raw());
                    }
                    $this->view->subview = 'user/recover';
                    return;
                }
            }
        } else {
            throw new NotFoundException;
        }
    }

    /**
     * @Vuln\Description("View: user/terms.")
     */
    public function action_terms()
    {
        $this->view->subview = 'user/terms';
    }

    /**
     * @return array|VulnerableField[]
     */
    private function getDataUser(){
        return array(
            'first_name' => $this->request->postWrap('first_name'),
            'last_name' => $this->request->postWrap('last_name'),
            'email' => $this->request->postWrap('email'),
            'username' => $this->request->postWrap('username'),
            'password' =>  $this->request->postWrap('password'),
            'password_confirmation' =>  $this->request->postWrap('password_confirmation'),
        );
    }
}