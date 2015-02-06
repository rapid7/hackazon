<?php
return array(
    'rest_my' => array('/api/my/<controller>(/<id>(/<property>))',
        array(
            'namespace' => 'App\\Rest\\My',
            'action' => 'get'
        )
    ),
    'rest_parented' => array('/api/<parent_controller>/<parent_id>/<controller>(/<id>(/<property>))',
        array(
            'controller' => 'Default',
            'action' => 'get'
        )
    ),
    'rest' => array('/api(/<controller>(/<id>(/<property>)))',
        array(
            'controller' => 'Default',
            'action' => 'get'
        )
    ),
    'error' => array(
        '/error/<id>',
        array(
            'controller' => 'error',
            'action' => 'view'
        ),
    ),
    'admin_error' => array(
        '/admin/error/<id>',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'error',
            'action' => 'view'
        ),
    ),
    'wishlist_add_product' => array('/wishlist/add-product/<id>',
        array(
            'controller' => 'Wishlist',
            'action' => 'add_product'
        ),
        'POST'
    ),
    'wishlist_delete_product' => array('/wishlist/remove-product/<id>',
        array(
            'controller' => 'Wishlist',
            'action' => 'delete_product'
        ),
        'POST'
    ),

    'wishlist_new' => array('/wishlist/new',
        array(
            'controller' => 'Wishlist',
            'action' => 'new'
        ),
        'POST'
    ),
    'search' => array('/search', array(
        'controller' => 'Search',
        'action' => 'index',
        'page'   =>  1
        ),
        'GET'
    ),
    'profile_edit' => ['/account/profile/edit',
        array(
            'controller' => 'account',
            'action' => 'edit_profile'
        )
    ],

    'admin_option_value' => array('/admin/option-value(/<action>(/<id>))',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'OptionValue',
            'action' => 'index',
            'force_hyphens' => true
        )
    ),

    'admin_product_option_value' => array('/admin/product-option-value(/<action>(/<id>))',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'ProductOptionValue',
            'action' => 'index',
            'force_hyphens' => true
        )
    ),

    'admin_entity_action' => array(
        array(
            '/admin/<controller>/<id>/<action>',
            array(
                'id' => '\d+'
            ),
        ),
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'home',

            'force_hyphens' => true
        )
    ),

    'admin' => array('/admin(/<controller>(/<action>(/<id>)))',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'home',
            'action' => 'index'
        )
    ),

    'install_login' => array('/install/login',
        array(
            'controller' => 'install',
            'action' => 'login'
        )
    ),

    'install_finish' => array('/install/finish',
        array(
            'controller' => 'install',
            'action' => 'finish'
        )
    ),

    'install' => array('/install(/<id>)',
        array(
            'controller' => 'install',
            'action' => 'index'
        )
    ),
	'default' => array('(/<controller>(/<action>(/<id>)))',
        array(
            'controller' => 'home',
            'action' => 'index'
        )
    ),
);
