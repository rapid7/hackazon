<?php
namespace App\Controller;

use App\Model\User as User;

class Facebook extends \PHPixie\Auth\Controller\Facebook{
 
    //This method gets called for new users
    //$access_token is the users access token
    //$return_url is the url to redirect the user to
    //after you are done (it can be null if you are
    //using the popup way, it means that the popup
    //will be closed after the login)
    //$display_mode is either 'page' or 'popup'
    public function new_user($access_token, $return_url, $display_mode) {
 
        //Facebook provider allows use to request
        //URLs with CURL, but you can use any other way of
        //fetching a URL here.
        $data = $this->provider
            ->request("https://graph.facebook.com/me?access_token=".$access_token);
        $data = json_decode($data);

        //Save the new user
        $model = new User($this->pixie);
        $user = $model->saveOAuthUser($data->first_name, $data->id, 'facebook');
 
        //Get the 'pixie' role
        /*
        $role=$this->pixie->orm->get('role')
            ->where('name','pixie')
            ->find();
 
        //Add the 'pixie' role to the user
        $fairy->add('roles',$role);
 */
        //Finally set the user inside the provider
        $this->provider->set_user($user, $access_token);
 
        //And redirect him back.
        $this->return_to_url($display_mode, $return_url);
    }


}