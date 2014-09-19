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
        'user_phone' => [
            'db_field' => 'user.user_phone'
        ],
        'password' => [
            'db_field' => 'user.password'
        ],
        'password_confirmation' => [],
        'return_url' => [],
        'page' => [],
        'photo' => [
            'ArbitraryFileUpload',
            'db_field' => 'user.photo',
        ]
    ],

    'actions' => [
        
        'help_articles' => [
            'fields' => [
                'page' => [
                    'RemoteFileInclude'
                ]
            ]
        ],
        
        'documents' => [
            'vulnerabilities' => [
                'os_command' => [
                    'enabled' => true
                ]
            ]
        ]
    ],
    


];