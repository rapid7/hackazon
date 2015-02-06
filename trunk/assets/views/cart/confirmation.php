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
                    if (data.location) {
                        window.location.href = data.location;
                    } else if (data.success) {
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
                            <?php //$shippingAddress = $cart->getShippingAddress()?>
                                <h3>Shipping Address</h3>
                                <b><?php echo $_($shippingAddress->getWrapperOrValue('full_name')); ?></b><br />
                                <?php echo $_($shippingAddress->getWrapperOrValue('address_line_1')); ?><br />
                                <?php echo $_($shippingAddress->getWrapperOrValue('address_line_2')); ?><br />
                                <?php echo $_($shippingAddress->getWrapperOrValue('city')) . ' ' . $_($shippingAddress->getWrapperOrValue('region')) . ' ' . $_($shippingAddress->getWrapperOrValue('zip')); ?><br />
                                <?php echo $_($shippingAddress->getWrapperOrValue('country_id')); ?><br />
                                <?php echo $_($shippingAddress->getWrapperOrValue('phone')); ?><br />
                            </div>
                            <div class="blockShadow bg-info">
                                <?php //$billingAddress = $cart->getBillingAddress()?>
                                <h3>Billing Address</h3>
                                <b><?php echo $_($billingAddress->getWrapperOrValue('full_name')); ?></b><br />
                            <?php echo $_($billingAddress->getWrapperOrValue('address_line_1')); ?><br />
                            <?php echo $_($billingAddress->getWrapperOrValue('address_line_2')); ?><br />
                            <?php echo $_($billingAddress->getWrapperOrValue('city')) . ' ' . $_($billingAddress->getWrapperOrValue('region')) . ' ' . $_($billingAddress->getWrapperOrValue('zip')); ?><br />
                            <?php echo $_($billingAddress->getWrapperOrValue('country_id')); ?><br />
                            <?php echo $_($billingAddress->getWrapperOrValue('phone')); ?><br />
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
                /** @var \App\Model\CartItems $item */
                foreach ($items as $item) :?>
                    <?php $product = $item->getItemProduct(); ?>
	<tr>
                    <td class="product-image"><a href="/product/view?id=<?php echo $product->id();?>"><img class="img-thumbnail img-rounded" src="/products_pictures/<?php $_($product->picture); ?>" alt=""/></a></td>
                    <td><?php echo $item->name ?></td>
                    <td class="text-center"><?php echo $item->qty ?></td>
                    <td class="text-right">$<?php echo $item->price * $item->qty ?></td>
                </tr>
                <?php endforeach;?>
                </tbody>
				
                <tfoot>
                <tr class="info">
                    <td colspan="2">Shipping: <?php $_($cart->getWrapperOrValue('shipping_method')); ?></td>
                    <td align="right" colspan="2">$0</td>
                </tr>
                <tr class="info">
                    <td colspan="2">Payment: <?php $_($cart->getWrapperOrValue('payment_method'));?></td>
                    <td align="right" colspan="2">$0</td>
                </tr>
                <?php if ($discount): ?>
                    <tr class="info">
                        <td colspan="2">Discount: </td>
                        <td align="right" colspan="2"><?php echo $discount; ?>%</td>
                    </tr>
                <?php endif; ?>
                <tr class="danger">
                    <td align="right" colspan="4"><strong>$<?php echo $totalPrice;?></strong></td>
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
