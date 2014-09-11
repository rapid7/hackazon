<?php
/**
 * @var array $productBlock
 * @var \App\Model\Product $product
 * @var callable $_
 */
?>
<!-- START CONTENT ITEM -->
<div class="row">
    <div class="col-xs-12">
        <div class="well well-small">
            <ul class="nav nav-list">
                <li class="nav-header"><?php echo $productBlock['title']; ?></li>
                <?php foreach ($productBlock['products'] as $product): ?>
                    <li><a href="/product/view?id=<?php echo $product->productID; ?>"><?php echo $product->name; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<!-- END CONTENT ITEM -->