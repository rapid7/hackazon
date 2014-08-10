<script>
    $(function () {
        $("#place_order").click(function(){
            $.ajax({
                url:'/checkout/placeOrder',
                type:"POST",
                dataType:"json",
                success: function(data) {
                    window.location.href = "/checkout/order";
                },
                fail: function() {
                    alert( "error" );
                }
            });
        });
    });
</script>
<?php include __DIR__ . '/cart_header.php'; ?>

<div class="tab-pane active" id="step4">
    <div class="row">

        <div class="col-xs-12 col-sm-5">

            <div class="well">

                <table class="table">
                    <thead>
                    <tr>
                        <th>Peronsal info</th>
                    </tr>
                    <tr>
                        <td>
                            <div class="address_confirmation">
                            <?php $shippingAddress = $cart->getShippingAddress()?>
                                <h3>Shipping Address</h3>
                                <b><?php echo $shippingAddress->full_name ?></b><br />
                                <?php echo $shippingAddress->address_line_1 ?><br />
                                <?php echo $shippingAddress->address_line_2 ?><br />
                                <?php echo $shippingAddress->city . ' ' . $shippingAddress->region . ' ' . $shippingAddress->zip ?><br />
                                <?php echo $shippingAddress->country_id ?><br />
                                <?php echo $shippingAddress->phone ?><br />
                            </div>
                            <div class="address_confirmation">
                                <?php $billingAddress = $cart->getBillingAddress()?>
                                <h3>Billing Address</h3>
                                <b><?php echo $billingAddress->full_name ?></b><br />
                            <?php echo $billingAddress->address_line_1 ?><br />
                            <?php echo $billingAddress->address_line_2 ?><br />
                            <?php echo $billingAddress->city . ' ' . $billingAddress->region . ' ' . $billingAddress->zip ?><br />
                            <?php echo $billingAddress->country_id ?><br />
                            <?php echo $billingAddress->phone ?><br />
                                </div>
                        </td>
                    </tr>
                    </thead>
                </table>

            </div>

        </div>

        <div class="col-xs-12 col-sm-7">
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
                $items = $cart->getCartItemsModel()->getAllItems();
                foreach ($items as $item) :?>
                <tr>
                    <td><?php echo $item->name ?></td>
                    <td align="center"><?php echo $item->qty ?></td>
                    <td align="right">€ <?php echo $item->price * $item->qty ?>,-</td>
                </tr>
                <?php endforeach;?>
                <tr>
                    <td>Shipping: <?php echo $cart->shipping_method;?></td>
                    <td align="right" colspan="2">€ 0,-</td>
                </tr>
                <tr>
                    <td>Payment: <?php echo $cart->payment_method;?></td>
                    <td align="right" colspan="2">€ 0,-</td>
                </tr>
                <tr>
                    <td align="right" colspan="3"><strong>€ <?php echo $cart->getCartItemsModel()->getItemsTotal();?>,-</strong></td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-6">
            <button class="btn btn-default" data-target="#step2" data-toggle="tab" onclick="window.location.href='/checkout/billing'"><span class="glyphicon glyphicon-chevron-left"></span> billing step</button>
        </div>
        <div class="col-xs-6">
            <button class="btn btn-primary pull-right" data-target="#step4" data-toggle="tab" id="place_order">Place Order <span class="glyphicon glyphicon-chevron-right"></span></button>
        </div>
    </div>

</div>

<?php include __DIR__ . '/cart_footer.php'; ?>
