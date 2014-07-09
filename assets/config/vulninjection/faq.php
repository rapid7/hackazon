<?php
/*
$_GET
$_POST
$_FILES
$_COOKIE
$_SESSION
$_REQUEST
*/

$get = $_GET;
$post = $_POST;


return array(
        'get' => $get,
        'post'  => $post,
        'inputs' => array('userQuestion' => array('xss'), 'userEmail' => array('sql')),
        
        'sql' => array('select'=>array(
                                   'Double-up Single Quotes' => true, /*true-false*/
                                   'SanitizationLevel' => 'none',
                                   'PatternMatchingStyle' => 'Keywords',
                                   'SanitizationParameters' => array()
                                   ),
                        'insert'=>array(
                                   'Double-up Single Quotes' => true, /*true-false*/
                                   'SanitizationLevel' => 'none',
                                   'PatternMatchingStyle' => 'Keywords',
                                   'SanitizationParameters' => array()
                                   ),
                      ),
    
         'xss' => array(
             
                      ),
);
