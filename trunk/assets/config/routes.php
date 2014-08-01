<?php
return array(
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
	'default' => array('(/<controller>(/<action>(/<id>)))',
        array(
            'controller' => 'home',
            'action' => 'index'
        )
    ),
);
