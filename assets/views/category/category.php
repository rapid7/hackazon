<div class="section">
    <div class="container category-products">
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

        } else { ?>
            <div class="row">
                <div class="col-xs-12">
                    <!--START CONTENT ITEM-->
                    <div class="product-list-inline-large">
                        <?php
                        $itemClass = 'light';
                        $index = 0;
                        foreach ($products as $product) {
                            $item = array_shift($subCategories); ?>
                            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <?php $_addToCartLink($product->id()); ?>
                                <div class="thumbnail <?=$itemClass?>">
                                    <div class="img-box">
                                        <a href="/product/view?id=<?=$product->productID?>">
                                            <div class="label label-info price">$ <?=$product->Price?></div>
                                            <img class="category-list-product" data-hover="/products_pictures/<?=$product->picture?>" src="/products_pictures/<?=$product->picture?>" alt="">
                                        </a>
                                    </div>
                                    <div class="caption">
                                        <a href="/product/view?id=<?=$product->productID?>"><?=$product->name?></a>
                                    </div>
                                    <div class="ratings">
                                        <p><?php include($common_path."rating_stars.php"); ?></p>
                                    </div>
                                </div>
                            </div>
                           <?php
                            if (($index + 1) % 4 == 0 || $index == count($products) - 1) {
                                echo "<div class=\"clearfix\"></div>";
                            }
                            $index++;
                            $itemClass = $itemClass == 'light' ? 'dark' : 'light';
                        } ?>
                    </div>
                    <?php $_pager($pager, '/category/view?id=' . $_($categoryID) . '&page=#page#'); ?>
                    <!--END CONTENT ITEM-->
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>


