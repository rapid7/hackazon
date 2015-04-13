<?php
return array (
    'name' => 'faq',
    'type' => 'controller',
    'technology' => 'generic',
    'mapped_to' => 'faq',
    'storage_role' => 'root',
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
    ),
);