<div class="row">
    <div class="col-xs-12 col-sm-3">
        <br/>
        <!-- START CONTENT ITEM -->
        <div class="row">
            <div class="col-xs-12">
                <div class="well well-small">
                    <ul class="nav nav-list">
                        <li class="nav-header">Brands</li>
                        <li class="active"><a href="#"><span class="glyphicon glyphicon-ok"></span> Brand A</a></li>
                        <li><a href="#"><span class="glyphicon glyphicon-ok"></span> Brand B</a></li>
                        <li class="active"><a href="#"><span class="glyphicon glyphicon-ok"></span> Brand C</a></li>
                        <hr>
                        <li class="nav-header">Price</li>
                        <li class="active"><a href="#"><span class="glyphicon glyphicon-ok"></span> &euro; 10 - &euro;
                                50</a></li>
                        <li class="active"><a href="#"><span class="glyphicon glyphicon-ok"></span> &euro; 50 - &euro;
                                100</a></li>
                        <li><a href="#"><span class="glyphicon glyphicon-ok"></span> &euro; 100 - &euro; 250</a></li>
                        <hr>
                        <li class="nav-header">Color</li>
                        <li><a href="#"><span class="glyphicon glyphicon-ok"></span> Orange</a></li>
                        <li class="active"><a href="#"><span class="glyphicon glyphicon-ok"></span> Red</a></li>
                        <li><a href="#"><span class="glyphicon glyphicon-ok"></span> Yellow</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END CONTENT ITEM -->
        <!-- START CONTENT ITEM -->
        <div class="row">
            <div class="hidden-xs col-sm-12">
                <?php include $common_path.'/_side_slider.php'; ?>
            </div>
        </div>
    </div>
    <!-- END CONTENT ITEM -->
    <div class="col-xs-12 col-sm-9">
        <!-- START CONTENT ITEM -->
        <div class="row">
            <div class="col-xs-12 col-sm-9">
                <h2><?= $pageTitle ?></h2>
            </div>
        </div>
        <!-- END CONTENT ITEM -->
        <?php if (count($products) > 0) {
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
                                        <a href="/product/view?id=<?= $item->productID ?>"><img
                                                src="/products_pictures/<?= $item->picture ?>" alt=""></a>

                                        <div class="caption">
                                            <a href="/product/view?id=<?= $item->productID ?>"><?= $item->name ?></a>

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
