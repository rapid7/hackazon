<?php
return array (
    'name' => 'product',
    'type' => 'controller',
    'technology' => 'web',
    'storage_role' => 'root',
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
    'children' => 
    array (
        'view' => 
        array (
            'name' => 'view',
            'type' => 'action',
            'technology' => 'generic',
            'storage_role' => 'child',
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
                                'enabled' => false,
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