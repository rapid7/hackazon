<?php
return [
    'fields' => [
        'full_name' => [
            'xss',
            'db_field' => 'customerAddress.full_name'
        ],
        'address_line_1' => [
            'db_field' => 'customerAddress.address_line_1'
        ],
        'address_line_2' => [
            'db_field' => 'customerAddress.address_line_2'
        ],
        'city' => [
            'db_field' => 'customerAddress.city'
        ],
        'region' => [
            'db_field' => 'customerAddress.region'
        ],
        'zip' => [
            'db_field' => 'customerAddress.zip'
        ],
        'country_id' => [
            'db_field' => 'customerAddress.country_id'
        ],
        'phone' => [
            'db_field' => 'customerAddress.phone'
        ],
    ],

    'actions' => [
        'shipping' => [
            'fields' => [
                'address_id' => [
                    'db_field' => 'cart.shipping_address_id'
                ],
                'full_name' => []
            ]
        ],
        'billing' => [
            'fields' => [
                'address_id' => [
                    'db_field' => 'cart.billing_address_id'
                ],
            ]
        ],
        'getAddress' => [
            'fields' => [
                'address_id' => [
                    'db_field' => 'customerAddress.id'
                ],
            ]
        ],
        'deleteAddress' => [
            'fields' => [
                'address_id' => [
                    'db_field' => 'customerAddress.id'
                ],
            ]
        ],
        'placeOrder' => [
            'fields' => [
                'address_line_1' => [
                    'sql' => ['blind' =>true],
                    'db_field' => 'orderAddress.address_line_1'
                ],
            ]
        ]        
    ],

    'vulnerabilities' => [
        'xss' => [
            'stored' => true
        ],
    ]
];