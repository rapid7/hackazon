<?php
return array(
    'fields' => [
        'id' => [
            'sql',
            'db_field' => 'tbl_category_product.categoryID'
        ],
        
        'searchString' => ['xss']
    ],
    'vulnerabilities' => [
        'sql' => [ 
            'enabled' => true,
            'blind' => true
        ],
    ]
);