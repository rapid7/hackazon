<?php
return array (
    'name' => 'category',
    'type' => 'controller',
    'storage_role' => 'root',
    'fields' => [],
    'children' => [
        'view' => [
            'name' => 'index',
            'type' => 'action',
            'storage_role' => 'child',
            'fields' => [
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
                                'blind' => true,
                            ),
                        ),
                    ),
                ),
            ]
        ]
    ]
);