<?php
/**
 * @var \App\Model\Product $product
 * @var callable $_
 */
?>
<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
    <div class="thumbnail">
        <div class="img-box">
            <a href="/product/view?id=<?php echo $product->productID; ?>"><img class="img-responsive img-home-portfolio"
                    src="/products_pictures/<?php echo $product->picture; ?>" alt="<?php echo $product->name; ?>"></a>
        </div>

        <div class="caption">
            <a href="/product/view?id=<?php echo $product->productID; ?>" title="<?=$product->name?>"><?php $_trim($product->name, 50); ?></a>

            <p class="product-annotation"><span class="text-block" title="<?=$product->description?>"><?php $_trim($product->description, 60); ?></span>
                <span class="label label-info price pull-right">$<?php echo $product->Price; ?></span>
                <?php $_addToCartLink($product->id()); ?>
            </p>
        </div>
    </div>
</div>