<?php
return array (
    'name' => 'search',
    'type' => 'controller',
    'technology' => 'web',
    'storage_role' => 'root',
    'children' => 
    array (
        'index' => 
        array (
            'name' => 'index',
            'type' => 'action',
            'technology' => 'generic',
            'storage_role' => 'child',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'searchString',
                    'source' => 'query',
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
                1 => 
                array (
                    'name' => 'id',
                    'source' => 'query',
                ),
                2 => 
                array (
                    'name' => 'brands',
                    'source' => 'query',
                ),
                3 => 
                array (
                    'name' => 'price',
                    'source' => 'query',
                ),
                4 => 
                array (
                    'name' => 'quality',
                    'source' => 'query',
                ),
                5 => 
                array (
                    'name' => 'page',
                    'source' => 'query',
                ),
            ),
        ),
    ),
);