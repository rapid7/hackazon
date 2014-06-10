<?php

namespace App\Controller;

class User extends \App\Page {



    public function action_login() {
        if (!is_null($this->pixie->auth->user()))
            $this->redirect('/account');
                
        if ($this->request->method == 'POST') {
            $login = $this->request->post('username');
            $password = $this->request->post('password');

            
            //Attempt to login the user using his
            //username and password
            $logged = $this->pixie->auth
                    ->provider('password')
                    ->login($login, $password);

            //On successful login redirect the user to
            //our protected page
            if ($logged)
                return $this->redirect('/account');
            else{
                $this->view->errorMessage = "Username or password are incorrect.";
            }
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
        $this->view->subview = 'user/password';
    }

    public function action_register() {
        if (!is_null($this->pixie->auth->user()))
            $this->redirect('/account');
        
        if ($this->request->method == 'POST') {
            $login = $this->request->post('username');
            $password = $this->request->post('password');
            
            $existingUser = $this->model->GetUserByUsername($login);
            if(iterator_count($existingUser) > 0){
                $this->errorMessage = "User already registered";
            }else{
                $this->model->RegisterUser($login, $password);
                $this->pixie->auth
                    ->provider('password')
                    ->login($login, $password);
                $this->redirect('/account');
            }

        }
        $this->view->subview = 'user/register';
        
    }    
    
    

    public function action_facebook($access_token, $return_url, $display_mode) {

        //Facebook provider allows use to request
        //URLs with CURL, but you can use any other way of
        //fetching a URL here.
        $data = $this->provider
                ->request("https://graph.facebook.com/me?access_token=" . $access_token);
        $data = json_decode($data);

        //Save the new user
        $fairy = $this->pixie->orm->get('fairy');
        $fairy->name = $data->first_name;
        $fairy->fb_id = $data->id;
        $fairy->save();

        //Get the 'pixie' role
        $role = $this->pixie->orm->get('role')
                ->where('name', 'pixie')
                ->find();

        //Add the 'pixie' role to the user
        $fairy->add('roles', $role);

        //Finally set the user inside the provider
        $this->provider->set_user($fairy, $access_token);

        //And redirect him back.
        $this->return_to_url($display_mode, $return_url);
    }



}