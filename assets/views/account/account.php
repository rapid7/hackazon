    <!-- Page Content -->
    <div class="container account-page">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">My Account</h1>
                <ol class="breadcrumb">
                    <li><a href="/">Home</a>
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
                            <button id="form-submit" type="submit" class="btn btn-primary ladda-button" data-style="expand-right"><span class="ladda-label">Go to my orders</span></button>
                        </p>
                    </div>
                    <div class="tab-pane fade profile-show" id="profile">
                        <?php include __DIR__ . '/_profile_info.php'; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
    
<script>
    $(function() {
        Ladda.bind( 'input[type=submit]' );
        
        $('#form-submit').on('click', function(e) {
            var l = Ladda.create(document.querySelector( '#form-submit' ));
            l.start();
            window.location.href = "/account/orders";
            return false; // Will stop the submission of the form
        });
    });
</script>