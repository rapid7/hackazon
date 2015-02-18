<?php
return array (
    'name' => 'faq',
    'type' => 'controller',
    'technology' => 'generic',
    'mapped_to' => 'faq',
    'storage_role' => 'root',
    'vulnerabilities' => 
    array (
        'vuln_list' => 
        array (
            'PHPSessionIdOverflow' => 
            array (
                'enabled' => true,
                'on_corrupted_id' => 'fix',
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
            'mapped_to' => 'index',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'userQuestion',
                    'source' => 'body',
                    'vulnerabilities' => 
                    array (
                        'vuln_list' => 
                        array (
                            'XSS' => 
                            array (
                                'enabled' => true,
                                'stored' => true,
                            ),
                        ),
                    ),
                ),
                1 => 
                array (
                    'name' => 'userEmail',
                    'source' => 'body',
                ),
            ),
        ),
        'view' => 
        array (
            'name' => 'view',
            'type' => 'action',
            'technology' => 'generic',
            'mapped_to' => 'view',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'id',
                    'source' => 'param',
                    'vulnerabilities' => 
                    array (
                        'vuln_list' => 
                        array (
                            'XSS' => 
                            array (
                                'enabled' => true,
                                'stored' => true,
                            ),
                        ),
                    ),
                ),
            ),
            'vulnerabilities' => 
            array (
                'vuln_list' => 
                array (
                    'PHPSessionIdOverflow' => 
                    array (
                        'enabled' => true,
                        'on_corrupted_id' => 'fix',
                    ),
                ),
            ),
        ),
    ),
);