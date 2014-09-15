<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 29.07.2014
 * Time: 19:46
 */
return array(
    'host' => 'http://hackazon.com',     // Used in various scripts, where $_SERVER is inaccessible, or where there is a risk thereof.
    'display_errors' => false,
    'use_perl_upload' => false,
    'use_external_dir' => false,
    'user_pictures_external_dir' => '/lib/init/rw',
    'user_pictures_path' => '/web/user_pictures/',
    'common_path' => dirname(dirname(__DIR__)) . '/assets/views/common/',
    'annotation_length' => 900,
);
