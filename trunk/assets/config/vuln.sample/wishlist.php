<?php
return array (
    'name' => 'wishlist',
    'type' => 'controller',
    'technology' => 'web',
    'mapped_to' => 'wishlist',
    'storage_role' => 'root',
    'fields' => 
    array (
        0 => 
        array (
            'name' => 'id',
            'source' => 'query',
        ),
    ),
    'children' => 
    array (
        'add_product' => 
        array (
            'name' => 'add_product',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'add_product',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'wishlist_id',
                    'source' => 'body',
                ),
                1 => 
                array (
                    'name' => 'id',
                    'source' => 'query',
                    'vulnerabilities' => 
                    array (
                        'vuln_list' => 
                        array (
                            'SQL' => 
                            array (
                                'enabled' => true,
                                'blind' => true,
                            ),
                        ),
                    ),
                ),
            ),
        ),
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
                    'name' => 'page',
                    'source' => 'query',
                ),
                1 => 
                array (
                    'name' => 'id',
                    'source' => 'query',
                ),
            ),
        ),
        'new' => 
        array (
            'name' => 'new',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'new',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'name',
                    'source' => 'body',
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
                    'name' => 'type',
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
        'edit' => 
        array (
            'name' => 'edit',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'edit',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'name',
                    'source' => 'query',
                ),
                1 => 
                array (
                    'name' => 'type',
                    'source' => 'query',
                ),
            ),
        ),
        'set_default' => 
        array (
            'name' => 'set_default',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'set_default',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'id',
                    'source' => 'body',
                ),
            ),
        ),
        'delete_product' => 
        array (
            'name' => 'delete_product',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'delete_product',
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
                            'SQL' => 
                            array (
                                'enabled' => true,
                                'blind' => false,
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'delete' => 
        array (
            'name' => 'delete',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'delete',
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
                            'XSS' => 
                            array (
                                'enabled' => false,
                                'stored' => false,
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'search' => 
        array (
            'name' => 'search',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'search',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'search',
                    'source' => 'body',
                ),
            ),
        ),
        'remember' => 
        array (
            'name' => 'remember',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'remember',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'user_id',
                    'source' => 'body',
                ),
            ),
        ),
        'remove_follower' => 
        array (
            'name' => 'remove_follower',
            'type' => 'action',
            'technology' => 'web',
            'mapped_to' => 'remove_follower',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'follower_id',
                    'source' => 'body',
                ),
            ),
        ),
    ),
);