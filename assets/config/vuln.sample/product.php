<?php
return array (
    'name' => 'product',
    'type' => 'controller',
    'technology' => 'web',
    'mapped_to' => 'product',
    'storage_role' => 'root',
    'children' => 
    array (
        'view' => 
        array (
            'name' => 'view',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'view',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'id',
                    'source' => 'query',
                    'vulnerabilities' => 
                    array (
                        'vuln_list' => 
                        array (
                            'IntegerOverflow' => 
                            array (
                                'enabled' => true,
                                'transform_strategy' => 'cast_to_integer',
                                'custom_value' => 0,
                                'action_on_not_numeric' => 'bypass',
                            ),
                            'SQL' => 
                            array (
                                'enabled' => true,
                                'blind' => false,
                            ),
                            'XSS' => 
                            array (
                                'enabled' => false,
                                'stored' => true,
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);