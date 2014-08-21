<?php
return [
    'fields' => [
        'q' => ['sql'],
        'userQuestion' => [
            'xss',
            'db_field' => 'faq.question'
        ],
        
        'userEmail' => [
            'db_field' => 'faq.email'
        ]
    ],

    'vulnerabilities' => [
        'xss' => [
            'enabled' => true,
            'stored' => true
        ],
    ]
];