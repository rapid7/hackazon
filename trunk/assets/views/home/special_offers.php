<?php $j=1;?>
<?php foreach($special_offers as $product):?>
    <?php if($j==1 || $j==4) echo '<div class="row">'?>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="thumbnail">
                <img class="img-responsive img-home-portfolio" src="/products_pictures/<?=$product['picture']?>" alt="<?=$product['name']?>">
                <div class="caption">
                    <h4 class="pull-right">$<?=$product['price']?></h4>
                    <h4><a href="/product/view/<?=$product['productID']?>"><?=$product['name']?></a></h4>
                    <p><?=$product['annotation']?></p>
                </div>
                <div class="ratings">
                    <p>
                        <?php include($common_path."rating_stars.php")?>
                    </p>
                </div>
            </div>
        </div>
    <?php $j++;if($j==4 || $j ==7) echo '</div>';?>
<?php endforeach;?>