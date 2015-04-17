<?php
return array (
    'name' => 'contact',
    'type' => 'controller',
    'technology' => 'web',
    'mapped_to' => 'contact',
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
                    'name' => 'contact_name',
                    'source' => 'body',
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
                    'name' => 'contact_email',
                    'source' => 'body',
                ),
                2 => 
                array (
                    'name' => 'contact_phone',
                    'source' => 'body',
                ),
                3 => 
                array (
                    'name' => 'contact_message',
                    'source' => 'body',
                ),
            ),
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
        ),
    ),
);