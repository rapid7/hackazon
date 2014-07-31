<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 31.07.2014
 * Time: 11:38
 */


namespace App\Controller;


use App\Exception\Forbidden;
use App\Model\User;
use App\Page;
use PHPixie\Exception\PageNotFound;

/**
 * Class Wishlist.
 * @package App\Controller
 */
class Wishlist extends Page
{
	/**
	 * @var User
	 */
	private $user;

	public function action_index()
	{
		$this->prepare();

		// Offer to create a new wishlist.

		if ($this->user == null) {
			$this->view->subview = 'wishlist/no_list';
			return;
		}

        $wishList = $this->user->getDefaultWishList();

		if ($wishList) {
            $this->showDefaultWishList();
		} else {
            $this->view->subview = 'wishlist/no_list';
		}
	}

    public function action_show()
    {
        $this->prepare();

        $id = $this->request->param('id');
        $wishList = $this->pixie->orm->get('wishlist', $id);

        if (!$wishList) {
            throw new PageNotFound();
        }

        $this->view->wishList = $wishList;
        $this->view->subview = 'wishlist/show';
    }

    public function action_new()
    {
        $this->prepare();

        if (!$this->user) {
            throw new Forbidden();
        }

        if ($this->request->method != 'POST') {
            $this->redirect('/wishlist');
        }

        $name = $this->request->post('name', 'New Wish List');
        $type = $this->request->post('type', \App\Model\WishList::TYPE_PRIVATE);
        $wishList = new \App\Model\WishList($this->pixie);

        if (!$wishList->isValidType($type)) {
             $type = \App\Model\WishList::TYPE_PRIVATE;
        }

        $wishList->type = $type;
        $wishList->name = $name;
        $wishList->created = date('Y-m-d H:i:s');

        $this->user->addWishList($wishList);
        $wishList->save();

        $this->redirect($this->generateUrl('default', array(
            'controller' => 'wishlist'
        )));
    }

    public function action_set_default()
    {
        $this->prepare();
        if ($this->request->method != 'POST') {
            throw new \Exception();
        }

        if (!$this->user) {
            throw new Forbidden();
        }

        $id = $this->request->post('id');

        if (!$id) {
            throw new \Exception();
        }
        $wishList = $this->getWishList($id);

        if ($wishList->owner->id() != $this->user->id()) {
            throw new Forbidden();
        }

        $this->user->setDefaultWishList($wishList);

        if ($this->request->is_ajax()) {
            $this->response->body = json_encode(['success' => 1]);
            $this->response->headers['Content-Type'] = 'application/json';
        }

        $this->redirect($this->generateUrl('default', array(
            'controller' => 'wishlist',
            'action' => 'show',
            'id' => $wishList->id()
        )));
    }


    public function action_delete()
    {
        $this->prepare();
        if ($this->request->method != 'POST') {
            throw new \Exception();
        }

        if (!$this->user) {
            throw new Forbidden();
        }

        $id = $this->request->post('id');

        if (!$id) {
            throw new \Exception();
        }
        $wishList = $this->getWishList($id);

        if ($wishList->owner->id() != $this->user->id()) {
            throw new Forbidden();
        }

        $wishList->delete();

        if ($this->request->is_ajax()) {
            $this->response->body = json_encode(['success' => 1]);
            $this->response->headers['Content-Type'] = 'application/json';
        }

        $this->redirect($this->generateUrl('default', array(
            'controller' => 'wishlist'
        )));
    }

    /**
     * Set up important variables for most of actions.
     */
    protected function prepare()
    {
        $this->user = $this->pixie->auth->user();
        if ($this->user) {
            $this->view->user = $this->user;
        }
        $this->view->pageTitle = "Wish List";
    }

	protected function showDefaultWishList()
	{
        $wishList = $this->user->getDefaultWishList();
        $this->view->wishList = $wishList;

		if (!$wishList) {
            $this->view->showDefaultPage = true;
			$this->view->subview = 'wishlist/no_list';
            return;
		}

        $this->view->subview = 'wishlist/show';
	}

    protected function checkIsOwner()
    {
        if (!$this->user) {
            throw new Forbidden();
        }
    }

    /**
     * @param $id
     * @return mixed|\App\Model\WishList
     */
    public function getWishList($id)
    {
        $model = new \App\Model\WishList($this->pixie);
        return $model->where('id', '=', $id)->find_all()->current();
    }
} 