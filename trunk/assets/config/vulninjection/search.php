<?php
return array(
    'fields' => [
        'id' => [
            'db_field' => 'category.categoryID'
        ],
        'brand-filter' => [
            'db_field' => 'category.categoryID'
        ],
        'quality-filter' => [
            'db_field' => 'category.categoryID'
        ]
    ],

    'vulnerabilities' => [
        'sql' => [
            'blind' => true
        ],
        'xss' => [
            'stored' => true
        ]
    ]
);