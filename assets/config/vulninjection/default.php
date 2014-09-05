<?php
/*
$_GET
$_POST
$_FILES
$_COOKIE
$_SESSION
$_REQUEST
*/
return [
    'fields' => [   // Separate request params, either from GET or POST
//        'field_name' => [
//            'sql',
//            'xss',
//            'ArbitraryFileUpload'
//            'RemoteFileInclude'
//        ]
    ],

    'forms' => [    // Forms with fields
    ],

    'contexts' => [     // Custom contexts
    ],

    'vulnerabilities' => [
        'sql' => [   
        ],

        'xss' => [     // XSS params
            'stored' => false
        ],

        'csrf' => [    // CSRF params
            'enabled' => false
        ],

        'referrer' => [
            'enabled' => false,
            'hosts' => [$_SERVER['HTTP_HOST']],
            'protocols' => ['http', 'https'],
            'methods' => ['POST'],
            'paths' => ['/']
        ],

        'os_command' => [
            'enabled' => false
        ]
    ]
];


