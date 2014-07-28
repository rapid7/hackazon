<?php
/** @var array|\App\Model\Product $product */
$productRating = is_array($product) ? $product['customers_rating'] : $product->customers_rating;
?>
<?php for($i=1;$i<6;$i++):?>
    <?php if($i > $productRating):?>
        <span class="glyphicon glyphicon-star-empty"></span>
    <?php else:?>
        <span class="glyphicon glyphicon-star"></span>
    <?php endif;?>
<?php endfor;?>