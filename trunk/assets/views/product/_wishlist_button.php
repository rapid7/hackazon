<?php
$userObj = $this->pixie->auth->user();
$userWishLists = $userObj ? $userObj->wishlists->find_all()->as_array() : array();
?>
<div class="wish-list-button-block js-wish-list-button-block">
    <?php if ($userObj): ?>
        <?php $userDefaultWishList = $userObj->getDefaultWishList(); ?>
        <?php if (!$productObj->isInUserWishList($userObj)): ?>
            <?php if (count($userWishLists) >= 2): ?>
                <div class="dropdown pull-right add-to-wish-list-dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" id="addProduct_<?php echo $productObj->id(); ?>ToWishList" data-toggle="dropdown">
                        Add To Wish List
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="addProduct_<?php echo $productObj->id(); ?>ToWishList">
                        <?php foreach ($userWishLists as $userWishList): ?>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#"
                                    class="js-add-to-wish-list" data-id="<?php echo $productObj->productID; ?>"
                                    data-wishlist-id="<?php echo $userWishList->id; ?>">
                                <?php echo $userWishList->name; ?>
                            </a></li>
                        <?php endforeach; ?>


                    </ul>
                </div>
            <?php else: ?>
                <a href="#" class="btn btn-default pull-right js-add-to-wish-list" data-id="<?php echo $productObj->productID; ?>"
                    data-wishlist-id="<?php echo $userDefaultWishList ? $userDefaultWishList->id : ''; ?>">Add To Wish List</a>
            <?php endif; ?>

        <?php else: ?>
            <a href="#" class="btn btn-warning pull-right js-remove-from-wish-list" data-id="<?php echo $productObj->id(); ?>">Remove From Wish List</a>
        <?php endif; ?>
    <?php else: ?>
        <a href="<?php echo $controller->generateUrl('default', array(
                'controller' => 'user',
                'action' => 'login',
            )) . '?return_url=' . rawurlencode($controller->request->server('REQUEST_URI'));
        ?>" class="btn btn-default pull-right">Add To Wish List</a>
    <?php endif; ?>
</div>