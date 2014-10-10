<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 10.10.2014
 * Time: 11:18
 */

namespace App\Model;

/**
 * Class Coupon
 * @property int $id
 * @property string $coupon
 * @property number $discount
 * @package App\Model
 */
class Coupon extends BaseModel
{
    public $table = 'tbl_coupons';
}