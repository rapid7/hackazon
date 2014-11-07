<script>
    function update_qty(productId, qty)
    {
        var priceItem = parseInt($("#row_span_item_" + productId).html());
        var priceTotal = priceItem * qty;
        $("#row_span_total_" + productId).empty().append(priceTotal);

        $.ajax({
            url:'/cart/update',
            type:"POST",
            data: {qty: qty, productId: productId},
            dataType:"json",
            success: function(data){
                if (qty <= 0) {
                    $("#tr_item_" + productId).remove();
                }
                $("#items_qty").html(data.items_qty);
                $("#total_price").html(data.total_price);
                $("#checkout-alert-info").empty().append('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Your cart has been updated</strong></div>');
                setTimeout('$(".alert-info").alert("close");', 3000);

            },
            fail: function() {
                alert( "error" );
            }
        });
    }

    $(function () {
        var cardBlock = $('.js-credit-card-block'),
            paymentMethodField = $('select[name="payment_method"]');
        cardBlock.hzBootstrapValidator();

        var toggleCreditCardBlock = function () {
            if (paymentMethodField.val() == 'creditcard') {
                cardBlock.show();
                cardBlock.find('input, select, textarea').removeAttr('disabled');
            } else {
                cardBlock.hide();
                cardBlock.find('input, select, textarea').attr('disabled', 'disabled');
            }
        };

        toggleCreditCardBlock();
        paymentMethodField.on('change', toggleCreditCardBlock);

        $("#empty_cart").click(function(){
            var el = $(this),
                l = el.ladda();
            el.attr('disabled', 'disabled');
            l.ladda('start');

            $.ajax({
                url:'/cart/empty',
                type:"POST",
                dataType:"json",
                success: function(data){
                    $(".tr_items").remove();
                    $("#items_qty").html(0);
                    $("#total_price").html(0);
                    $("#checkout-alert-info").empty().append('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Your cart has been deleted</strong></div>');
                    l.ladda('stop');
                    setTimeout(function () {
                        $(".alert-info").alert("close");
                        location.reload();
                    }, 3000);
                },
                fail: function() {
                    alert( "error" );
                    l.ladda('stop');
                    el.removeAttr('disabled');
                }
            });
        });
        $(".close").click(function(){
            $(".alert-info").alert('close');
        });
        $(".plus_btn").click(function() {
            var qty = parseInt($('#input_' + $(this).attr('data-id')).val()) + 1;
            $('#input_' + $(this).attr('data-id')).val(qty);
            update_qty($(this).attr('data-id'), qty);
        });
        $(".minus_btn").click(function() {
            var qty = parseInt($('#input_' + $(this).attr('data-id')).val()) - 1;
            if (qty < 0) return;
            $('#input_' + $(this).attr('data-id')).val(qty);
            update_qty($(this).attr('data-id'), qty);
        });
        $("#step1_next").click(function(ev) {
            if (cardBlock.is(':visible')) {
                cardBlock.hzBootstrapValidator('validate');
                var validator = cardBlock.data('bootstrapValidator');
                if (!validator.isValid()) {
                    ev.preventDefault();
                    return;
                }
            }

            var el = $(this),
                l = el.ladda();
            el.attr('disabled', 'disabled');
            l.ladda('start');
            var dataFields = $("#methods, #methods2");
            if (cardBlock.is(':visible')) {
                dataFields = dataFields.add(cardBlock.find('input, select, textarea'));
            }

            $.ajax({
                url:'/cart/setMethods',
                type:"POST",
                data: dataFields.serialize(),
                timeout: 10000,
                success: function(data) {
                    <?php /* if (is_null($this->pixie->auth->user())): ?>
                        window.location.href = "<?php echo '/user/login?return_url=' . rawurlencode('/checkout/shipping');?>"
                    <?php else :*/ ?>
                        window.location.href = "/checkout/shipping";
                    <?php //endif;?>
                },
                fail: function() {
                    alert( "error" );
                    l.ladda('stop');
                    el.removeAttr('disabled');
                }
            });
        });

        $('#useCouponLink').on('click', function (ev) {
            ev.preventDefault();
            getCouponWidget().useCoupon($('#couponField').val());
        });

        $('#clearCouponLink').on('click', function (ev) {
            ev.preventDefault();
            getCouponWidget().unsetCoupon();
        });
    });

    function successCouponCallback(result) {
        location.reload();
    }

    function successCouponUnsetCallback(result) {
        location.reload();
    }

    function invalidCouponCallback(error) {
        var el = $('#couponField').tooltip('destroy').tooltip({
            title : 'Wrong coupon',
            delay: 3000
        }).tooltip('show');
        setTimeout(function () { el.tooltip('hide'); }, 2000);

//        console.log("Error: ");
//        console.log(error);
    }

    function getCouponWidget() {
        return getFlashMovie('coupon_as');
    }
</script>

<?php
if (count($items) == 0) :?>
    <div class="container">

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <h1 class="page-header">Shopping Cart</h1>
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li class="active">My Cart</li>
            </ol>
            <div id="checkout-alert-info"></div>
            <div class="text-center"><h1 class="">Your cart is empty</h1></div>
        </div>
    </div>
    </div>
<?php return; endif;?>
<?php include __DIR__ . '/cart_header.php'; ?>
    <div class="tab-pane active" id="step1">
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-order">
                    <thead>
						<tr>
							<th class="hidden-xs">&nbsp;</th>
							<th class="th-title"></th>
							<th class="th-count">count</th>
							<th class="th-each">each (incl. tax)</th>
							<th class="th-total">total</th>
						</tr>
                    </thead>
                    <tbody>
                    <?php
                    /** @var \App\Model\CartItems $item */
                    foreach ($items as $item):?>
                    <?php
                    $product = $item->getProduct();
                    $itemProduct = $item->getItemProduct();
                    ?>
                    <tr class="tr_items" id="tr_item_<?php echo $itemProduct->id()?>">
						<td class="hidden-xs text-center">
							<div class="img-thumbnail-wrapper">
								<img class="img-responsive img-home-portfolio " src="/products_pictures/<?=$product['picture']?>" alt="photo <?=$product['name']?>"></td>
							</div>
                        <td>
                            <h4><a href="/product/view?id=<?=$product['productID']?>"><?=$product['name']?></a></h4>
                        </td>
                        <td>

                            <div class="input-group hw-count-control">
								<span class="input-group-btn">
									<button data-id="<?php echo $itemProduct->id()?>" class="btn btn-default minus_btn" type="button">
									<span class="glyphicon glyphicon-minus">
									</span></button>
									</span>
									<input type="text" id="input_<?php echo $itemProduct->id()?>" onchange="update_qty(<?php echo $itemProduct->id()?>, this.value)" class=" form-control" value="<?php echo $item->qty; ?>">
								<span class="input-group-btn">
									<button data-id="<?php echo $itemProduct->id()?>" class="btn btn-default plus_btn" type="button">
										<span class="glyphicon glyphicon-plus">
									</span></button>
								</span>
                            </div>

                        </td>
                        <td class="text-left">
                            <span class="hw-total">$ <span id="row_span_item_<?php echo $itemProduct->id()?>"><?php echo $item->price?></span>,- </span>
                        </td>
                        <td align="right">
                            <span class="hw-total">$ <span id="row_span_total_<?php echo $itemProduct->id()?>"><?php echo $item->price*$item->qty?></span>,- </span>
                        </td>
                    </tr>
                    <?php endforeach;?>
					</tbody>
					<tfoot>
                    <tr class="info">
                        <td class="text-right" colspan="3">
                            <form class="form-horizontal" id="methods" class="methods">
                                <?php $_token('checkout_step_1'); ?>
                                <fieldset>
                                    <div class="form-group">
                                        <label class="col-xs-4 col-xs-offset-4 control-label">Shipping: </label>
                                        <div class="col-xs-4">
                                            <select class="form-control" name="shipping_method">
                                                <?php $shippingMethods = [
                                                    'mail' => 'Mail',
                                                    'collect' => 'Collect',
                                                    'express' => 'Express'
                                                ]; ?>
                                                <?php foreach($shippingMethods as $sMethod => $sMethodName): ?>
                                                    <option value="<?php $_($sMethod); ?>" <?php if ($shippingMethod == $sMethod):
                                                        ?>selected<?php endif; ?>><?php $_($sMethodName); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </fieldset>
							</form>
                        </td>
                        <td class="text-center" colspan="2">
                            <span class="label label-success">FREE</span>
                        </td>
                    </tr>
                    <tr class="info">
                        <td class="text-right" colspan="3">
							<form class="form-horizontal" id="methods2" class="methods">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="col-xs-4 col-xs-offset-4 control-label">Payment: </label>
                                        <div class="col-xs-4">
                                            <select class="form-control" name="payment_method">
                                                <?php $paymentMethods = [
                                                    'wire transfer' => 'Wire Transfer',
                                                    'paypal' => 'Paypal',
                                                    'creditcard' => 'Credit Card'
                                                ]; ?>
                                                <?php foreach($paymentMethods as $pMethod => $pMethodName): ?>
                                                    <option value="<?php $_($pMethod); ?>" <?php if ($paymentMethod == $pMethod):
                                                        ?>selected<?php endif; ?>><?php $_($pMethodName); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </td>
                        <td class="text-center" colspan="2">
                            <span class="label label-success">FREE</span>
                        </td>
                    </tr>

                    <tr class="info">
                        <td class="text-right" colspan="3">
                            <div class="js-credit-card-block credit-card-block">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-xs-4 col-xs-offset-4 control-label">Card number: </label>
                                        <div class="col-xs-4">
                                            <input type="text" name="credit_card_number" id="creditCardField"
                                                   value="<?php $_($creditCardNumber); ?>" class="form-control"
                                                   required pattern="^[\d-]+$" /> <!--data-bv-creditcard="true"-->
                                        </div>
                                    </div>
                                </div>

                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-xs-4 col-xs-offset-4 control-label">Expires: </label>
                                        <div class="col-xs-4 form-horizontal expires-fields">
                                            <select name="credit_card_month" id="creditCardMonthField" class="form-control" required>
                                                <?php for ($month = 1; $month <= 12; $month++): ?>
                                                    <option value="<?php $_($month); ?>"
                                                        <?php if ($month == $creditCardMonth): ?>selected="selected" <?php endif; ?>
                                                        ><?php $_(sprintf("%02d", $month)); ?></option>
                                                <?php endfor; ?>
                                            </select>

                                            <select name="credit_card_year" id="creditCardYearField" class="form-control" required >
                                                <?php for ($year = (int) date('Y'), $lastYear = $year + 10; $year <= $lastYear; $year++): ?>
                                                    <option value="<?php $_($year); ?>"
                                                        <?php if ($year == $creditCardYear): ?>selected="selected" <?php endif; ?>
                                                        ><?php $_($year); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-xs-4 col-xs-offset-4 control-label">CVV: </label>
                                        <div class="col-xs-4">
                                            <input type="text" name="credit_card_cvv" id="creditCardCVVField"
                                                   value="<?php $_($creditCardCVV); ?>" class="form-control"
                                                   required data-bv-cvv="true" /> <!--data-bv-cvv-ccfield="credit_card_number"-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td colspan="2" class="text-center"></td>
                    </tr>

                    <tr class="info">
                        <td class="text-right" colspan="3">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-xs-4 col-xs-offset-4 control-label">Use coupon: </label>
                                    <div class="col-xs-4">
                                        <input type="text" name="coupon" id="couponField" value="<?php $_($coupon ? $coupon->coupon : ''); ?>" class="form-control" />
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td colspan="2" class="text-center">
                            <div class="form-horizontal">
                                <a href="#" class="btn btn-primary" id="useCouponLink">Use</a>
                            </div>
                            <?php include __DIR__.'/_coupon_flash_widget.php'; ?>
                        </td>
                    </tr>
                    <tr class="info" id="currentCouponRow" style="<?php if (!$coupon): ?>display: none;<?php endif; ?>">
                        <td class="text-right" colspan="3">
                            <label>Your coupon is &laquo;<span id="currentCoupon"><?php $_($coupon->coupon); ?></span>&raquo;, and you have a discount:
                                <span id="currentCouponDiscount"><?php echo $coupon->discount ?: 0; ?></span>%</label>
                        </td>
                        <td colspan="2" class="text-center">
                            <div class="form-horizontal">
                                <a href="#" class="btn btn-warning" id="clearCouponLink">Clear coupon</a>
                            </div>
                        </td>
                    </tr>

                    <tr class="danger">
                        <th class="text-right" colspan="3">Quantity items:<br>Total: </th>
                        <th class="text-left" colspan="2">
                            <span id="items_qty"><?=$itemQty?></span><br>
                            $ <span id="total_price"><?=$totalPrice?>,-<br>
                        </th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-8">
                <a href="/" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Continue Shopping</a>
                
				<button class="btn btn-danger ladda-button" id="empty_cart" data-style="expand-right" data-spinner-color="#999999"
                    ><span class="ladda-label"><span class="glyphicon glyphicon-trash"></span> Empty cart</span></button>
            </div>
            <div class="col-xs-4">
                <button class="btn btn-primary pull-right ladda-button" data-target="#step2" data-toggle="tab" id="step1_next"
                        data-style="expand-left"><span class="ladda-label">Next <span class="glyphicon glyphicon-chevron-right"></span></span></button>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/cart_footer.php';