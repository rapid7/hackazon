<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
    <div class="thumbnail <?= $itemClass ?>">
        <div class="category-list-product">
            <span class="no-product-icon glyphicon glyphicon-question-sign"> </span>
        </div>
        <div class="caption">
            <a href="/category/view?id=<?= $item->categoryID ?>"><?= $item->name ?></a>
        </div>
        <a href="/category/view?id=<?= $item->categoryID ?>" class="btn btn-default btn-block"> No products </a>
    </div>
</div>