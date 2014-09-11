<ul class="thumbnails product-list-inline-small">
    <?php foreach ($related as $offer) {
        ?>
        <li class="col-xs-3">
            <div class="thumbnail">
                <div class="special-offer-big-img">
                    <a class="img-wrap" href="/product/view?id=<?= $offer->productID ?>"><img class="img-responsive" src="/products_pictures/<?= $offer->picture ?>" alt=""></a>
                </div>
                <div class="caption">
                    <a href="/product/view?id=<?= $offer->productID ?>" title="<?=$offer->name?>"><?php $_trim($offer->name, 50) ?></a>
                    <p><?php $_trim($offer->description, 80) ?><span class="label label-info price pull-right">$<?= $offer->Price ?></span></p>
                </div>
            </div>
        </li>
    <?php } ?>
</ul>