    <!-- Page Content -->

    <div class="container account-page">

        <div class="row">

            <div class="col-lg-12">
                <h1 class="page-header">My Account</h1>
                <ol class="breadcrumb">
                    <li><a href="index.html">Home</a>
                    </li>
                    <li class="active">My Account</li>
                </ol>
            </div>

        </div>
        <!-- /.row -->

        <!-- Service Tabs -->

        <div class="row">

            <div class="col-lg-12">
                <?php if ($success = $this->pixie->session->flash('success')): ?>
                    <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
                <?php endif; ?>

                <ul id="myTab" class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#my-orders" data-toggle="tab">My Latest Orders</a></li>
                    <li><a href="#profile" data-toggle="tab">Profile</a></li>
                </ul>
                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade in active latest-orders" id="my-orders">
                        <?php include __DIR__.'/_order_list.php'; ?>
                        <p class="text-right">
                            <a href="/account/orders">Go to my orders &rarr;</a>
                        </p>
                    </div>
                    <div class="tab-pane fade profile-show" id="profile">
                        <?php include __DIR__ . '/_profile_info.php'; ?>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

       <?php /*
        <!-- Service Paragraphs -->

        <div class="row">

            <div class="col-md-8">
                <h2 class="page-header">Our Premium Services</h2>
                <p>Lid est laborum dolo rumes fugats untras. Etharums ser quidem rerum facilis dolores nemis omnis fugats vitaes nemo minima rerums unsers sadips amets. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
            </div>

            <div class="col-md-4">
                <h2 class="page-header">Something More</h2>
                <p>Etharums ser quidem rerum facilis dolores nemis omnis fugats vitaes nemo minima rerums unsers sadips amets.</p>
                <a class="btn btn-primary" href="#">Click Me!</a>
            </div>

        </div>
        <!-- /.row -->

        <!-- Service Images -->

        <div class="row">

            <div class="col-lg-12">
                <h2 class="page-header">Service Images</h2>
            </div>

            <div class="col-sm-4">
                <img class="img-responsive" src="http://placehold.it/750x450">
                <h3>Service One</h3>
                <p>Service one description. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc placerat diam quis nisl vestibulum dignissim. In hac habitasse platea dictumst.</p>
                <a class="btn btn-link btn-sm pull-right">More <i class="fa fa-angle-right"></i></a>
            </div>

            <div class="col-sm-4">
                <img class="img-responsive" src="http://placehold.it/750x450">
                <h3>Service Two</h3>
                <p>Service two description. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc placerat diam quis nisl vestibulum dignissim. In hac habitasse platea dictumst.</p>
                <a class="btn btn-link btn-sm pull-right">More <i class="fa fa-angle-right"></i></a>
            </div>

            <div class="col-sm-4">
                <img class="img-responsive" src="http://placehold.it/750x450">
                <h3>Service Three</h3>
                <p>Service three description. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc placerat diam quis nisl vestibulum dignissim. In hac habitasse platea dictumst.</p>
                <a class="btn btn-link btn-sm pull-right">More <i class="fa fa-angle-right"></i></a>
            </div>

        </div>
        <!-- /.row -->
         */ ?>
    </div>
    <!-- /.container -->