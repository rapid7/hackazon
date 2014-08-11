<!--<div class="col-md-3">-->
<div class="dropdown sidebar-menu-inside">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="sidebar-link">Shop By Department <b class="caret"></b></a>
    <ul class="dropdown-menu menu">
        <?php foreach ($sidebar as $value): ?>
            <li><a href="/category/view/<?=$value->categoryID; ?>"><?=$value->name; ?></a>
            <?php if(count($value->childs) > 0):?>
                <ul>
                    <?php foreach($value->childs as $subcategory):?>
                        <li><a href="/category/view/<?=$subcategory->categoryID; ?>"><?=$subcategory->name; ?></a></li>
                    <?php endforeach;?>
                </ul>
            <?php endif;?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<!--</div>-->


