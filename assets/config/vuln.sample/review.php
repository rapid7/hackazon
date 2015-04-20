<?php
return array (
    'name' => 'review',
    'type' => 'controller',
    'technology' => 'web',
    'mapped_to' => 'review',
    'storage_role' => 'root',
    'children' => 
    array (
        'send' => 
        array (
            'name' => 'send',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'send',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'productID',
                    'source' => 'body',
                ),
                1 => 
                array (
                    'name' => 'userName',
                    'source' => 'body',
                ),
                2 => 
                array (
                    'name' => 'starValue',
                    'source' => 'body',
                ),
                3 => 
                array (
                    'name' => 'textReview',
                    'source' => 'body',
                ),
                4 => 
                array (
                    'name' => 'userEmail',
                    'source' => 'body',
                ),
            ),
            'vulnerabilities' => 
            array (
                'vuln_list' => 
                array (
                    'CSRF' => 
                    array (
                        'enabled' => false,
                    ),
                ),
            ),
        ),
    ),
);