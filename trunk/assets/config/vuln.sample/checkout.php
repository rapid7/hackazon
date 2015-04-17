<?php
return array (
    'name' => 'checkout',
    'type' => 'controller',
    'technology' => 'generic',
    'mapped_to' => 'checkout',
    'storage_role' => 'root',
    'fields' => 
    array (
        0 => 
        array (
            'name' => 'addressLine1',
            'source' => 'any',
        ),
        1 => 
        array (
            'name' => 'addressLine2',
            'source' => 'any',
        ),
        2 => 
        array (
            'name' => 'city',
            'source' => 'any',
        ),
        3 => 
        array (
            'name' => 'region',
            'source' => 'any',
        ),
        4 => 
        array (
            'name' => 'zip',
            'source' => 'any',
        ),
        5 => 
        array (
            'name' => 'country_id',
            'source' => 'any',
        ),
        6 => 
        array (
            'name' => 'phone',
            'source' => 'any',
        ),
    ),
    'children' => 
    array (
        'shipping' => 
        array (
            'name' => 'shipping',
            'type' => 'action',
            'technology' => 'generic',
            'mapped_to' => 'shipping',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'addressLine1',
                    'source' => 'body',
                    'vulnerabilities' => 
                    array (
                        'name' => 'addressLineVuln',
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
                    'name' => 'fullName',
                    'source' => 'body',
                ),
                2 => 
                array (
                    'name' => 'address_id',
                    'source' => 'body',
                ),
                3 => 
                array (
                    'name' => 'full_form',
                    'source' => 'body',
                ),
            ),
        ),
        'billing' => 
        array (
            'name' => 'billing',
            'type' => 'action',
            'technology' => 'generic',
            'mapped_to' => 'billing',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'addressLine2',
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
                    'name' => 'address_id',
                    'source' => 'body',
                ),
                2 => 
                array (
                    'name' => 'full_form',
                    'source' => 'body',
                ),
            ),
        ),
        'getAddress' => 
        array (
            'name' => 'getAddress',
            'type' => 'action',
            'technology' => 'generic',
            'mapped_to' => 'getAddress',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'address_id',
                    'source' => 'any',
                ),
            ),
        ),
        'deleteAddress' => 
        array (
            'name' => 'deleteAddress',
            'type' => 'action',
            'technology' => 'generic',
            'mapped_to' => 'deleteAddress',
            'fields' => 
            array (
                0 => 
                array (
                    'name' => 'address_id',
                    'source' => 'body',
                ),
            ),
        ),
        'placeOrder' => 
        array (
            'name' => 'placeOrder',
            'type' => 'action',
            'technology' => 'generic',
            'mapped_to' => 'placeOrder',
        ),
        'confirmation' => 
        array (
            'name' => 'confirmation',
            'type' => 'action',
            'technology' => 'generic',
            'mapped_to' => 'confirmation',
        ),
        'order' => 
        array (
            'name' => 'order',
            'type' => 'action',
            'technology' => 'generic',
            'mapped_to' => 'order',
        ),
    ),
);