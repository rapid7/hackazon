<div class="panel panel-default product-page">
        <div class="panel-heading">
            <a href="/admin/<?php $_(strtolower($modelName)); ?>">&larr; Return to list</a>
        </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <div class="col-xs-6 col-md-6">
            <?php
            /** @var \App\Admin\FieldFormatter $formatter */
            $formatter->renderForm();
            ?>
        </div>
        <div class="col-xs-6 col-md-6">
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
    <!-- /.panel-body -->
</div>

