<script>
    function update_qty(itemId, qty)
    {
        var priceItem = parseInt($("#row_span_item_" + itemId).html());
        var priceTotal = priceItem * qty;
        $("#row_span_total_" + itemId).empty().append(priceTotal);

        $.ajax({
            url:'/cart/update',
            type:"POST",
            data: {qty: qty, itemId: itemId},
            dataType:"json",
            success: function(data){
                if (qty <= 0) {
                    $("#tr_item_" + itemId).remove();
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
        $("#step1_next").click(function() {
            var el = $(this),
                l = el.ladda();
            el.attr('disabled', 'disabled');
            l.ladda('start');

            $.ajax({
                url:'/cart/setMethods',
                type:"POST",
                data: $("#methods, #methods2").serialize(),
                dataType:"json",
                timeout: 10000,
                success: function(data) {
                    <?php if (is_null($this->pixie->auth->user())): ?>
                    window.location.href = "<?php echo '/user/login?return_url=' . rawurlencode('/checkout/shipping');?>"
                    <?php else : ?>
window.location.href = "/checkout/shipping";
                    <?php endif;?>
                },
                fail: function() {
                    alert( "error" );
                    l.ladda('stop');
                    el.removeAttr('disabled');
                }
            });
        })
    });

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
            <h1>Your cart is empty</h1>
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
                    foreach ($items as $item):?>
                    <?php
                    $product = $item->getProduct();
                    ?>
                    <tr class="tr_items" id="tr_item_<?php echo $item->id?>">
<td class="hidden-xs"><img class="img-responsive img-home-portfolio" src="/products_pictures/<?=$product['picture']?>" alt="photo <?=$product['name']?>"></td>
                        <td>
                            <h4><a href="/product/view/<?=$product['productID']?>"><?=$product['name']?></a></h4>
                        </td>
                        <td>

                            <div class="input-group hw-count-control">
								<span class="input-group-btn">
									<button data-id="<?php echo $item->id?>" class="btn btn-default minus_btn" type="button">
									<span class="glyphicon glyphicon-minus">
									</span></button>
									</span>
									<input type="text" id="input_<?php echo $item->id?>" onchange="update_qty(<?php echo $item->id?>, this.value)" class=" form-control" value="<?php echo $item->qty?>">
								<span class="input-group-btn">
									<button data-id="<?php echo $item->id?>" class="btn btn-default plus_btn" type="button">
										<span class="glyphicon glyphicon-plus">
									</span></button>
								</span>
                            </div>

                        </td>
                        <td align="right">
                            <span class="hw-total">$ <span id="row_span_item_<?php echo $item->id?>"><?php echo $item->price?></span>,- </span>
                        </td>
                        <td align="right">
                            <span class="hw-total">$ <span id="row_span_total_<?php echo $item->id?>"><?php echo $item->price*$item->qty?></span>,- </span>
                        </td>
                    </tr>
                    <?php endforeach;?>
					</tbody>
					<tfoot>
                    <tr>
                        <td class="text-right" colspan="3">
                            <form class="form-horizontal" id="methods" class="methods">
                                <?php $_token('checkout_step_1'); ?>
                                <fieldset>
                                    <div class="form-group">
                                        <label class="col-xs-4 col-xs-offset-4 control-label">Shipping: </label>
                                        <div class="col-xs-4">
                                            <select class="form-control" name="shipping_method">
                                                <option value="mail">Mail</option>
                                                <option value="collect">Collect</option>
                                                <option value="express">Express</option>
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
                    <tr>
                        <td class="text-right" colspan="3">
							<form class="form-horizontal" id="methods2" class="methods">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="col-xs-4 col-xs-offset-4 control-label">Payment: </label>
                                        <div class="col-xs-4">
                                            <select class="form-control" name="payment_method">
                                                <option value="wire transfer">Wire Transfer</option>
                                                <option value="paypal">Paypal</option>
                                                <option value="creditcard">Credit Card</option>
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
                    <tr>
                        <th class="text-right" colspan="3">Quantity items:<br>Total: </th>
                        <th class="text-left" colspan="2">
                            $ <span id="items_qty"><?=$itemQty?></span>,-<br>
                            $ <span id="total_price"><?=$totalPrice?>,-<br>
                        </th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-8">
                <a href="/" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Continue shopping</a>
                
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