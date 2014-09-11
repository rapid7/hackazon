<?php
use App\Model\WishList;

$isWishListOwner = isset($user) && $user->id() == $wishList->user_id;
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?php echo $wishList->name; ?></h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/wishlist">Wish Lists</a></li>
                <li><?php echo $wishList->name; ?><?php if ($wishList->isDefault()): ?> (Default)<?php endif; ?></li>
            </ol>
        </div>
    </div>
    <div class="row wishlist" data-access="<?php if ($isWishListOwner): ?>owner<?php else: ?>guest<?php endif; ?>"
        data-id="<?php echo $wishList->id(); ?>" data-name="<?php echo htmlspecialchars($wishList->name); ?>"
        data-type="<?php echo $wishList->type; ?>"
        data-token="<?php echo $this->pixie->vulnService->getToken('wishlist'); ?>">
        <div class="col-lg-3">
            <?php if (isset($user)) : ?>
            <div class="collapsible-block js-wish-my-lists">
                <div class="block-header js-block-header">
                    <h4><a class="toggle">Your Wish Lists</a></h4>
                </div>
                <div class="block-content js-block-content">
                    <ul class="list-group">
                    <?php foreach ($user->lists as $list): ?>
                            <li class="list-group-item <?php if ($wishList->id() == $list->id()): ?> list-group-item active<?php endif; ?>">
                                <span class="badge"><?php echo $list->items->count_all(); ?></span>
                                <a href="<?php echo $controller->generateUrl('default', array(
                                    'controller' => 'wishlist', 'action' => 'view', 'id' => $list->id))?>"><?php $_($list->name, 'name'); ?></a>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
            <?php endif;?>
            <?php
            if (isset($user)) {
                foreach ($user->wishlistFollowers as $wishlistFollower) : ?>
                <?php if (empty($wishlistFollower->lists)) continue;?>
                <div class="collapsible-block js-wish-my-lists" data-id="<?php echo $wishlistFollower->id?>">
                    <hr />
                    <div class="block-header js-block-header">
                        <h4><a class="toggle"><?php echo $wishlistFollower->username;?></a></h4>
                    </div>
                    <div class="block-content js-block-content" style="<?php if ($wishList->user_id != $wishlistFollower->id) echo 'display: none';?>">
                        <ul class="list-group">
                            <?php foreach ($wishlistFollower->lists as $list): ?>
                                <?php if (WishList::TYPE_PUBLIC != $list->type) { continue; } ?>
                                <li class="list-group-item <?php if ($wishList->id() == $list->id()): ?> list-group-item active<?php endif; ?>">
                                    <span class="badge"><?php echo $list->items->count_all(); ?></span>
                                    <a href="<?php echo $controller->generateUrl('default', array(
                                        'controller' => 'wishlist', 'action' => 'view', 'id' => $list->id))?>"><?php $_($list->name, 'name'); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a class="remove_follower" onclick="return false;" href="javascript:void(0);"><span class="glyphicon glyphicon-remove" style="color:#d58512"></span> Remove person</a>
                    </div>
                </div>
            <?php endforeach;
            }?>
        </div>

        <div class="col-lg-9">

            <div class="clearfix">

                <?php include __DIR__ . '/_search_form.php'; ?>

            <div class="btn-group pull-right top-buttons">
                <?php if (isset($user)) : ?><button type="button" class="btn btn-default js-add-wish-list">Add Wish List</button><?php endif;?>
                <?php if (isset($wishList) && $wishList && $isWishListOwner): ?>
                    <button type="button" class="btn btn-default js-edit-wish-list">Edit</button>
                    <button type="button" class="btn btn-default js-delete-wish-list">Delete</button>
                <?php endif; ?>
            </div>

            </div>

            <div class="products clearfix">
                <div class="clearfix product-list">
                <?php
                $productListData = ['products' => $products, 'hide_container' => true];
                $perRow = 3;
                $productPages = ceil($productCount / $perPage);
                ?>
                <?php include __DIR__ . '/../home/product_list.php'; ?>
                </div>

                <?php if ($productPages > 1): ?>
                    <ul class="pagination pull-right clearfix">
                        <li><a href="/wishlist/view/<?php echo $wishList->id() . ($page > 1 ? '?page=' . max(1, $page - 1) : '');
                            ?>" class="<?php if ($page == 1): ?>disabled<?php endif; ?>">&laquo;</a></li>
                        <?php for ($iPage = 1; $iPage <= $productPages; $iPage++): ?>
                            <li <?php if ($iPage == $page): ?>class="active"<?php endif; ?>><a
                                    href="/wishlist/view/<?php echo $wishList->id()
                                    . ($iPage == 1 ? '' : '?page=' . $iPage); ?>"><?php echo $iPage; ?></a></li>
                        <?php endfor; ?>
                        <li><a href="/wishlist/view/<?php echo $wishList->id() . ($page < $productPages
                            ? '?page=' . min($productPages, $page + 1) : ''); ?>" class="<?php
                            if ($page == $productPages): ?>disabled<?php endif; ?>">&raquo;</a></li>
                    </ul>
                <?php endif; ?>

            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/_add_form.php'; ?>