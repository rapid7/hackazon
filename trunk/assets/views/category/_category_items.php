<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?= $pageTitle ?></h1>
        <?php include($common_path . "breadcrumbs.php") ?>
    </div>
</div>
<?php
if (count($subCategories) > 0) {
    $rows = count($subCategories) % 4 == 0 ? count($subCategories) / 4 : ceil(count($subCategories) / 4);
    for ($r = 0; $r < $rows; $r++) {
        ?>
        <div class="row">
            <div class="col-xs-12">
                <!--START CONTENT ITEM-->
                <div class="product-list-inline-large">
                    <?php $count = count($subCategories) < 4 ? count($subCategories) : 4;
                    $itemClass = 'light';
                    for ($cnt = 0; $cnt < $count; $cnt++) {
                        $item = array_shift($subCategories);
                        $product = $item->products->limit(1)->find();
                        ?>
                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                            <div class="thumbnail <?=$itemClass?>">
                                <a href="/product/view/<?=$product->productID?>">
                                    <div class="label label-info price">$ <?=$product->Price?></div>
                                    <img class="category-list-product" data-hover="/products_pictures/<?=$product->thumbnail?>" src="/products_pictures/<?=$product->thumbnail?>" alt="">
                                </a>
                                <div class="caption">
                                    <a href="/product/view/<?=$product->productID?>"><?=$product->name?></a>
                                </div>
                                <a href="/category/view/<?= $item->categoryID ?>" class="btn btn-default btn-block"> more products </a>
                            </div>
                        </div>
                        <?php
                        $itemClass = $itemClass == 'light' ? 'dark' : 'light';
                    } ?>
                </div>
                <!--END CONTENT ITEM-->
            </div>
        </div>
    <?php
    }
} else {
    ?>
    <div class="alert alert-info">No categories found.</div>
<?php } ?>