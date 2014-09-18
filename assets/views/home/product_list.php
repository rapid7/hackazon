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
$perRow = isset($perRow) && is_numeric($perRow) ? $perRow : 4;
$colClasses = "col-md-3 col-lg-3";
if ($perRow == 3) {
    $colClasses = "col-md-4 col-lg-4";
}
?>
<?php if (!isset($productListData['hide_container']) || !$productListData['hide_container']): ?>
    <div class="container product-list">
<?php endif; ?>

<?php foreach($productListData['products'] as $product):
    $specialOffer = $product;
    if ($product instanceof App\Model\SpecialOffers) {
        $product = $product->product_offers;
    }

    $product->setAnnotationLength(60);
    $firstInRow = (0 == $j % $perRow && $j <= $lastProductNum);
    $lastInRow = ((0 == ($j + 1) % $perRow || $j == $lastProductNum)); ?>

    <?php if ($firstInRow):  ?>
    <div class="row">
        <div class="col-xs-12">

            <!-- START CONTENT ITEM -->
            <div class="product-list-inline-large">
    <?php endif; ?>
                <div class="col-xs-12 col-sm-6 <?php echo $colClasses; ?>">
                    <div class="thumbnail light product-item" data-id="<?php echo $product->id(); ?>">
                        <?php $_addToCartLink($product->id()); ?>
                        <div class="img-box">
                            <a href="/product/view?id=<?php echo $product->productID; ?>">
                                <span class="label label-info price">$<?php echo $product->Price; ?></span>
                                <!--span class="label label-danger price price-over">$<?php echo $product->Price; ?></span-->
                                <img class="img-home-portfolio" alt="" src="/products_pictures/<?php echo $product->picture; ?>"
                                    alt="<?php echo $product->name; ?>" <?php //data-hover="img/product_04b.jpg" ?>>
                            </a>
                        </div>
                        <div class="caption">
                            <a href="/product/view?id=<?php echo $product->productID; ?>"><?php echo $product->name; ?></a>
                        </div>
                        <div class="ratings">
                            <p><?php include($common_path."rating_stars.php"); ?></p>
                        </div>
                        <a class="btn btn-default btn-block" href="/category/view?id=<?php echo $product->categoryID; ?>">all products in category</a>

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
<?php if (!isset($productListData['hide_container']) || !$productListData['hide_container']): ?>
    </div>
<?php endif; ?>