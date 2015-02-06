<?php
return array (
    'name' => 'amf',
    'type' => 'application',
    'technology' => 'amf',
    'mapped_to' => 'amf',
    'storage_role' => 'root',
    'children' => 
    array (
        'SliderService' => 
        array (
            'name' => 'SliderService',
            'type' => 'controller',
            'technology' => 'amf',
            'mapped_to' => 'SliderService',
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
            'children' => 
            array (
                'getSlides' => 
                array (
                    'name' => 'getSlides',
                    'type' => 'action',
                    'technology' => 'amf',
                    'mapped_to' => 'getSlides',
                    'fields' => 
                    array (
                        0 => 
                        array (
                            'name' => 'num',
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
                    ),
                ),
            ),
        ),
        'CouponService' => 
        array (
            'name' => 'CouponService',
            'type' => 'controller',
            'technology' => 'amf',
            'mapped_to' => 'CouponService',
            'vulnerabilities' => 
            array (
                'vuln_list' => 
                array (
                    'OSCommand' => 
                    array (
                        'enabled' => true,
                    ),
                ),
            ),
            'children' => 
            array (
                'useCoupon' => 
                array (
                    'name' => 'useCoupon',
                    'type' => 'action',
                    'technology' => 'amf',
                    'mapped_to' => 'useCoupon',
                    'fields' => 
                    array (
                        0 => 
                        array (
                            'name' => 'couponCode',
                            'source' => 'body',
                            'vulnerabilities' => 
                            array (
                                'vuln_list' => 
                                array (
                                    'SQL' => 
                                    array (
                                        'enabled' => false,
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