<?php foreach($rnd_products as $product):?>
    <div class="col-lg-3 col-md-3 col-sm-4">
        <div class="thumbnail">
            <a data-toggle="lightbox" data-title="<?=$product['name']?>" href="/products_pictures/<?=$product['picture']?>">
                <img class="img-responsive img-home-portfolio preview-image" src="/products_pictures/<?=$product['picture']?>" alt="photo <?=$product['name']?>">
            </a>
            <div class="caption">
                <h4 class="pull-right">$<?=$product['price']?></h4>
                <h4><a href="/product/view?id=<?=$product['productID']?>"><?=$product['name']?></a></h4>
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