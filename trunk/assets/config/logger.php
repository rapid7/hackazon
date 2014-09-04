<?php
return array(
    'rootLogger' => array(
        'appenders' => array('default'),
    ),
    'appenders' => array(
        'default' => array(
            'class' => 'LoggerAppenderNull',
        ),
        // uncomment to enable logging
        /*'default' => array(
            'class' => 'LoggerAppenderFile',
            'layout' => array(
                'class' => 'LoggerLayoutHtml'
            ),
            'params' => array(
                'file' => 'log.html',
                'append' => true
            )
        ),*/
    )
);