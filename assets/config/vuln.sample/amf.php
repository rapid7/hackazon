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
            'children' => 
            array (
                'getSlides' => 
                array (
                    'name' => 'getSlides',
                    'type' => 'action',
                    'technology' => 'amf',
                    'mapped_to' => 'getSlides',
                ),
            ),
        ),
        'CouponService' => 
        array (
            'name' => 'CouponService',
            'type' => 'controller',
            'technology' => 'amf',
            'mapped_to' => 'CouponService',
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