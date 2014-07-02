<?php if(!empty($products)):?>
    <?php foreach($products as $product):?>
        <div class="col-sm-4 col-lg-4 col-md-4">
            <div class="thumbnail">
                <img src="/products_pictures/<?=$product['thumbnail']?>" alt="">
                    <div class="caption">
                        <h4 class="pull-right">$<?=$product['price']?></h4>
                        <h4><a href="/product/view/<?=$product['productID']?>"><?=$product['name']?></a></h4>
                        <p><?=$product['annotation']?></p>
                    </div>
                    <div class="ratings">
                        <p class="pull-right"><?=$product['customers_votes']?> reviews</p>
                        <p><?php include($common_path."rating_stars.php")?></p>
                    </div>
            </div>
        </div>
    <?php endforeach;?>
<?php else:?>
    <span>No products found.</span>
<?php endif;?>