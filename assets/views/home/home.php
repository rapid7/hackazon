    <div id="myCarousel" class="carousel slide">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner">
            <div class="item active">
                <div class="fill" style="background-image:url('/images/attackstory.jpg');"></div>
                <div class="carousel-caption">
                    <h1>Modern Business - tell us your story.</h1>
                </div>
            </div>
            <div class="item">
                <div class="fill" style="background-image:url('/images/dance.png');"></div>
                <div class="carousel-caption">
                    <h1>Did all right thing?</h1>
                </div>
            </div>
            <div class="item">
                <div class="fill" style="background-image:url('/images/spiderman.jpg');"></div>
                <div class="carousel-caption">
                    <h1>Is your system protected?</a>
                    </h1>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <span class="icon-next"></span>
        </a>
    </div>

    <div class="section">
        <!--<div class="section-colored text-center">-->
            <div class="container">

                <div class="row well">
                    <div class="col-lg-12">
                        <div class="col-lg-4 col-md-4">
                            <h3><i class="fa fa-pencil"></i><a href="user/register"> Register on the site</a></h3>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <h3><i class="fa fa-thumbs-up"></i> Get the Best Price</h3>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <h3><i class="fa fa-shopping-cart"></i> By with pleasure</h3>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        <!--</div>-->
        <!-- /.container -->
    </div>


    <!-- /.section-colored -->
    <div class="col-lg-12 text-center">
        <h2>Special Offers for You</h2>
        <hr>
    </div>

    <div class="section">
        <div class="container">
            <div class="row">
                <?php include("special_offers.php")?>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.section -->

    <div class="section-colored">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>What Other Customers Are Looking At Right Now</h2>
                    <hr>
                </div>
                <?php include("rnd_product.php")?>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.section-colored -->


    <div class="section">
        <div class="container">

            <div class="row well">
                <div class="col-lg-8 col-md-8">
                    <h4>Sign up for mailing list and get the best products and best price!</h4>
                </div>
                <div class="col-lg-4 col-md-4">
                    <a class="btn btn-lg btn-primary pull-right" href="http://startbootstrap.com">Sign up</a>
                </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->
    </div>

