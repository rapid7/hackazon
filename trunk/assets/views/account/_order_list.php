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
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Order №</th>
                <th>Date</th>
                <th>Payment Method</th>
                <th>Shipping Method</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($myOrders as $order) : ?>
            <tr>
                <td><a href="/account/orders/<?php $_($order->increment_id);?>"><?php $_($order->increment_id);?></a></td>
                <td><?php echo date('m/d/Y', strtotime($order->created_at));?></td>
                <td><?php $_($order->payment_method);?> </td>
                <td><?php $_($order->shipping_method);?> </td>
                <td><?php echo $_order_status($order->status);?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php $_pager($pager, '/account/orders/?page=#page#'); ?>
    </div>
</div>