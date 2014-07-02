<!--<div class="col-md-3">-->
<li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="sidebar-link">Shop By Department <b class="caret"></b></a>
    <ul class="dropdown-menu menu">
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
</li>
<!--</div>-->


