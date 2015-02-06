<script>
    $(function() {
        var queryParameters = {}, queryString = location.search.substring(1), re = /([^&=]+)=([^&]*)/g, m;
        while (m = re.exec(queryString)) {
            queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
        }
        function remove(arr, item) {
            for (var i = arr.length; i--; ) {
                if (arr[i] == item) {
                    console.log(arr[i])
                    arr.splice(i, 1);
                }
            }
        }
        function search(link) {
            $.ajax({
                url: location.origin + link,
                data: {},
                dataType: 'html'
            }).done(function(data) {
                history.pushState(null, null, location.origin + link)
                $("#container").html(data);
                $('.nivoslider').nivoSlider();
                $('#slider2').carousel();
            });
        }
        $("#filter-block [data-filter=brands]").on("click", function(e) {
            queryParameters['brands'] = $(this).find("input[type=hidden]").val();
            if ($(this).parent().hasClass("active")) {
                queryParameters['brands'] = "";
            }
            search(location.pathname + "?" + $.param(queryParameters));
            e.preventDefault();
        });

        $("#filter-block [data-filter=price]").on("click", function(e) {
            queryParameters['price'] = $(this).find("input[type=hidden]").val();
            if ($(this).parent().hasClass("active")) {
                queryParameters['price'] = "";
            }
            search(location.pathname + "?" + $.param(queryParameters));
            e.preventDefault();
        });

        $("#filter-block [data-filter=quality]").on("click", function(e) {
            queryParameters['quality'] = $(this).find("input[type=hidden]").val();
            if ($(this).parent().hasClass("active")) {
                queryParameters['quality'] = "";
            }
            search(location.pathname + "?" + $.param(queryParameters));
            e.preventDefault();
        });
        $("#filter-block input[type=reset]").on("click", function(e) {
            queryParameters['quality'] = "";
            queryParameters['price'] = "";
            queryParameters['brands'] = "";
            search(location.pathname + "?" + $.param(queryParameters));
            e.preventDefault();
        });
        $(".pagination a").on("click", function(e) {
            search($(this).attr("href"));
            e.preventDefault();
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
                    <form action="/search" name="filter-block">
                        <ul class="nav nav-list">
                            <li class="nav-header">Brands</li>
                            <?php
                            foreach ($filterFabric->getFilter('Brand')->getVariants() as $id => $name) {
                                //$isChecked = in_array($id, $filterFabric->getFilter('brandFilter')->getValue()) ? 'checked' : '';
                                $isChecked = $brand == $id ? 'active' : '';
                                ?>
                                <li class="<?= $isChecked ?>">
                                    <a href="#" data-filter="brands">
                                        <span class="glyphicon glyphicon-ok"></span>&nbsp;<?= $name ?>
                                        <input type="hidden" data-type="filter" name="<?= $filterFabric->getFilter('Brand')->getFieldName() ?>[]" value="<?= $id ?>" data-type="filter-param">
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                            <hr>
                            <li class="nav-header">Price</li>
                            <?php
                            foreach ($filterFabric->getFilter('Price')->getVariants() as $id => $name) {
                                //$isChecked = $filterFabric->getFilter('priceFilter')->getValue() == $id ? 'checked' : '';
                                $isChecked = $price == $id ? 'active' : '';
                                $elemId = 'price-' . $id;
                                ?>
                                <li class="<?= $isChecked ?>">
                                    <a href="#" data-filter="price">
                                        <span class="glyphicon glyphicon-ok"></span>&nbsp;<?= $filterFabric->getFilter('Price')->getLabel($id) ?>
                                        <input type="hidden" name="<?= $filterFabric->getFilter('Price')->getFieldName() ?>" id="<?= $elemId ?>" value="<?= $id ?>" <?= $isChecked ?> data-type="filter-param"   />
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                            <hr>
                            <li class="nav-header">Quality</li>
                            <?php
                            foreach ($filterFabric->getFilter('Quality')->getVariants() as $id => $name) {
                                //$isChecked = $filterFabric->getFilter('qualityFilter')->getValue() == $id ? 'checked' : '';
                                $isChecked = $quality == $id ? 'active' : '';
                                $elemId = 'quality-' . $id;
                                ?>
                                <li class="<?= $isChecked ?>">
                                    <a href="#" data-filter="quality">
                                        <span class="glyphicon glyphicon-ok"></span>&nbsp;<?= $name ?>
                                        <input type="hidden" name="<?= $filterFabric->getFilter('Quality')->getFieldName() ?>" id="<?= $elemId ?>" value="<?= $id ?>" <?= $isChecked ?> data-type="filter-param"  />
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                        <br>
                        <div class="clearfix">
                            <input type="reset" value="Reset" class="btn btn-warning pull-right" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- END CONTENT ITEM -->
        <!-- START CONTENT ITEM -->
        <div class="row">
            <div class="hidden-xs col-sm-12">
                <?php include __DIR__.'/../common/_side_slider.php'; ?>
            </div>
        </div>
    </div>
    <!-- END CONTENT ITEM -->
    <div class="col-xs-12 col-sm-9">
        <!-- START CONTENT ITEM -->
        <div class="row">
            <div class="col-xs-12 col-sm-9">
                <h2><?= ($searchString == "") ? "Search" : $pageTitle; ?></h2>
            </div>
        </div>
        <!-- END CONTENT ITEM -->
        <?php
        if (count($pager->current_items()) > 0) {
            $products_count = count($currentItems);
            ?>
            <div class="row product-list-inline-small">

                <?php foreach ($currentItems as $item): ?>
                    <div class="col-sm-6 col-md-3">
                        <div class="thumbnail">
                            <a href="/product/view?id=<?= $item->productID ?>"><img src="/products_pictures/<?= $item->picture ?: $item->big_picture?>" alt="">
                            </a>
                            <div class="caption">
                                <a href="/product/view?id=<?= $item->productID ?>" title="<?= $item->name ?>">
                                    <?php $_trim($item->name); ?>
                                </a>
                                <p>
                                    <small title="<?= $item->description ?>"><?php $_trim($item->description, 80); ?></small>
                                    <span class="label label-info price pull-right">
                                        $<?= $item->Price ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Render pager links -->
            <ul class="pagination pull-right">
                <?php for ($i = 1; $i <= $pager->num_pages; $i++): ?>
                    <li>
                        <a href="<?php echo $pager->url($i); ?>"><?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php } else { ?>
            <div class="alert alert-info">No products found.</div>
        <?php } ?>
    </div>
</div>
