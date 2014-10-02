<div class="container">

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <h1 class="page-header">My Orders</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/account<?php echo $useRest ? '#!' : '#my-orders'; ?>">My Account</a></li>
                <li class="active">My Orders</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <?php include __DIR__.'/_order_list.php'; ?>
        </div>
    </div>
</div>