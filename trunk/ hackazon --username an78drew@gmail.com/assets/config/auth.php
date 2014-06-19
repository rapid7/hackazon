<?php

return array(
    'default' => array(
        'model' => 'user',
        //Login providers
        'login' => array(
              'password' => array(
                'login_field' => 'username',
                //Make sure that the corresponding field in the database
                //is at least 50 characters long
                'password_field' => 'password'
            ),
            'facebook' => array(
                //Facebook App ID and Secret
                'app_id' => '',
                'app_secret' => '',
                //Permissions to request from the user
                'permissions' => array('user_about_me'),
                //'fbid_field' => 'fb_id',
                'fbid_field' => 'oauth_uid',
                //Redirect user here after he logs in
                'return_url' => '/home'
            ),
            'twitter' => array(
                'oauth_consumer_key' => '',
                'oauth_consumer_secret' => '',
                'twid_field' => 'oauth_uid',
                //'permissions' => array('user_about_me'),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_callback' => '/home',
                'oauth_version' => '1.0'
            ),
        ),
        //Role driver configuration
        'roles' => array(
            'driver' => 'relation',
            'type' => 'has_many',
            //Field in the roles table
            //that holds the models name
            'name_field' => 'name',
            'relation' => 'roles'
        )
    )
);
