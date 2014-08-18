<?php
return [
    'fields' => [
        'name' => [
            'db_field' => 'wishlist.name'
        ],
        'type' => [
            'db_field' => 'wishlist.type'
        ],
        'id' => [
            'sql',
            'db_field' => 'wishlist.id'
        ],
        'search' => [],
        'page' => [],
        'user_id' => [
            'db_field' => 'wishListFollowers.follower_id'
        ],
        'follower_id' => [
            'db_field' => 'wishListFollowers.follower_id'
        ]
    ],

    'actions' => [
        'add_product' => [
            'fields' => [
                'wishlist_id' => [
                    'db_field' => 'wishlist.id'
                ],
                'id' => [
                    'db_field' => 'product.productID'
                ]
            ],
        ]
    ],

    'vulnerabilities' => [
    ]
];