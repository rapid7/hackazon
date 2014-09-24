<script>
    $(function () {
        var form = $("#cart_form"),
            link = $("#add_to_cart"),
            counter = 0,
			backBtn = $("#hw-back-btn");
		
        link.click(function (ev) {
            ev.preventDefault();

            var l = link.ladda();

            link.blur();

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                timeout: 15000,
                type: 'POST',
                beforeSend: function () {
                    link.attr('disabled', 'disabled');
                    l.ladda('start');
                },
                complete: function () {
                    link.removeAttr('disbled');
                    l.ladda('stop');
                }

            }).success(function (res) {
                link.removeAttr('disabled');
                link.blur();

                if (!(res && res.product)) {
                    return;
                }

                addTopCartItem(res);
            });

            return false;
        });
		backBtn.click(function(e) {
			if(history.length > 1) {
				history.back();
				e.preventDefault();
			}
		});
    });
</script>
<div class="section">
    <div class="container">

        <div class="row">
            <div class="col-xs-9">
                <h1><?= $product->name; ?></h1>
            </div>
            <div class="col-xs-3">
                <div class="social-icons pull-right">
                    <!-- Replace with something like:
                    <div class="fb-like fb_edge_widget_with_comment fb_iframe_widget" data-send="false" data-layout="button_count" data-width="150" data-show-faces="false" data-font="arial">
                        <span style="height: 20px; width: 107px; ">
                            <iframe id="f36680bf28" name="f1bd6447bc" scrolling="no" style="border: none; overflow: hidden; height: 20px; width: 107px; " title="Like this content on Facebook." class="fb_ltr" src="http://www.facebook.com/plugins/like.php"></iframe>
                        </span>
                    </div>
                    -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php include($common_path . "multiple_breadcrumbs.php") ?>
            </div>
        </div>
        <div class="row product-detail" data-id="<?= $product->productID; ?>">
            <div class="col-xs-12 col-sm-5 col-md-4">
                <a data-toggle="lightbox" data-title="<?php $_($product->name, 'name'); ?>"
                   href="/products_pictures/<?php $_($product->big_picture, 'picture'); ?>">
                    <img class="img-responsive product-image img-thumbnail" src="/products_pictures/<?php $_($product->picture, 'picture'); ?>" alt="">
                </a>
            </div>
            <div class="hidden-xs col-sm-2 col-md-1">
                <!-- Additional pictures -->
            </div>
            <div class="col-xs-12 col-sm-5 col-md-7">
                <!-- START CONTENT ITEM -->
                <div class="well">
                    <div class="row">
                        <div class="col-xs-6 col-sm-5 col-md-7">
                            <?php if (count($options) > 0) { ?>
                            <div class="option-variants">
                                <?php foreach ($options as $variant) { ?>
                                    <strong><?= $variant->parentOption->name; ?>:</strong> <span><?= $variant->name; ?></span>
                                    <br>

                                <?php } ?>
                            </div>
                            <?php } ?>
                            <div class="ratings product-item-ratings">
                                <p class="pull-right"><?= $product->customer_votes ?> reviews</p>

                                <p>
                                    <?php include($common_path . "rating_stars.php") ?>
                                    <?php $_($product->customers_rating, 'customers_rating') ?> stars
                                </p>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-7 col-md-5">
                            <span class="label label-important price">$<?= $product->Price ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <h3>Description</h3>

                            <p><?php $_($product->description, 'description'); ?></p>

                            <?php include __DIR__ . '/_wishlist_button.php'; ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-12 col-md-3">
                            <a class="btn btn-block btn-default" href="/category/view?id=<?=$product->categoryID;?>" id="hw-back-btn"><span class="glyphicon glyphicon-chevron-left"></span>Back</a>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <form id="cart_form" action="/cart/add" method="post" class="form-horizontal" role="form">
                                <div class="form-group">
                                    <label for="count"
                                           class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">Count</label>

                                    <div class="col-xs-12 col-sm-9 col-md-9 col-lg-10">
                                        <div class="text-right">
                                            <input type="hidden" name="product_id" value="<?= $product->productID ?>">
                                            <select class="form-control" id="qty" name="qty">
                                                <option>1</option>
                                                <option>2</option>
                                                <option>3</option>
                                                <option>4</option>
                                                <option>5</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-xs-12 col-md-3">
                            <a class="btn btn-block btn-primary ladda-button" id="add_to_cart" href="#" data-style="expand-right"
                               data-size="xs" data-spinner-size="16"><span class="glyphicon glyphicon-shopping-cart"></span> Add to cart</a>
                        </div>
                    </div>
                </div>
                <!-- END CONTENT ITEM -->
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <!-- START CONTENT ITEM -->
                <div class="tabbable">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#offers" data-toggle="tab">Special Offers</a></li>
                        <li class=""><a href="#bestsell" data-toggle="tab">Best selling products</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="row tab-pane offers-tab-pane active" id="offers">
                            <?php include __DIR__ . '/small_productlist.php'; ?>
                        </div>
                        <div class="row tab-pane bestsell-tab-pane" id="bestsell">
                            <?php include __DIR__ . '/big_productlist.php'; ?>
                        </div>
                    </div>
                </div>
                <!-- END CONTENT ITEM -->
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="well">
                    <div class="text-right">
                        <?php
                        include($common_path . "review_form.php")
                        ?>
                        <button class="btn btn-success" data-toggle="modal" data-target="#reviewForm">Leave a Review
                        </button>
                    </div>
                    <?php foreach ($product->getReviews() as $review) { ?>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <?php for ($i = 1; $i < 6; $i++) {
                                    if ($i > $review->rating) {
                                        ?>
                                        <span class="glyphicon glyphicon-star-empty"></span>
                                    <?php } else { ?>
                                        <span class="glyphicon glyphicon-star"></span>
                                    <?php
                                    }
                                }
                                $_($review->username, 'username'); ?>
                                <span class="pull-right"><?php echo $review->getDateLabel(); ?></span>

                                <p><?php $_($review->review, 'review'); ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
