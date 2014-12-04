<div class="container order-page">

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <h1 class="page-header">Order №<?php echo $order->increment_id; ?> <small><?php echo $_order_status($order->status); ?></small></h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/account<?php echo $useRest ? '#!' : '#my-orders'; ?>">My Account</a></li>
                <li><a href="/account<?php echo $useRest ? '#!orders' : '/orders'; ?>">Orders</a></li>
                <li class="active">Order №<?php echo $order->increment_id; ?></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Overview</h3>
                </div>
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt>Date</dt>
                        <dd><?php echo date('m/d/Y', strtotime($order->created_at)); ?></dd>

                        <dt>Status</dt>
                        <dd><?php echo $_order_status($order->status); ?></dd>
                        <?php if ($order->discount > 0): ?>
                            <dt>Discount</dt>
                            <dd><?php echo $_($order->discount); ?>%</dd>
                        <?php endif; ?>
                        <dt>Total</dt>
                        <dd><span class="label label-danger">$<?php echo $order->orderItems->getItemsTotal(); ?></span></dd>
                    </dl>
                </div>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th colspan="2">Items</th>
                        <th width="50">count</th>
                        <th width="70">total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orderItems) : ?>
                        <?php foreach ($orderItems as $item) : ?>
                            <?php $item->product->find(); ?>
                            <tr>
                                <td class="product-image">
                                    <div class="img-thumbnail-wrapper">
                                        <a href="/product/view?id=<?php echo $item->product->id(); ?>"><img src="/products_pictures/<?php $_($item->product->picture); ?>" alt=""/></a>
                                    </div>
                                </td>
                                <td><a href="/product/view?id=<?php echo $item->product_id; ?>"><?php echo $item->name ?></a></td>
                                <td align="center"><?php echo $item->qty ?></td>
                                <td align="right">$<?php echo $item->price * $item->qty ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr>
                        <th colspan="4">Services</th>
                    </tr>
                    <tr class="info">
                        <td colspan="2">Shipping: <?php echo $order->shipping_method; ?></td>
                        <td align="right" colspan="2">$0</td>
                    </tr>
                    <tr class="info">
                        <td colspan="2">Payment: <?php echo $order->payment_method; ?></td>
                        <td align="right" colspan="2">$0</td>
                    </tr>
                    <tr class="danger">
                        <td align="right" colspan="4"><strong>$<?php echo $order->orderItems->getItemsTotal(); ?></strong></td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>