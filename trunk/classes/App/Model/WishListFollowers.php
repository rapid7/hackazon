<?php
namespace App\Model;

class WishListFollowers extends BaseModel
{
    public $table = 'tbl_wishlist_followers';
    public $id_field = 'id';

    protected $belongs_to = array(
        'user' => array(
            'model' => 'user',
            'key' => 'user_id'
        ),
        'follower' => array(
            'model' => 'user',
            'key' => 'follower_id'
        )
    );
}