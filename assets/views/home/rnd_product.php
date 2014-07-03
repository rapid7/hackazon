<?php foreach($rnd_products as $product):?>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="thumbnail">
            <img class="img-responsive img-home-portfolio" src="/products_pictures/<?=$product['picture']?>" alt="photo <?=$product['name']?>">
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
<?php endforeach;?>