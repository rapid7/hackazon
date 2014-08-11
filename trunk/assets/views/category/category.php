<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $pageTitle ?></h1>
                <?php include($common_path . "breadcrumbs.php") ?>
            </div>
        </div>
        <?php if (count($products) > 0) include("_product_items.php");
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
                                if (!$product->loaded()) include("_category_empty_portret.php"); else
                                    include("_category_portret.php");
                                $itemClass = $itemClass == 'light' ? 'dark' : 'light';
                            } ?>
                        </div>
                        <!--END CONTENT ITEM-->
                    </div>
                </div>
            <?php
            }
        }
        ?>
    </div>
</div>


