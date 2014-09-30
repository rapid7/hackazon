<?php
return array(
    'excluded_models' => [
        'BaseModel',
        'Model',
        'OrderAddress'
    ],
    'auth' => [
        'type' => 'token',  // default == 'basic'
        'session' => false,   // whether to use session to check that user is authenticated
    ]
);