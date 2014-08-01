<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 31.07.2014
 * Time: 18:13
 */


namespace App\Model;


/**
 * Class WishListItem
 * @property int id
 * @property WishList wishlist
 * @property int wish_list_id
 * @property Product product
 * @property int  product_id
 * @property string created
 * @package App\Model
 */
class WishListItem extends BaseModel
{
    public $table = 'tbl_wish_list_item';
    public $id_field = 'id';

    protected $belongs_to = array(
        'wishlist' => array(
            'model' => 'wishlist',
            'key' => 'wish_list_id'
        ),
        'product' => array(
            'model' => 'product',
            'key' => 'product_id'
        )
    );
} 