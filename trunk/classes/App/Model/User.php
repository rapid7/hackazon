<?php

namespace App\Model;
use App\Pixie;

/**
 * Class User.
 * @property WishList|WishList[] wishlists
 * @property WishList[] lists
 * @property Pixie pixie
 * @package App\Model
 */
class User extends \PHPixie\ORM\Model {

    public $table = 'tbl_users';
    public $id_field = 'id';

	private $defaultWishList = null;

	protected $has_many = array(
		'wishlists' => array(
            'model' => 'wishList',
            'key' => 'user_id'
        )
	);


	public function checkExistingUser($dataUser){
        if(
            strlen($dataUser['username']) && iterator_count($this->getUserByUsername($dataUser['username'])) > 0
            || strlen($dataUser['email']) && iterator_count($this->getUserByEmail($dataUser['email'])) > 0
        ) {
            return true;
        } else {
            return false;
        }
    }


    protected  function getUserByUsername($username) {
        return $this->pixie->db->query('select')
                                ->table($this->table)
                                ->where('username', $username)
                                ->execute();  
    }

    protected function getUserByEmail($email){
        return $this->pixie->db->query('select')
            ->table($this->table)
            ->where('email', $email)
            ->execute();
    }

    public function RegisterUser($dataUser) {
        $dataUser['password'] = $this->pixie->auth->provider('password')->hash_password($dataUser['password']);
        $dataUser['created_on'] = $dataUser['last_login'] = date('Y-m-d H:i:s');
        $allowed = ['first_name', 'last_name', 'email', 'password', 'username'];
        $allowedData = [];
        foreach ($dataUser as $key => $field) {
            if (in_array($key, $allowed)) {
                $allowedData[$key] = $field;
            }
        }

        $this->pixie->orm->get('User')->values($allowedData)->save();
    }

    public function checkLoginUser($login){
        if (preg_match("/[a-z0-9_-]+(\.[a-z0-9_-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})/i", $login)){
            $user=$this->pixie->orm->get('User')->where('email',$login)->find();
            if($user->loaded())
                $login = $user->username;
        }
        return $login;
    }

    public function loadUserModel($login){
        $user = $this->pixie->orm->get('User')->where('username',$login)->find();
        if($user->loaded())
            return $user;
        return null;
    }

    public function saveOAuthUser($username,$oauth_uid, $oauth_provider){
        $user = $this->pixie->orm->get('User');
        $user->username = $username;
        $user->oauth_provider = $oauth_provider;
        $user->oauth_uid = $oauth_uid;
        $user->created_on =  date('Y-m-d H:i:s');
        return $user->save();
    }

    public function getEmailData($email){
        $user = $this->pixie->orm->get('User')->where('email',$email)->find();

        if($user->loaded()){
            $host = $this->pixie->config->get('parameters.host');
            $host = $host ? $host : 'http://hackazon.com';

            return array(
                'to' => $email,
                'from' => 'RobotHackazon@hackazon.com',
                'subject' => 'recovering password',
                'text' => 'Hello, '.$user->username.'.
Recovering link is here
' . $host . '/user/recover?username='.$user->username.'&recover='.$this->getTempPassword($user),
            );
        }
        return null;
    }

    private function getTempPassword($user){
        $arr = array(
            'a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'q', 'r',
            's', 't', 'u', 'v', 'w', 'x',
            'y', 'z', 'A', 'B', 'C', 'D',
            'E', 'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O', 'P',
            'Q', 'R', 'S', 'T', 'U', 'V',
            'W', 'X', 'Y', 'Z', '1', '2',
            '3', '4', '5', '6', '7', '8',
            '9', '0',
        );
        $password = "";
        for ($i = 0; $i < 32; $i++)
            $password .= $arr[rand(0, count($arr) - 1)];
        $user->recover_passw = md5($password);
        $user->save();
        if($user->loaded())
            return $password;

        return null;
    }

    public function checkRecoverPass($username, $recover_passw){
        $user = $this->loadUserModel($username);
            if($user && md5($recover_passw) === $user->recover_passw)
                return true;
            else
                return false;
    }

    public function changeUserPassword($username, $new_passw){
        $user = $this->loadUserModel($username);
        if ($user) {
            $user->password = $this->pixie->auth->provider('password')->hash_password($new_passw);
            $user->recover_passw = null;
            $user->save();
            if($user->loaded())
                return true;
        }
        return false;
    }

	/**
	 * @param WishList $list
	 * @return $this
	 */
	public function addWishList(WishList $list)
	{
		if ($this->wishlists->containsById($list)) {
			return $this;
		}

		$this->add('wishlists', $list);
        $list->save();
		return $this;
	}

	/**
	 * @param WishList $list
	 * @return $this
	 */
	public function removeWishList(WishList $list)
	{
		// If collection doesn't contain wish list, just stop
		if (!$this->wishlists->containsById($list)) {
			return $this;
		}

		// Else search for
		$itemInList = $this->wishlists->filterOneBy(array('id' => $list->id));
		if ($itemInList) {
			$this->remove('wishlists', $itemInList);
		}

		return $this;
	}

	/**
	 * Returns default wish list.
	 * If there is no default wish list, method marks first list as default
	 * and returns it.
	 * @return null|WishList
	 */
	public function getDefaultWishList()
	{
		if (!$this->loaded()) {
			return null;
		}

		if ($this->defaultWishList instanceof WishList) {
			return $this->defaultWishList;
		}

        if (!$this->wishlists->count_all()) {
            return null;
        }


		$first = null;
        /** @var WishList $list */
		foreach ($this->lists as $list) {
			if (!$first) {
				$first = $list;
			}
			if ($list->isDefault()) {
				$this->setDefaultWishList($list);
				return $this->defaultWishList;
			}
		}

		if ($first instanceof WishList) {
			$this->setDefaultWishList($first);
		}

		return $this->defaultWishList;
	}

	/**
	 * Sets one of users wishlists as a default.
	 * @param WishList $wishList
	 * @return $this User himself for chaining
	 * @throws \InvalidArgumentException If wishlist isn't a users one.
	 */
	public function setDefaultWishList(WishList $wishList)
	{
		if (!$wishList->loaded()) {
			throw new \InvalidArgumentException("Can't set empty (not loaded) Wish List as default for user.");
		}

		$newDefault = null;

        /** @var WishList $list */
		foreach ($this->lists as $list) {
			if ($wishList->id == $list->id) {
				if (!$list->isDefault()) {
					$list->setDefault();
					$list->save();
                    $wishList->setDefault();
				}
                $this->defaultWishList = $list;
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

    public function get($propertyName)
    {
        if ($propertyName == 'lists') {
            return $this->wishlists->find_all()->as_array();
        }
        return null;
    }

    public function createNewWishList($name = 'New Wish List', $type = WishList::TYPE_PRIVATE)
    {
        $wishList = new WishList($this->pixie);

        if (!$wishList->isValidType($type)) {
            $type = WishList::TYPE_PRIVATE;
        }

        $wishList->type = $type;
        $wishList->name = $name;
        $wishList->created = date('Y-m-d H:i:s');

        $this->addWishList($wishList);
        $wishList->save();
        return $wishList;
    }

    public function removeProductFromWishLists($productId)
    {
        if (!$this->loaded()) {
            return;
        }

        $this->pixie->db->query('delete')->table($this->pixie->orm->get('wishListItem')->table, 'wli')
            ->join(array('tbl_products', 'p'), array('wli.product_id', 'p.productID'))
            ->join(array('tbl_wish_list', 'wl'), array('wl.id', 'wli.wish_list_id'))
            ->where('wl.user_id', '=', $this->id())
            ->where('p.productID', '=', $productId)
            ->execute();
    }
}