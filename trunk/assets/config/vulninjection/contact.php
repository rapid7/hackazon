<?php
return array(
    'fields' => [
        'contact_name' => [
            'sql',
            'db_field' => 'contactMessages.name'
        ],
        'contact_email' => [
            'db_field' => 'contactMessages.email'
        ],
        'contact_phone' => [
            'db_field' => 'contactMessages.phone'
        ],
        'contact_message' => [
            'db_field' => 'contactMessages.message'
        ]
    ],
    'vulnerabilities' => [
        'csrf' => [
            'enabled' => true
        ]
    ]    
);