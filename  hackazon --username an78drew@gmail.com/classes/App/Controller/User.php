<?php

namespace App\Controller;

class User extends \App\Page {



    public function action_login() {
        if (!is_null($this->pixie->auth->user()))
            $this->redirect('/account');
                
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

                    $user->last_login = date('Y-m-d H:i:s');
                    $user->save();
                    //On successful login redirect the user to
                    //our protected page
                    return $this->redirect('/account');
                }
            }
            $this->view->username = $this->request->post('username');
            $this->view->errorMessage = "Username or password are incorrect.";
        }
        //Include 'login.php' subview
        
        $this->view->subview = 'user/login';
    }
    
    public function action_logout() {
        if (!is_null($this->pixie->auth->user()))
            $this->pixie->auth->logout();
        $this->redirect('/');
    }

    public function action_password() {
        if ($this->request->method == 'POST') {
            $email = $this->request->post('email');
            if(!empty($email)){
                $emailData = $this->model->getEmailData($email);
                if(!empty($emailData)){
                    $this->pixie->email->send($emailData['to'], $emailData['from'],$emailData['subject'],$emailData['text']);
                    $this->view->successMessage = "Check your email and restore password.";
                }
                else
                    $this->view->errorMessage = "Email is incorrect.";
            }
        }
        $this->view->subview = 'user/password';
    }


    public function action_register() {
        if (!is_null($this->pixie->auth->user()))
            $this->redirect('/account');
        
        if ($this->request->method == 'POST') {
            $dataUser = $this->getDataUser();

           if($this->model->checkExistingUser($dataUser)){
               $this->view->errorMessage = "User already registered";
               foreach ($dataUser as $key=>$value)
                    $this->view->$key = $value;
            }else{
                $this->model->RegisterUser($dataUser);
                $this->pixie->auth
                    ->provider('password')
                    ->login($dataUser['username'], $dataUser['password']);
                $this->redirect('/account');
            }
        }
        $this->view->subview = 'user/register';
    }

    public function action_recover(){
        if ($this->request->method == 'GET') {
            $username = $this->request->get('username');
            $recover_passw = $this->request->get('recover');
            if(!empty($username) && !empty($recover_passw) && $this->model->checkRecoverPass($username,$recover_passw)){
                $this->view->username = $username;
                $this->view->recover_passw = $recover_passw;
                $this->view->subview = 'user/recover';
            }
            else
                $this->redirect('/404');
        }
        else
            $this->redirect('/404');
    }

    public function action_newpassw(){
        if ($this->request->method == 'POST'){
            $username = $this->request->post('username');
            $recover_passw = $this->request->post('recover');
            $new_passw = $this->request->post('password');
            $confirm_passw = $this->request->post('cpassword');
            if(!empty($username) && !empty($recover_passw) && !empty($new_passw) && !empty($confirm_passw)){
                if($confirm_passw === $new_passw && $this->model->checkRecoverPass($username,$recover_passw)){
                    if($this->model->changeUserPassword($username,$new_passw))
                        $this->view->successMessage = "The password has been changed successfully";
                    $this->view->subview = 'user/recover';
                }
            }
        }
        else
            $this->redirect('/404');
    }


    private function getDataUser(){
        return array(
            'username' => $this->request->post('username'),
            'email' => $this->request->post('email'),
            'user_phone' => $this->request->post('user_phone'),
            'password' =>  $this->request->post('password'),
        );
    }



}