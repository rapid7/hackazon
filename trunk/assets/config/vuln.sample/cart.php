<?php
return array (
    'name' => 'cart',
    'type' => 'controller',
    'technology' => 'web',
    'storage_role' => 'root',
    'fields' => 
    array (
        0 => 
        array (
            'name' => 'qty',
            'source' => 'body',
        ),
        1 => 
        array (
            'name' => 'product_id',
            'source' => 'body',
        ),
        2 => 
        array (
            'name' => 'itemId',
            'source' => 'any',
        ),
    ),
    'children' => 
    array (
        'add' => 
        array (
            'name' => 'add',
            'type' => 'action',
            'technology' => 'generic',
            'storage_role' => 'child',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'product_id',
                    'source' => 'body',
                ),
            ),
        ),
        'view' => 
        array (
            'name' => 'view',
            'type' => 'action',
            'technology' => 'generic',
            'storage_role' => 'child',
        ),
        'update' => 
        array (
            'name' => 'update',
            'type' => 'action',
            'technology' => 'generic',
            'storage_role' => 'child',
        ),
        'empty' => 
        array (
            'name' => 'empty',
            'type' => 'action',
            'technology' => 'generic',
            'storage_role' => 'child',
        ),
        'setMethods' => 
        array (
            'name' => 'setMethods',
            'type' => 'action',
            'technology' => 'generic',
            'storage_role' => 'child',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'shipping_method',
                    'source' => 'body',
                ),
                1 => 
                array (
                    'name' => 'payment_method',
                    'source' => 'body',
                ),
                2 => 
                array (
                    'name' => 'credit_card_number',
                    'source' => 'body',
                ),
                3 => 
                array (
                    'name' => 'credit_card_year',
                    'source' => 'body',
                ),
                4 => 
                array (
                    'name' => 'credit_card_month',
                    'source' => 'body',
                ),
                5 => 
                array (
                    'name' => 'credit_card_cvv',
                    'source' => 'body',
                ),
            ),
        ),
    ),
);