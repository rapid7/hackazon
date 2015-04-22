<?php
return array (
    'name' => 'rest',
    'type' => 'application',
    'technology' => 'rest',
    'mapped_to' => 'rest',
    'storage_role' => 'root',
    'children' => 
    array (
        'user' => 
        array (
            'name' => 'user',
            'type' => 'controller',
            'technology' => 'rest',
            'mapped_to' => 'user',
            'children' => 
            array (
                'put' => 
                array (
                    'name' => 'put',
                    'type' => 'action',
                    'technology' => 'rest',
                    'mapped_to' => 'put',
                    'fields' => 
                    array (
                        0 => 
                        array (
                            'name' => 'first_name',
                            'source' => 'body',
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
                    'vulnerabilities' => 
                    array (
                        'vuln_list' => 
                        array (
                            'XMLExternalEntity' => 
                            array (
                                'enabled' => true,
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'category' => 
        array (
            'name' => 'category',
            'type' => 'controller',
            'technology' => 'rest',
            'mapped_to' => 'category',
            'children' => 
            array (
                'put' => 
                array (
                    'name' => 'put',
                    'type' => 'action',
                    'technology' => 'rest',
                    'mapped_to' => 'get',
                    'fields' => 
                    array (
                        0 => 
                        array (
                            'name' => 'name',
                            'source' => 'any',
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
                ),
                'get_collection' => 
                array (
                    'name' => 'get_collection',
                    'type' => 'action',
                    'technology' => 'rest',
                    'mapped_to' => 'get_collection',
                    'fields' => 
                    array (
                        0 => 
                        array (
                            'name' => 'page',
                            'source' => 'any',
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
                            'name' => 'per_page',
                            'source' => 'any',
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
            ),
        ),
    ),
);