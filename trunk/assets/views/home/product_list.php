<?php
/**
 * @var \App\Model\Product $product
 * @var string $common_path
 * @var array $productListData
 * @var callable $_
 */
$j = 0;
$productCount = count($productListData['products']);
$lastProductNum = $productCount - 1;
$perRow = 4;
?>
<div class="container product-list">
<?php foreach($productListData['products'] as $product):
    $specialOffer = $product;
    if ($product instanceof App\Model\SpecialOffers) {
        $product = $product->product_offers;
    }

    $product->setAnnotationLength(60);
    $firstInRow = (0 == $j % $perRow && $j <= $lastProductNum);
    $lastInRow = ($j > 0 && (0 == ($j + 1) % $perRow || $j == $lastProductNum)); ?>

    <?php if ($firstInRow):  ?>
    <div class="row">
        <div class="col-xs-12">

            <!-- START CONTENT ITEM -->
            <div class="product-list-inline-large">
    <?php endif; ?>
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <div class="thumbnail light">
                        <?php /*<div class="caption">
                            <h4 class="pull-right">$<?php $_($product->Price); ?></h4>
                            <h4><a href="/product/view/<?php $_($product->productID); ?>"><?php $_($product->name); ?></a></h4>
                            <p class="product-description"><?php $_($product->getAnnotation()); ?></p>
                        </div>  */ ?>

                        <div class="img-box">
                            <a href="/product/view/<?php echo $product->productID; ?>">
                                <span class="label label-info price">$<?php echo $product->Price; ?></span>
                                <!--span class="label label-danger price price-over">$<?php echo $product->Price; ?></span-->
                                <img class="img-home-portfolio" alt="" src="/products_pictures/<?php echo $product->picture; ?>"
                                    alt="<?php echo $product->name; ?>" <?php //data-hover="img/product_04b.jpg" ?>>
                            </a>
                        </div>
                        <div class="caption">
                            <a href="/product/view/<?php echo $product->productID; ?>"><?php echo $product->name; ?></a>
                        </div>
                        <div class="ratings">
                            <p><?php include($common_path."rating_stars.php"); ?></p>
                        </div>
                        <a class="btn btn-default btn-block" href="/category/view/<?php echo $product->categoryID; ?>">all products in category</a>

                    </div>
                </div>
    <?php if ($lastInRow): ?>
            </div>
            <!-- END CONTENT ITEM -->

        </div>
    </div>
    <?php endif;
    $j++;
    ?>
<?php endforeach;?>
</div>