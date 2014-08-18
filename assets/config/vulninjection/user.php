<?php
return [
    'fields' => [
        'username' => [
            'db_field' => 'user.username'
        ],
        'first_name' => [
            'db_field' => 'user.first_name'
        ],
        'last_name' => [
            'db_field' => 'user.last_name'
        ],
        'email' => [
            'db_field' => 'user.email'
        ],
        'display_name' => [
            'db_field' => 'user.display_name'
        ],
        'password' => [
            'db_field' => 'user.password'
        ],
        'recover' => [
            'db_field' => 'user.recover_passw'
        ],
        'return_url' => []
    ],

    'actions' => [
        'login' => [
            'fields' => [
                'return_url' => [],
                'email' => [],
                'password' => []
            ]
        ],

        'password' => [
            'fields' => [
                'email' => []
            ]
        ],

        'register' => [
            'fields' => [
                'first_name' => [],
                'last_name' => [],
                'email' => [],
                'display_name' => [],
                'password' => [],
                'password_confirmation' => [],
            ]
        ],

        'recover' => [
            'fields' => [
                'username' => [],
                'recover' => []
            ]
        ],

        'newpassw' => [
            'fields' => [
                'username' => [],
                'recover' => [],
                'password' => [],
                'cpassword' => [],
            ]
        ]
    ],

    'vulnerabilities' => [

    ]
];