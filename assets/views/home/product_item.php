<?php
/**
 * @var \App\Model\Product $product
 * @var callable $_
 */
?>
<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
    <div class="thumbnail">
        <div class="img-box">
            <a href="/product/view/<?php echo $product->productID; ?>"><img class="img-responsive img-home-portfolio"
                    src="/products_pictures/<?php echo $product->picture; ?>" alt="<?php echo $product->name; ?>"></a>
        </div>

        <div class="caption">
            <a href="/product/view/<?php echo $product->productID; ?>"><?php echo $product->name; ?></a>

            <p class="product-annotation"><span class="text-block"><?php echo $product->getAnnotation(30); ?></span>
                    <span class="label label-info price pull-right">$<?php echo $product->Price; ?></span>
            </p>
        </div>
    </div>
</div>