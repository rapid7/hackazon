<?php
return array (
    'name' => 'review',
    'type' => 'controller',
    'technology' => 'web',
    'storage_role' => 'root',
    'vulnerabilities' => 
    array (
        'vuln_list' => 
        array (
            'CSRF' => 
            array (
                'enabled' => true,
            ),
        ),
    ),
    'children' => 
    array (
        'send' => 
        array (
            'name' => 'send',
            'type' => 'action',
            'technology' => 'generic',
            'storage_role' => 'child',
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
        ),
    ),
);