<?php
if (count($myOrders) == 0) {
    if ($pager->num_items == 0) {
        echo '<h2>You don\'t have any orders.</h2>';
    } else {
        echo '<h2>Incorrect page.</h2>';
    }
}
?>
<div class="row">
        <div class="col-xs-12">
            <?php if (count($myOrders) > 0): ?>
            <table class="table">
                <tr>
                    <th>Order №</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Shipping Method</th>
                    <th>Status</th>
                </tr>
                <?php foreach($myOrders as $order) : ?>
                <tr>
                    <td><a href="/account/orders/<?php $_($order->increment_id);?>"><?php $_($order->increment_id);?></a></td>
                    <td><?php echo date('m/d/Y', strtotime($order->created_at));?></td>
                    <td><?php $_($order->payment_method);?> </td>
                    <td><?php $_($order->shipping_method);?> </td>
                    <td><?php echo $_order_status($order->status);?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>

            <?php $_pager($pager, '/account/orders/?page=#page#'); ?>
        </div>
</div>

<?php return; /*
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
<?php endforeach; ?>        */