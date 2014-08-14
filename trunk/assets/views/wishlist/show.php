<?php
$isWishListOwner = $user->id() == $wishList->user_id;
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?php echo $wishList->name; ?></h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li>Wish Lists</li>
                <li><?php echo $wishList->name; ?><?php if ($wishList->isDefault()): ?> (Default)<?php endif; ?></li>
            </ol>
        </div>
    </div>

    <div class="row wishlist" data-access="<?php if ($isWishListOwner): ?>owner<?php else: ?>guest<?php endif; ?>"
        data-id="<?php echo $wishList->id(); ?>" data-name="<?php echo htmlspecialchars($wishList->name); ?>"
        data-type="<?php echo $wishList->type; ?>"
        data-token="<?php echo $this->pixie->vulnService->getToken('wishlist'); ?>">
        <div class="col-lg-3">
            <div class="collapsible-block js-wish-my-lists">
                <div class="block-header js-block-header">
                    <h4>Your Wish Lists</h4>
                </div>
                <div class="block-content js-block-content">
                    <ul class="list-group">
                    <?php foreach ($user->lists as $list): ?>
                        <li class="list-group-item <?php if ($wishList->id() == $list->id()): ?> list-group-item active<?php endif; ?>">
                            <span class="badge"><?php echo $list->items->count_all(); ?></span>
                            <a href="<?php echo $controller->generateUrl('default', array(
                                'controller' => 'wishlist', 'action' => 'view', 'id' => $list->id))?>"><?php $_($list->name, 'name'); ?></a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-9">

            <div class="clearfix class=">

                <?php include __DIR__ . '/_search_form.php'; ?>

            <div class="btn-group pull-right top-buttons">
                <button type="button" class="btn btn-default js-add-wish-list">Add Wish List</button>
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