<?php for($i=1;$i<6;$i++):?>
    <?php if($i>$product['customers_rating']):?>
        <span class="glyphicon glyphicon-star-empty"></span>
    <?php else:?>
        <span class="glyphicon glyphicon-star"></span>
    <?php endif;?>
<?php endfor;?>