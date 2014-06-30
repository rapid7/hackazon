<div class="col-md-3">
    <ul class="menu">
        <?php foreach ($sidebar as $value): ?>
            <li><a href="/category/view/<?=$value['categoryID']; ?>"><?=$value['name']; ?></a>
            <?php if(!empty($value['child'])):?>
                <ul>
                    <?php foreach($value['child'] as $subcategory):?>
                        <li><a href="/category/view/<?=$subcategory['categoryID']; ?>"><?=$subcategory['name']; ?></a></li>
                    <?php endforeach;?>
                </ul>
            <?endif;?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>


