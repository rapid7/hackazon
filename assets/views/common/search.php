<div class="row">
    <form role="search" action="/search" method="get" id="searchForm">
        <input type="hidden" name="id" value="<?= $search_category['value'] ?>" />
        <div class="col-xs-11 col-md-11">
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                            id="searchLabel"><?= $search_category['label'] ?> <span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu" id="searchValue">
                        <?php foreach ($search_subcategories as $key => $value): ?>
                            <li><a href="#" data-item-id="<?= $key ?>"><?= $value ?></a></li>
                        <?php endforeach ?>
                    </ul>
                </div>
                <!-- /btn-group -->
                <input type="text" class="form-control"  maxlength="100" name="searchString"
                       value="">
            </div>
            <!-- /input-group -->
        </div>
        <div class="col-lg-1">
            <button type="submit" class="btn btn-default">Go</button>
        </div>
    </form>
</div>