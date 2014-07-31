<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 31.07.2014
 * Time: 11:29
 */


namespace App\Model;

/**
 * Class WishList.
 * @property int id
 * @property int user_id
 * @property string name
 * @property string type
 * @property int is_default
 * @property string created
 * @property string modified
 * @property User owner
 * @package App\Model
 */
class WishList extends BaseModel
{
	const TYPE_PUBLIC = 'public'; // Accessible by anyone
	const TYPE_SHARED = 'shared'; // Accessible only by links
	const TYPE_PRIVATE = 'private'; // Accessible only by owner

    public $table = 'tbl_wish_list';
    public $id_field = 'id';

	protected $belongs_to = array(
		'owner' => array(
			'model' => 'user',
			'key' => 'user_id'
		)
	);

	protected $has_many = array(
		'items'=>array(
			'model'=>'wishListItem',
			'key'=>'id'
		)
	);

	public function isDefault()
	{
		return 0 != (int) $this->is_default;
	}

	public function setDefault($isDefault = true)
	{
		$this->is_default = $isDefault ? 1 : 0;
	}

    public function getPossibleTypes()
    {
        return array(
            self::TYPE_PUBLIC,
            self::TYPE_SHARED,
            self::TYPE_PRIVATE
        );
    }

    public function isValidType($type)
    {
        if (in_array($type, $this->getPossibleTypes())) {
            return true;
        }
        return false;
    }
}