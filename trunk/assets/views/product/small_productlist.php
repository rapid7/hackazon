<ul class="thumbnails product-list-inline-small">
    <?php foreach ($special_offers as $so) {
        $offer = $so->product_offers;
        ?>
        <li class="col-xs-12 col-sm-3 col-md-2">
            <div class="thumbnail">
                <div class="special-offer-img">
                    <a class="img-small-wrap" href="/product/view/<?= $offer->productID ?>"><img src="/products_pictures/<?= $offer->picture ?>" alt=""></a>
                </div>
                <div class="caption">
                    <a href="/product/view/<?= $offer->productID ?>"><?= $offer->name ?></a>
                    <p><?= $offer->getAnnotation(30) ?><span class="label label-info pull-right price">$<?= $offer->Price ?></span></p>
                </div>
            </div>
        </li>
    <?php } ?>
</ul>