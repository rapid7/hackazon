<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
    <div class="thumbnail <?=$itemClass?>">
        <a href="/product/view?id=<?=$product->productID?>">
            <div class="label label-info price">$ <?=$product->Price?></div>
            <img class="category-list-product" data-hover="/products_pictures/<?=$product->picture?>" src="/products_pictures/<?=$product->picture?>" alt="">
        </a>
        <div class="caption">
            <a href="/product/view?id=<?=$product->productID?>"><?=$product->name?></a>
        </div>
        <a href="/category/view?id=<?= $item->categoryID ?>" class="btn btn-default btn-block"> more products </a>
    </div>
</div>