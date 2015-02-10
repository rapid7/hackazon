<?php

/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 31.07.2014
 * Time: 11:38
 */

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Model\Product as ProductModel;
use App\Model\User as UserModel;
use App\Model\WishListFollowers as WishListFollowersModel;
use App\Page;
use VulnModule\Config\Annotations as Vuln;


/**
 * Class Wishlist.
 * @package App\Controller
 * @property \App\Model\WishList $model
 */
class Wishlist extends Page {

    /**
     * @var UserModel Current logged in user.
     */
    private $user;

    /**
     * Either shows empty page if user has no lists, or shows the default list.
     * @Vuln\Description("View: wishlist/no_list.")
     */
    public function action_index() {
        $this->prepare();

        // Offer to create a new wishlist.

        if ($this->user == null) {
            $this->view->subview = 'wishlist/no_list';
            return;
        }

        $wishList = $this->model->getUserDefaultWishList($this->user);

        if ($wishList) {
            $this->showDefaultWishList();
        } else {
            $this->view->subview = 'wishlist/no_list';
        }
    }

    /**
     * View wish list.
     * @throws NotFoundException
     * @Vuln\Route(params={"id": "_id_"})
     * @Vuln\Description("View: wishlist/show.")
     */
    public function action_view() {
        $this->prepare();

        $id = $this->request->paramWrap('id');

        /** @var \App\Model\WishList $wishList */
        $wishList = $this->pixie->orm->get('wishlist', $id);

        if (!$wishList || !$wishList->loaded()) {
            throw new NotFoundException();
        }

        if (!$wishList->isVisibleToUser($this->user)) {
            throw new NotFoundException();
        }

        $this->showWishList($wishList);
    }

    /**
     * Create new wish list.
     * @throws \App\Exception\ForbiddenException
     * @Vuln\Route(name="wishlist_new")
     * @Vuln\Description("No view.")
     */
    public function action_new() {
        $this->prepare();

        if (!$this->user) {
            throw new ForbiddenException();
        }

        $name = $this->request->postWrap('name', 'New Wish List');
        $type = $this->request->postWrap('type', \App\Model\WishList::TYPE_PRIVATE);

        if (!$name->raw() || !$type->raw()) {
            if ($this->request->is_ajax()) {
                $this->jsonResponse(['error' => 1]);
                return;

            } else {
                throw new HttpException('Invalid request', 400, 'Bad Request');
            }
        }

        // Check CSRF token only if new wishlist is not the only one.
        if ($this->user->wishlists->count_all()) {
            $this->checkCsrfToken('wishlist_add', null, !$this->request->is_ajax());
        }

        $wishList = $this->model->createNewWishListForUser($this->user, $name, $type);

        if ($this->request->is_ajax()) {
            $this->jsonResponse(['success' => 1, 'id' => $wishList->id()]);

        } else {
            $this->redirect($this->generateUrl('default', array(
                'controller' => 'wishlist'
            )));
        }
    }

    /**
     * @throws \App\Exception\ForbiddenException
     * @Vuln\Route(params={"id": "_id_"})
     * @Vuln\Description("No view.")
     */
    public function action_edit() {
        $this->prepare();

        if (!$this->user) {
            throw new ForbiddenException();
        }

        if ($this->request->method != 'POST') {
            $this->redirect('/wishlist');
        }

        $id = $this->request->paramWrap('id');
        $name = $this->request->postWrap('name', 'New Wish List');
        $type = $this->request->postWrap('type', \App\Model\WishList::TYPE_PRIVATE);

        $wishList = $this->getWishList($id);

        if (!$wishList || !$wishList->loaded()) {
            throw new NotFoundException();
        }

        if (!$wishList->isValidType($type->raw())) {
            if ($this->request->is_ajax()) {
                $this->jsonResponse(['error' => 1, 'message' => 'Invalid "type" parameter.']);
                return;
            } else {
                throw new HttpException("Invalid wishlist type");
            }
        }

        $wishList->name = $name->raw() ? $name : $wishList->name;
        $wishList->type = $type;
        $wishList->save();

        if ($this->request->is_ajax()) {
            $this->jsonResponse(['success' => 1, 'id' => $wishList->id()]);
            return;
        }

        $this->redirect($this->generateUrl('default', array(
            'controller' => 'wishlist'
        )));
    }

    /**
     * Sets given wish list as default for user.
     * @throws \App\Exception\ForbiddenException
     * @throws \Exception
     * @Vuln\Description("No view.")
     */
    public function action_set_default() {
        $this->prepare();
        if ($this->request->method != 'POST') {
            throw new \Exception();
        }

        if (!$this->user) {
            throw new ForbiddenException();
        }

        $id = $this->request->postWrap('id');

        if (!$id) {
            throw new NotFoundException("Missing wishlist id.");
        }
        $wishList = $this->getWishList($id);

        if ($wishList->owner->id() != $this->user->id()) {
            throw new NotFoundException("Missing wishlist");
        }

        $wishList->setAsUserDefaultWishList($this->user);

        if ($this->request->is_ajax()) {
            $this->jsonResponse(['success' => 1]);
            return;
        }

        $this->redirect($this->generateUrl('default', array(
            'controller' => 'wishlist',
            'action' => 'view',
            'id' => $wishList->id()
        )));
    }

    /**
     * Add product to the list.
     * @throws \App\Exception\ForbiddenException
     * @throws \Exception
     * @Vuln\Route(name = "wishlist_add_product", params={"id" : "_id_"})
     * @Vuln\Description("No view.")
     */
    public function action_add_product() {
        $this->prepare();
        if ($this->request->method != 'POST') {
            throw new HttpException("Invalid method: " . $this->request->method, 405, null, "Method Not Allowed");
        }

        if (!$this->user) {
            throw new ForbiddenException();
        }

        $productId = $this->request->paramWrap('id');
        $wishlistId = $this->request->postWrap('wishlist_id');

        /** @var ProductModel $product */
        $product = $this->pixie->orm->get('product', $productId);

        if (!$product->loaded()) {
            $this->jsonResponse(['error' => 1, 'Product with id=' . $productId->getFilteredValue() . ' doesn\'t exist.']);
            return;
        }

        /** @var \App\Model\WishList $wishListModel */
        $wishListModel = $this->pixie->orm->get('wishList');

        if ($product->isInUserWishList($this->user)) {
            $this->jsonResponse(['success' => 1, 'Product with id=' . $productId->getFilteredValue() . ' is in your wish list already.']);
            return;
        }

        if ($wishlistId->raw()) {
            $wishList = $this->pixie->orm->get('wishList', $wishlistId);

        } else {
            $wishList = $wishListModel->getUserDefaultWishList($this->user);
            if (!$wishList || !$wishList->loaded()) {
                $wishList = $wishListModel->createNewWishListForUser($this->user);
            }
        }

        if (!$wishList->loaded() || $wishList->user_id != $this->user->id()) {
            $this->jsonResponse(['error' => 1, 'You can add products only to your own wish lists.']);
            return;
        }

        $item = $wishList->addProductItem($product->id());

        $this->jsonResponse([
            'success' => 1,
            'id' => $item->id(),
            'message' => 'You have successfully added product into your wish list.'
        ]);
    }

    /**
     * Removes product from list
     * @throws \App\Exception\ForbiddenException
     * @throws \Exception
     * @Vuln\Route(name = "wishlist_delete_product", params={"id": "_id_"})
     * @Vuln\Description("No view.")
     */
    public function action_delete_product() {
        $this->prepare();
        if ($this->request->method != 'POST') {
            throw new HttpException("Invalid method: " . $this->request->method, 405, null, "Method Not Allowed");
        }

        if (!$this->user) {
            throw new ForbiddenException();
        }

        $productId = $this->request->paramWrap('id');

        /** @var ProductModel $product */
        $product = $this->pixie->orm->get('product', $productId);

        if (!$product->loaded()) {
            $this->jsonResponse(['error' => 1, 'Product with id=' . $productId . ' doesn\'t exist.']);
            return;
        }

        $this->model->removeProductFromUserWishLists($this->user, $productId);
        $this->jsonResponse(['success' => 1]);
        return;
    }

    /**
     * Removes given wish list.
     * @throws \App\Exception\ForbiddenException
     * @throws \Exception
     * @Vuln\Route(params={"id": "_id_"})
     * @Vuln\Description("No view.")
     */
    public function action_delete() {
        $this->prepare();
        if ($this->request->method != 'POST') {
            throw new HttpException("Invalid method: " . $this->request->method, 405, null, "Method Not Allowed");
        }

        if (!$this->user) {
            throw new ForbiddenException();
        }

        $id = $this->request->paramWrap('id');

        if (!$id->getFilteredValue()) {
            throw new HttpException("Missing wishlist id.");
        }
        $wishList = $this->getWishList($id);

        if ($wishList->owner->id() != $this->user->id()) {
            throw new NotFoundException();
        }

        $this->checkCsrfToken('wishlist', $this->request->post('token'), !$this->request->is_ajax());

        $wishList->delete();

        if ($this->request->is_ajax()) {
            $this->jsonResponse(['success' => 1]);
            return;
        }

        $this->redirect($this->generateUrl('default', array(
            'controller' => 'wishlist'
        )));
    }

    /**
     * Set up important variables for most of actions.
     */
    protected function prepare() {
        $this->user = $this->pixie->auth->user();
        if ($this->user) {
            $this->view->user = $this->user;
        }
        $this->view->pageTitle = "Wish List";
    }

    protected function showDefaultWishList() {
        $wishList = $this->model->getUserDefaultWishList($this->user);
        $this->view->wishList = $wishList;

        if (!$wishList) {
            $this->view->showDefaultPage = true;
            $this->view->subview = 'wishlist/no_list';
            return;
        }

        $this->showWishList($wishList);
    }

    /**
     * @param $id
     * @return mixed|\App\Model\WishList
     */
    public function getWishList($id) {
        $model = new \App\Model\WishList($this->pixie);
        return $model->where('id', '=', $id)->find_all()->current();
    }

    /**
     * Shows single wish list.
     * @param \App\Model\WishList $wishList
     */
    private function showWishList(\App\Model\WishList $wishList) {
        $page = $this->request->getWrap('page', 1);
        $perPage = 9;
        $this->view->page = $page;
        $this->view->perPage = $perPage;
        $this->view->wishList = $wishList;
        $this->view->products = $wishList->products->offset($perPage * ($page->raw() - 1))->limit($perPage)->find_all()->as_array();
        $this->view->productCount = $wishList->products->count_all();
        $this->view->subview = 'wishlist/show';
    }

    /**
     * Search users and wishLists by username or email.
     * @Vuln\Description("No view.")
     */
    public function action_search() {
        $searchQuery = $this->request->postWrap('search');
        $result = $this->model->searchWishLists($searchQuery);
        $this->jsonResponse($result);
    }

    /**
     * @Vuln\Description("No view.")
     */
    public function action_remember() {
        $userId = $this->request->postWrap('user_id');
        $result = $this->model->remember($userId);
        if ($result) {
            $this->jsonResponse(['success' => 1]);
        } else {
            $this->jsonResponse([]);
        }
    }

    /**
     * @throws \Exception
     * @Vuln\Description("No view.")
     */
    public function action_remove_follower() {
        /** @var WishListFollowersModel $item */
        $item = $this->pixie->orm->get('WishListFollowers')
                ->where(array('user_id', '=', $this->pixie->auth->user()->id()),
                        array('and', array('follower_id', '=', $this->request->postWrap('follower_id'))))
                ->find();

        if ($item->loaded()) {
            $item->delete();
            $this->jsonResponse(['success' => 1]);
        } else {
            $this->jsonResponse(['success' => 0]);
        }
    }
}

