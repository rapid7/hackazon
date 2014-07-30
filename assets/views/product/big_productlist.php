<ul class="thumbnails product-list-inline-small">
    <?php foreach ($related as $offer) {
        ?>
        <li class="col-xs-3">
            <div class="thumbnail">
                <div class="special-offer-big-img">
                    <a href="/product/view/<?= $offer->productID ?>"><img src="/products_pictures/<?= $offer->picture ?>" alt=""></a>
                </div>
                <div class="caption">
                    <a href="/product/view/<?= $offer->productID ?>"><?= $offer->name ?></a>
                    <p><?= $offer->getAnnotation(30) ?><span class="label label-info price pull-right">$<?= $offer->Price ?></span></p>
                </div>
            </div>
        </li>
    <?php } ?>
</ul>