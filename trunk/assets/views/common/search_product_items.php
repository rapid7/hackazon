<script>
    $(function() {
        var queryParameters = {}, queryString = location.search.substring(1), re = /([^&=]+)=([^&]*)/g, m;
        while (m = re.exec(queryString)) {
            queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
        }
        $("#filter-block input[data-filter=brands]").on("change", function(e) {
            queryParameters['brands'] = $(this).val();
            location.search = $.param(queryParameters);
        });

        $("#filter-block input[data-filter=price]").on("change", function(e) {
            queryParameters['price'] = $(this).val();
            location.search = $.param(queryParameters);
        });

        $("#filter-block input[data-filter=quality]").on("change", function(e) {
            queryParameters['quality'] = $(this).val();
            location.search = $.param(queryParameters);
        });
    });
</script>
<div class="row">
    <div class="col-xs-12 col-sm-3">
        <br/>
        <!-- START CONTENT ITEM -->
        <div class="row">
            <div class="col-xs-12">
                <div class="well well-small" id="filter-block">
                    <form action="/search">
                        <legend>Brands</legend>
                        <?php
                        foreach ($filterFabric->getFilter('Brand')->getVariants() as $id => $name) {
                            $isChecked = in_array($id, $filterFabric->getFilter('brandFilter')->getValue()) ? 'checked' : '';
                            ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" data-type="filter"
                                           name="<?= $filterFabric->getFilter('Brand')->getFieldName() ?>[]"
                                           value="<?= $id ?>" <?= $isChecked ?> data-type="filter-param" data-filter="brands">
                                           <?= $name ?>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                        <hr />
                        <legend>Price</legend>
                        <?php
                        foreach ($filterFabric->getFilter('Price')->getVariants() as $id => $name) {
                            $isChecked = $filterFabric->getFilter('priceFilter')->getValue() == $id ? 'checked' : '';
                            $elemId = 'price-' . $id;
                            ?>
                            <div class="radio">
                                <label for="<?= $elemId ?>">
                                    <input type="radio" name="<?= $filterFabric->getFilter('Price')->getFieldName() ?>" id="<?= $elemId ?>" value="<?= $id ?>" <?= $isChecked ?> data-type="filter-param"  data-filter="price" />
                                    <?= $filterFabric->getFilter('Price')->getLabel($id) ?>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                        <hr />
                        <legend>Quality</legend>
                        <?php
                        foreach ($filterFabric->getFilter('Quality')->getVariants() as $id => $name) {
                            $isChecked = $filterFabric->getFilter('qualityFilter')->getValue() == $id ? 'checked' : '';
                            $elemId = 'quality-' . $id;
                            ?>
                            <div class="radio">
                                <label for="<?= $elemId ?>">
                                    <input type="radio" name="<?= $filterFabric->getFilter('Quality')->getFieldName() ?>" id="<?= $elemId ?>" value="<?= $id ?>" <?= $isChecked ?> data-type="filter-param"  data-filter="quality" />
                                    <?= $name ?>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <!-- END CONTENT ITEM -->
        <!-- START CONTENT ITEM -->
        <div class="row">
            <div class="hidden-xs col-sm-12">
                <div class="slider-wrapper theme-light">
                    <div class="ribbon"></div>
                    <div id="slider2" class="nivoslider">
                        <img src="/images/banner_01-v3.jpg" alt=""
                             title="This is an example of an optional long caption text"/>
                        <img src="/images/banner_02-v3.jpg" alt="" title=""/>
                        <img src="/images/banner_03-v3.jpg" alt="" title=""/>
                        <img src="/images/banner_04-v3.jpg" alt="" title="Another caption"/>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
    <!-- END CONTENT ITEM -->
    <div class="col-xs-12 col-sm-9">
        <!-- START CONTENT ITEM -->
        <div class="row">
            <div class="col-xs-12 col-sm-9">
                <h2><?= $_($pageTitle, 'searchString'); ?></h2>
            </div>
        </div>
        <!-- END CONTENT ITEM -->
        <?php
        if (count($products) > 0) {
            $rows = count($products) % 4 == 0 ? count($products) / 4 : ceil(count($products) / 4);
            for ($r = 0; $r < $rows; $r++) {
                ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row product-list-inline-small">
                            <?php
                            $count = count($products) < 4 ? count($products) : 4;
                            for ($cnt = 0; $cnt < $count; $cnt++) {
                                $item = array_shift($products);
                                ?>
                                <div class="col-xs-4 col-sm-3">
                                    <div class="thumbnail">
                                        <a href="/product/view/<?= $item->productID ?>"><img
                                                src="/products_pictures/<?= $item->thumbnail ?>" alt=""></a>

                                        <div class="caption">
                                            <a href="/product/view/<?= $item->productID ?>"><?= $item->name ?></a>

                                            <p><?= $item->getAnnotation(40) ?> <span
                                                    class="label label-info price pull-right">$<?= $item->Price ?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
        <?php } ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="alert alert-info">No products found.</div>
<?php } ?>        <!-- END CONTENT ITEM -->
    </div>
</div>
