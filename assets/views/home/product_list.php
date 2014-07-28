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
?>
<?php foreach($productListData['products'] as $product):
    $specialOffer = $product;
    if ($product instanceof App\Model\SpecialOffers) {
        $product = $product->product_offers;
    }

    $product->setAnnotationLength(60);
    $firstInRow = (0 == $j % 5 && $j <= $lastProductNum);
    $lastInRow = ($j > 0 && (0 == ($j + 1) % 5 || $j == $lastProductNum)); ?>

    <?php if ($firstInRow) { echo '<div class="row">'; } ?>
        <div class="col-md-5ths">
            <div class="thumbnail">
                <div class="img-box">
                    <?php
                    $imgUrl = '';
                    if (isset($product->picture)) { $imgUrl = $product->picture; }
                    ?>
                    <img class="img-responsive img-home-portfolio" src="/products_pictures/<?php $_($imgUrl); ?>" alt="<?php $_($product->name); ?>">

                </div>
                <div class="caption">
                    <h4 class="pull-right">$<?php $_($product->Price); ?></h4>
                    <h4><a href="/product/view/<?php $_($product->productID); ?>"><?php $_($product->name); ?></a></h4>
                    <p class="product-description"><?php $_($product->getAnnotation()); ?></p>
                </div>
                <div class="ratings">
                    <p><?php include($common_path."rating_stars.php"); ?></p>
                </div>
            </div>
        </div>
    <?php
    if ($lastInRow) { echo '</div><!-- /.row -->'; }
    $j++;
    ?>
<?php endforeach;?>