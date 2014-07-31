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

    <div class="row wishlist">
        <div class="col-lg-3">
            <div class="collapsible-block js-wish-my-lists">
                <div class="block-header js-block-header">
                    <h4>Your Wish Lists</h4>
                </div>
                <div class="block-content js-block-content">
                    <ul>
                    <?php foreach ($user->lists as $list): ?>
                        <li><a href="<?php echo $controller->generateUrl('default', array(
                                'controller' => 'wishlist', 'action' => 'show', 'id' => $list->id))?>"><?php echo $list->name; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <?php echo count($user->lists); ?>
        </div>
    </div>
</div>