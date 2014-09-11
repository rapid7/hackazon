<!--<div class="col-md-3">-->
<div class="dropdown sidebar-menu-inside">
    <button class="btn btn-default hide" data-toggle="dropdown" id="sidebar-link">Shop By Department <b class="caret"></b></button>
    <ul class="dropdown-menu menu" role="menu" aria-labelledby="sidebar-link">
        <?php foreach ($sidebar as $value): ?>
            <li><a href="/category/view?id=<?= $value->categoryID; ?>"><?= $value->name; ?></a>
                <?php if (count($value->childs) > 0): ?>
                    <ul>
                        <?php foreach ($value->childs as $subcategory): ?>
                            <li><a href="/category/view?id=<?= $subcategory->categoryID; ?>"><?= $subcategory->name; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<!--</div>-->


