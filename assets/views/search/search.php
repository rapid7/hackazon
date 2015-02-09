<div class="row">
    <form role="search" action="/search" method="get" id="searchForm" >
        <input type="hidden" name="id" value="<?= $_($search_category['value']); ?>" />
        <div class="col-xs-12 col-md-12">
            <div class="input-group" style="margin-bottom: 10px;">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" id="searchLabel"><?= $search_category['label'] ?> <span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu" id="searchValue">
                        <li class="dropdown"><a href="#">All</a></li>
                        <?php /* foreach ($search_subcategories as $key => $value): ?>
                          <li><a href="#" data-item-id="<?= $key ?>"><?= $value ?></a></li>
                          <?php endforeach; */ ?>
                        <?php foreach ($sidebar as $value): ?>
                            <li class="dropdown dropdown-submenu"><a href="/category/view?id=<?= $value->categoryID; ?>" data-item-id="<?= $value->categoryID; ?>"><?= $value->name; ?></a>
                                <?php if (count($value->childs) > 0): ?>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($value->childs as $subcategory): ?>
                                            <li><a href="/category/view?id=<?= $subcategory->categoryID; ?>" data-item-id="<?= $subcategory->categoryID; ?>"><?= $subcategory->name; ?></a>
                                            <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <!-- /btn-group -->
                <input type="text" class="form-control" placeholder="Search products..." maxlength="100" name="searchString" value="<?php $_($searchString); ?>">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">Search!</button>
                </span>
            </div>
            <!-- /input-group -->
        </div>
    </form>
</div>