<?php
return array (
    'name' => 'gwt',
    'type' => 'application',
    'technology' => 'gwt',
    'mapped_to' => 'helpdesk',
    'storage_role' => 'root',
    'children' => 
    array (
        'HelpdeskService' => 
        array (
            'name' => 'HelpdeskService',
            'type' => 'controller',
            'technology' => 'gwt',
            'mapped_to' => 'HelpdeskService',
            'children' => 
            array (
                'createEnquiryMessage' => 
                array (
                    'name' => 'createEnquiryMessage',
                    'type' => 'action',
                    'technology' => 'gwt',
                    'mapped_to' => 'createEnquiryMessage',
                    'fields' => 
                    array (
                        0 => 
                        array (
                            'name' => 'message',
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
                'getEnquiryById' => 
                array (
                    'name' => 'getEnquiryById',
                    'type' => 'action',
                    'technology' => 'gwt',
                    'mapped_to' => 'getEnquiryById',
                    'fields' => 
                    array (
                        0 => 
                        array (
                            'name' => 'id',
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
                    ),
                ),
                'createEnquiry' => 
                array (
                    'name' => 'createEnquiry',
                    'type' => 'action',
                    'technology' => 'gwt',
                    'mapped_to' => 'createEnquiry',
                    'fields' => 
                    array (
                        0 => 
                        array (
                            'name' => 'title',
                            'source' => 'body',
                        ),
                        1 => 
                        array (
                            'name' => 'description',
                            'source' => 'body',
                        ),
                    ),
                ),
                'getEnquiryMessages' => 
                array (
                    'name' => 'getEnquiryMessages',
                    'type' => 'action',
                    'technology' => 'gwt',
                    'mapped_to' => 'getEnquiryMessages',
                    'fields' => 
                    array (
                        0 => 
                        array (
                            'name' => 'enquiryId',
                            'source' => 'body',
                        ),
                    ),
                ),
            ),
        ),
    ),
);