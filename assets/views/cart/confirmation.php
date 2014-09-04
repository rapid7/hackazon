<script>
    $(function () {
        $("#place_order").click(function(){
            var el = $(this),
                l = el.ladda();
            el.attr('disabled', 'disabled');
            l.ladda('start');

            $.ajax({
                url:'/checkout/placeOrder',
                data: { _csrf_checkout_step4: el.data('token') },
                type:"POST",
                dataType:"json",
                success: function(data) {
                    if (data.success) {
                        window.location.href = "/checkout/order";
                    } else {
                        alert( "error" );
                    }
                },
                fail: function() {
                    l.ladda('start');
                    el.removeAttr('disabled');
                    alert( "error" );
                }
            });
        });
    });
</script>
<?php include __DIR__ . '/cart_header.php'; ?>

<div class="tab-pane active checkout-page" id="step4">
    <div class="row">

        <div class="col-xs-12 col-sm-4">

            <div class="well bg-info">

                <table class="table">
                    <thead>
                    <tr>
                        <th>Personal info</th>
                    </tr>
                    <tr>
                        <td>
                            <div class="blockShadow bg-info">
                            <?php $shippingAddress = $cart->getShippingAddress()?>
                                <h3>Shipping Address</h3>
                                <b><?php echo $_($shippingAddress->full_name, 'full_name'); ?></b><br />
                                <?php echo $_($shippingAddress->address_line_1, 'address_line_1'); ?><br />
                                <?php echo $_($shippingAddress->address_line_2, 'address_line_2'); ?><br />
                                <?php echo $_($shippingAddress->city, 'city') . ' ' . $_($shippingAddress->region, 'region') . ' ' . $_($shippingAddress->zip, 'zip'); ?><br />
                                <?php echo $_($shippingAddress->country_id, 'country_id'); ?><br />
                                <?php echo $_($shippingAddress->phone, 'phone'); ?><br />
                            </div>
                            <div class="blockShadow bg-info">
                                <?php $billingAddress = $cart->getBillingAddress()?>
                                <h3>Billing Address</h3>
                                <b><?php echo $_($billingAddress->full_name, 'full_name'); ?></b><br />
                            <?php echo $_($billingAddress->address_line_1, 'address_line_1'); ?><br />
                            <?php echo $_($billingAddress->address_line_2, 'address_line_2'); ?><br />
                            <?php echo $_($billingAddress->city, 'city') . ' ' . $_($billingAddress->region, 'region') . ' ' . $_($billingAddress->zip, 'zip'); ?><br />
                            <?php echo $_($billingAddress->country_id, 'country_id'); ?><br />
                            <?php echo $_($billingAddress->phone, 'phone'); ?><br />
                                </div>
                        </td>
                    </tr>
                    </thead>
                </table>

            </div>

        </div>
        <div class="col-xs-12 col-sm-8">
            <table class="table cart-overview">
                <thead>
                <tr>
                    <th colspan="2">Overview</th>
                    <th width="50">Count</th>
                    <th width="70">Total</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $items = $cart->getCartItemsModel()->getAllItems();
                foreach ($items as $item) :?>
                    <?php $item->product->find(); ?>
	<tr>
                    <td class="product-image"><a href="/product/view/<?php echo $item->product->id();?>"><img class="img-thumbnail img-rounded" src="/products_pictures/<?php $_($item->product->picture); ?>" alt=""/></a></td>
                    <td><?php echo $item->name ?></td>
                    <td align="center"><?php echo $item->qty ?></td>
                    <td align="right">$<?php echo $item->price * $item->qty ?></td>
                </tr>
                <?php endforeach;?>
                </tbody>
				
                <tfoot>
                <tr class="info">
                    <td colspan="2">Shipping: <?php echo $cart->shipping_method;?></td>
                    <td align="right" colspan="2">$0</td>
                </tr>
                <tr class="info">
                    <td colspan="2">Payment: <?php echo $cart->payment_method;?></td>
                    <td align="right" colspan="2">$0</td>
                </tr>
                <tr class="danger">
                    <td align="right" colspan="4"><strong>$<?php echo $cart->getCartItemsModel()->getItemsTotal();?></strong></td>
                </tr>
                <tfoot>
            </table>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-6">
            <button class="btn btn-default" data-target="#step2" data-toggle="tab" onclick="window.location.href='/checkout/billing'"><span class="glyphicon glyphicon-chevron-left"></span> Billing Step</button>
        </div>
        <div class="col-xs-6">
            <button class="btn btn-primary pull-right ladda-button" data-target="#step4" data-toggle="tab" id="place_order" data-token="<?php echo $this->getToken('checkout_step4'); ?>" data-style="expand-left">Place Order <span class="glyphicon glyphicon-chevron-right"></span></button>
        </div>
    </div>

</div>

<?php include __DIR__ . '/cart_footer.php'; ?>
