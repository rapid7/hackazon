<?php
return array(
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
    'wishlist_add_product' => array('/wishlist/add-product/<id>',
        array(
            'controller' => 'WishList',
            'action' => 'add_product'
        ),
        'POST'
    ),
    'wishlist_delete_product' => array('/wishlist/remove-product/<id>',
        array(
            'controller' => 'WishList',
            'action' => 'delete_product'
        ),
        'POST'
    ),

    'wishlist_new' => array('/wishlist/new',
        array(
            'controller' => 'WishList',
            'action' => 'new'
        ),
        'POST'
    ),
    'search' => array('/search(/page-<page>)', array(
        'controller' => 'Search',
        'action' => 'index',
        'page'   =>  1
        ),
        'GET'
    ),
	'default' => array('(/<controller>(/<action>(/<id>)))',
        array(
            'controller' => 'home',
            'action' => 'index'
        )
    ),
);
