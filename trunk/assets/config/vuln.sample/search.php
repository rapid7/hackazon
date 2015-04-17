<?php
return array (
    'name' => 'search',
    'type' => 'controller',
    'technology' => 'web',
    'mapped_to' => 'search',
    'storage_role' => 'root',
    'children' => 
    array (
        'index' => 
        array (
            'name' => 'index',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'index',
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
                            'XSS' => 
                            array (
                                'enabled' => true,
                                'stored' => false,
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