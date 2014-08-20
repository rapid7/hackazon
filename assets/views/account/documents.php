<div class="container">

    <div class="row">
        <div class="col-md-9 col-sm-8">
            <h1 class="page-header">My Documents</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a>
                </li>
                <li class="active">My Documents</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
        <?php
        if (count($myOrders) == 0) {
            echo '<h2>Orders not found</h2>';
        }
        foreach($myOrders as $order) : ?>
            <div class="row blockShadow">
                <div class="col-xs-12 col-sm-5">
                        <div class="blockShadow">
                            <?php $shippingAddress = $order->orderAddress->getShippingAddress()?>
                            <?php if ($shippingAddress) : ?>
                            <h3>Shipping Address</h3>
                            <b><?php echo $shippingAddress->full_name ?></b><br />
                            <?php echo $shippingAddress->address_line_1 ?><br />
                            <?php echo $shippingAddress->address_line_2 ?><br />
                            <?php echo $shippingAddress->city . ' ' . $shippingAddress->region . ' ' . $shippingAddress->zip ?><br />
                            <?php echo $shippingAddress->country_id ?><br />
                            <?php echo $shippingAddress->phone ?><br />
                            <?php endif; ?>
                        </div>
                        <div class="blockShadow">
                            <?php $billingAddress = $order->orderAddress->getBillingAddress()?>
                            <?php if ($billingAddress) : ?>
                            <h3>Billing Address</h3>
                            <b><?php echo $billingAddress->full_name ?></b><br />
                            <?php echo $billingAddress->address_line_1 ?><br />
                            <?php echo $billingAddress->address_line_2 ?><br />
                            <?php echo $billingAddress->city . ' ' . $billingAddress->region . ' ' . $billingAddress->zip ?><br />
                            <?php echo $billingAddress->country_id ?><br />
                            <?php echo $billingAddress->phone ?><br />
                            <?php endif; ?>
                                    </div>
                </div>
                <div class="col-xs-12 col-sm-7">
                    <h1>ORDER ID: <?php echo $order->increment_id?></h1>
                    <h3>
                        Status: <strong><?php echo $order->status;?></strong>
                    </h3>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Overview</th>
                            <th width="50">count</th>
                            <th width="70">total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = $order->orderItems->find_all()->as_array();
                        if ($items) : ?>
                        <?php foreach ($items as $item) :?>
                            <tr>
                                <td><a href="/product/view/<?php echo $item->product_id;?>"><?php echo $item->name ?></a></td>
                                <td align="center"><?php echo $item->qty ?></td>
                                <td align="right">$ <?php echo $item->price * $item->qty ?>,-</td>
                            </tr>
                        <?php endforeach;?>
                        <?php endif;?>
                        <tr>
                            <td>Shipping: <?php echo $order->shipping_method;?></td>
                            <td align="right" colspan="2">$ 0,-</td>
                        </tr>
                        <tr>
                            <td>Payment: <?php echo $order->payment_method;?></td>
                            <td align="right" colspan="2">$ 0,-</td>
                        </tr>
                        <tr>
                            <td align="right" colspan="3"><strong>$ <?php echo $order->orderItems->getItemsTotal();?>,-</strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>

</div>