<?php
return array(
    'fields' => [
        'id' => [
            'sql',
            'db_field' => 'tbl_category_product.categoryID'
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
    ]
);