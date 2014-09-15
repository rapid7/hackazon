<?php
/**
 * @var \App\Model\Product[] $special_offers
 * @var \App\Model\Product[] $otherCustomersProducts
 * @var array $topProductBlocks
 */
?>


    <div class="section home-top-section">
        <!--<div class="section-colored text-center">-->
        <div class="container well">
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-4 col-md-4">
                        <h3><i class="fa fa-pencil"></i><a href="user/register"> Register on the site</a></h3>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <h3><i class="fa fa-thumbs-up"></i> Get the Best Price</h3>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <h3><i class="fa fa-shopping-cart"></i> Buy with pleasure</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
        <!--</div>-->
        <!-- /.container -->
    </div>

    <div class="section home-top-product-section">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-8">

                    <!-- START CONTENT ITEM -->
                    <div class="row">
                        <div class="col-xs-9">
                            <h2>Special selection</h2>
                        </div>
                        <?php include __DIR__ . '/social_links.php'; ?>
                    </div>
                    <!-- END CONTENT ITEM -->

                    <!-- START CONTENT ITEM -->
                    <div class="row product-list-inline-small">
                        <?php /** @var \App\Model\SpecialOffers $specOffer */ ?>
                        <?php foreach ($special_offers as $specOffer): ?>
                            <?php $product = $specOffer->product_offers; ?>
                            <?php if ($product->loaded()):?>
                                <?php include __DIR__ . '/product_item.php'; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <!-- END CONTENT ITEM -->

                    <!-- START CONTENT ITEM -->
                    <?php include __DIR__ . '/top_reviews.php'; ?>
                    <!-- END CONTENT ITEM -->

                </div>

                <div class="hidden-xs col-sm-4 col-md-4 col-lg-4">
                    <br>
                    <?php // Output right blocks of links ?>
                    <?php foreach ($topProductBlocks as $productBlock): ?>
                        <?php include __DIR__ . '/top_products_block.php'; ?>
                    <?php endforeach; ?>

                    <!-- START CONTENT ITEM -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="slider-wrapper theme-light">
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
                    <!-- END CONTENT ITEM -->

                </div>
            </div>
        </div>
    </div>

<?php
// Output product sections
/** @var array $productSections */
foreach ($productSections as $sectionData) {
    if (count($sectionData['products'])) {
        include("product_section.php");
    }
}
?>

    <div class="section-colored">

        <?php
        $sectionData = array('title' => 'What Other Customers Are Looking At Right Now', 'products' => $otherCustomersProducts);
        include(__DIR__ . "/product_section.php");
        ?>

    </div>
    <!-- /.section-colored -->


    <div class="section">
        <div class="container">

            <div class="row well">
                <div class="col-lg-8 col-md-8">
                    <h4>Sign up for mailing list and get the best products and best price!</h4>
                </div>
                <div class="col-lg-4 col-md-4">
                    <a class="btn btn-lg btn-primary pull-right" href="/user/login">Sign up</a>
                </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->
    </div>
<?php include __DIR__ . '/category_list.php'; ?>