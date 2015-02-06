<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 31.07.2014
 * Time: 11:29
 */


namespace App\Model;
use VulnModule\VulnerableField;

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
     * @param string|VulnerableField $searchQuery
     * @return array
     */
    public function searchWishlists($searchQuery)
    {
        if ($searchQuery instanceof VulnerableField) {
            $searchString = $searchQuery->copy('%' . $searchQuery->raw() . '%');
        } else {
            $searchString = '%' . $searchQuery . '%';
        }
        /** @var User[] $users */
        $users = $this->pixie->db->query('select')->fields('id')->table('tbl_users', 'user')
            ->join(['tbl_wish_list', 'wishlists'], ['wishlists.user_id', 'user.id'])
            ->where('wishlists.type', 'public')
            ->where(
                'and', [
                    array('email', 'like', $searchString),
                    array('or', array('username', 'like', $searchString))
          //          array('or', array('wishlists.name', 'like', $searchString))
                ]
            )->execute()->as_array(true);

        $userIds = [];
        foreach ($users as $usr) {
            $userIds[] = $usr->id;  // That's correct, not ->id()
        }

        if ($userIds) {
            $users = $this->pixie->orm->get('User')->where('id', 'IN', $this->pixie->db->expr('(' . implode(',', $userIds) . ')'))
                ->find_all()->as_array();
        } else {
            $users = [];
        }
        $userList = array();
        $followers = array();
        if ($this->pixie->auth->user() !== null) {
            $userFollowers = $this->pixie->orm->get('WishListFollowers')
                ->where('user_id', $this->pixie->auth->user()->id())->find_all()->as_array();
            foreach ($userFollowers as $userFollower) {
                $followers[] = $userFollower->follower_id;
            }
        }
        $curUser = $this->pixie->auth->user();
        foreach ($users as $user) {
            if ($curUser && $user->id() == $curUser->id()) continue;
            $userList[$user->id()] = $user->as_array();
            $userList[$user->id()]['remembered'] = in_array($user->id(), $followers) ? true : false;
            $userList[$user->id()]['wishLists'] = array();
            $publicListExists = false;
            foreach ($user->lists as $list) {
                if ($list->type != self::TYPE_PUBLIC) continue;
                $userList[$user->id()]['wishLists'][] = $list->as_array();
                $publicListExists = true;
            } if (!$publicListExists) {
                unset($userList[$user->id()]);
            }
        }
        return $userList;
    }

    /**
     * Remember user to watch his wishlists.
     * @param $userId
     * @return mixed
     */
    public function remember($userId) {
        $this->pixie->db->query('insert')->table('tbl_wishlist_followers')
            ->data(array('user_id' => $this->pixie->auth->user()->id(), 'follower_id' => $userId))
            ->execute();
        return $this->pixie->db->insert_id();
    }

    public function createNewWishListForUser(User $user, $name = 'New Wish List', $type = WishList::TYPE_PRIVATE) {
        if (!$user || !$user->loaded()) {
            throw new \InvalidArgumentException('User is not valid.');
        }

        $wishList = new WishList($this->pixie);

        if (!$wishList->isValidType($type instanceof VulnerableField ? $type->raw() : $type)) {
            $type = WishList::TYPE_PRIVATE;
        }

        $wishList->type = $type;
        $wishList->name = $name;
        $wishList->created = date('Y-m-d H:i:s');

        $wishList->addToUser($user);
        $wishList->save();
        return $wishList;
    }

    /**
     * @param User $user
     * @throws \InvalidArgumentException
     * @internal param \App\Model\WishList $list
     * @return $this
     */
    public function addToUser(User $user) {
        if (!$user->loaded()) {
            throw new \InvalidArgumentException('User is not loaded.');
        }

        if ($user->wishlists->containsById($this)) {
            return $this;
        }

        $user->add('wishlists', $this);
        $this->save();
        return $this;
    }

    /**
     * @param User $user
     * @throws \InvalidArgumentException
     * @internal param \App\Model\WishList $list
     * @return $this
     */
    public function removeFromUser(User $user) {
        if (!$user->loaded()) {
            throw new \InvalidArgumentException('User is not loaded.');
        }

        // If collection doesn't contain wish list, just stop
        if (!$user->wishlists->containsById($this)) {
            return $this;
        }

        // Else search for
        $itemInList = $user->wishlists->filterOneBy(array('id' => $this->id()));
        if ($itemInList) {
            $user->remove('wishlists', $itemInList);
        }

        return $this;
    }

    /**
     * Returns default wish list.
     * If there is no default wish list, method marks first list as default
     * and returns it.
     * @param User $user
     * @return null|WishList
     */
    public function getUserDefaultWishList(User $user) {
        if (!$user->loaded()) {
            return null;
        }

        if ($user->defaultWishList instanceof WishList && $user->defaultWishList->loaded()) {
            return $user->defaultWishList;
        }

        if (!$user->wishlists->count_all()) {
            return null;
        }

        // If no wishlist in a collection is marked as default - mark first as one.
        $first = null;
        /** @var WishList $list */
        foreach ($user->lists as $list) {
            if (!$first) {
                $first = $list;
            }
            if ($list->isDefault()) {
                $list->setAsUserDefaultWishList($user);
                return $user->defaultWishList;
            }
        }

        if ($first instanceof WishList) {
            $first->setAsUserDefaultWishList($user);
        }

        return $user->defaultWishList;
    }

    /**
     * Sets one of users wishlists as a default.
     * @param User $user
     * @throws \InvalidArgumentException If wishlist isn't a users one.
     * @return $this User himself for chaining
     */
    public function setAsUserDefaultWishList(User $user) {
        if (!$this->loaded()) {
            throw new \InvalidArgumentException("Can't set empty (not loaded) Wish List as default for user.");
        }

        $newDefault = null;

        /** @var WishList $list */
        foreach ($user->lists as $list) {
            if ($this->id() == $list->id()) {
                if (!$list->isDefault()) {
                    $list->setDefault();
                    $list->save();
                    $this->setDefault();
                }
                $user->defaultWishList = $list;
                $newDefault = $list;  // Check that given wishlist really exists in users wishlists.

            } else {
                if ($list->isDefault()) {
                    $list->setDefault(false);
                    $list->save();
                }
            }
        }

        // If given wishlist is foreign to user, notify user about illegal argument
        // User can set as a default only his own wishlists
        if ($newDefault === null) {
            throw new \InvalidArgumentException("Can't set empty (not loaded) Wish List as default for user.");
        }

        return $this;
    }

    public function addProductItem($productId)
    {
        if (!$this->loaded()) {
            throw new \LogicException("Wishlist does not exist, therefore you can't add items into it.");
        }

        $item = new WishListItem($this->pixie);
        $item->product_id = $productId;
        $item->created = date('Y-m-d H:i:s');
        $this->add('items', $item);
        $item->save();
        return $item;
    }

    public function removeProductFromUserWishLists(User $user, $productId) {
        if (!$user->loaded()) {
            return;
        }

        $this->pixie->db->query('delete')->table($this->pixie->orm->get('wishListItem')->table, 'wli')
            ->join(array('tbl_products', 'p'), array('wli.product_id', 'p.productID'))
            ->join(array('tbl_wish_list', 'wl'), array('wl.id', 'wli.wish_list_id'))
            ->where('wl.user_id', '=', $user->id())
            ->where('p.productID', '=', $productId)
            ->execute();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isVisibleToUser(User $user = null)
    {
        $user = $user ? ($user->loaded() ? $user : null) : null;
        $notUsersList = !$user || $this->user_id != $user->id();
        return !($notUsersList && $this->type == self::TYPE_PRIVATE);
    }
}