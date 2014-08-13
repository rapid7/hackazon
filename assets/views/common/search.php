<div class="row">
    <div class="col-lg-1">
        <span style="font-size: 16px; color: #d9534f">Search</span>
    </div>
    <form role="search" action="/search" method="get" id="searchForm">
        <input type="hidden" name="id" value="<?= $search_category['value'] ?>">
        <div class="col-lg-10">
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
                <input type="text" class="form-control" style="width: 170px;" maxlength="100" name="searchString"
                       value="<?php echo isset($searchString) ? $searchString : ''; ?>">
            </div>
            <!-- /input-group -->
        </div>
        <div class="col-lg-1">
            <button type="submit" class="btn btn-default">Go</button>
        </div>
    </form>
</div>