<?php
return array (
    'name' => 'home',
    'type' => 'controller',
    'technology' => 'web',
    'storage_role' => 'root',
    'fields' => 
    array (
        0 => 
        array (
            'name' => 'visited_products',
            'source' => 'cookie',
            'vulnerabilities' => 
            array (
                'vuln_list' => 
                array (
                    'SQL' => 
                    array (
                        'enabled' => true,
                        'blind' => false,
                    ),
                ),
            ),
        ),
    ),
    'children' => 
    array (
        'index' => 
        array (
            'name' => 'index',
            'type' => 'action',
            'technology' => 'generic',
            'storage_role' => 'child',
        ),
    ),
);