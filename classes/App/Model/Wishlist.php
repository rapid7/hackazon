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
 * @property WishListItem|WishListItem[] items
 * @property Product|Product[] products
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
        'items' => array(
            'model' => 'wishListItem',
            'key' => 'wish_list_id'
        ),
        'products' => array(
            'model' => 'product',
            'through' => 'tbl_wish_list_item',
            'key' => 'wish_list_id',
            'foreign_key' => 'product_id'
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

    /**
     * Search Users and wishLists by username or email
     * @param string $searchQuery
     * @return array
     */
    public function searchWishlists($searchQuery)
    {
         $users = $this->pixie->orm->get('User')
            ->where(
                array('email', 'like', '%' . $searchQuery . '%'),
                array('or', array('username', 'like', '%' . $searchQuery . '%'))
            )->find_all()->as_array();
        $userList = array();
        $followers = array();
        if ($this->pixie->auth->user() !== null) {
            $userFollowers = $this->pixie->orm->get('WishListFollowers')
                ->where('user_id', $this->pixie->auth->user()->id)->find_all()->as_array();
            foreach ($userFollowers as $userFollower) {
                $followers[] = $userFollower->follower_id;
            }
        }
        foreach ($users as $user) {
            if ($user->id == $this->pixie->auth->user()->id) continue;
            $userList[$user->id] = $user->as_array();
            $userList[$user->id]['remembered'] = in_array($user->id, $followers) ? true : false;
            $userList[$user->id]['wishLists'] = array();
            $publicListExists = false;
            foreach ($user->lists as $list) {
                if ($list->type != 'public') continue;
                $userList[$user->id]['wishLists'][] = $list->as_array();
                $publicListExists = true;
            } if (!$publicListExists) {
                unset($userList[$user->id]);
            }
        }
        return $userList;
    }

    public function remember($userId) {
        $this->pixie->db->query('insert')->table('tbl_wishlist_followers')
            ->data(array('user_id' => $this->pixie->auth->user()->id, 'follower_id' => $userId))
            ->execute();
        return $this->pixie->db->insert_id();
    }
}