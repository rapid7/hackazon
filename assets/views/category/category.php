<div class="row">

    <div class="col-lg-12">
        <h1 class="page-header"><?=$pageTitle?></h1>
        <ol class="breadcrumb">
            <li><a href="/">Home</a></li>
            <li class="active">Product Item</li>
        </ol>
    </div>

</div>

<div class="section">
    <div class="container">
        <div class="row">

            <div class="col-md-12">

                <div class="row carousel-holder">

                    <div class="col-md-12">
                        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                            </ol>
                            <div class="carousel-inner">
                                <div class="item active">
                                    <img class="slide-image" src="http://placehold.it/800x300">
                                </div>
                                <div class="item">
                                    <img class="slide-image" src="http://placehold.it/800x300">
                                </div>
                                <div class="item">
                                    <img class="slide-image" src="http://placehold.it/800x300">
                                </div>
                            </div>
                            <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                            </a>
                            <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right"></span>
                            </a>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <?php if($productPage === true) include("_product_items.php");
                    else include("_category_items.php");
                    ?>

                </div>

                <div class="row">
                    <div class="col-sm-4 col-lg-4 col-md-offset-4">
                        <h4><a href="#">Like this template?</a></h4>
                        <p>If you like this template, then check out <a target="_blank" href="http://maxoffsky.com/code-blog/laravel-shop-tutorial-1-building-a-review-system/">this tutorial</a> on how to build a working review system for your online store!</p>
                        <a class="btn btn-primary" target="_blank" href="http://maxoffsky.com/code-blog/laravel-shop-tutorial-1-building-a-review-system/">View Tutorial</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

