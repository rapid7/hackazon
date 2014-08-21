<?php
return array(
    'fields' => [
        'id' => [
            'sql',
            'db_field' => 'category.categoryID'
        ],
        
//        'brand-filter' => [
//            'db_field' => 'category.categoryID'
//        ],
//        'quality-filter' => [
//            'db_field' => 'category.categoryID'
//        ],
        'searchString' => ['xss']
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