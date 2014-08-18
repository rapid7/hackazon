<?php
return array(
    'fields' => [
        'productID' => [
            'db_field' => 'product.productID'
        ],
        'userName' => [
            'db_field' => 'review.username'
        ],
        'starValue' => [
            'db_field' => 'review.rating'
        ],
        'textReview' => [
            'db_field' => 'review.review'
        ],
        'userEmail' => [
            'db_field' => 'review.email'
        ]
    ],

    'vulnerabilities' => [
        'sql' => [
            'blind' => false
        ],
        'xss' => [
            'stored' => true
        ],
        'csrf' => [
            'enabled' => false
        ]
    ]
);